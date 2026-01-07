<?php

namespace App\Services;

use App\Models\Formula;
use App\Models\Progress;
use Illuminate\Support\Facades\Log;

class FormulaEngine
{
    /**
     * Evaluate a formula with given context
     */
    public function evaluate(Formula $formula, array $context, $userId, $materialId)
    {
        try {
            // Build full context with database queries if needed
            $fullContext = $this->buildContext($formula, $context, $userId, $materialId);
            
            // Parse and evaluate expression
            $result = $this->evaluateExpression($formula->expression, $fullContext);
            
            // Cast to return type
            return $this->castResult($result, $formula->return_type);
            
        } catch (\Exception $e) {
            Log::error("Formula evaluation failed: {$formula->key}", [
                'error' => $e->getMessage(),
                'expression' => $formula->expression,
                'context' => $context
            ]);
            return null;
        }
    }
    
    /**
     * Build context by fetching required data from database
     */
    private function buildContext(Formula $formula, array $context, $userId, $materialId)
    {
        $fullContext = $context;
        
        // Add database-driven values based on scope
        if ($formula->scope === 'material') {
            // Get current level from context or default
            $currentLevel = $context['current_level'] ?? null;
            
            $query = Progress::where('user_id', $userId)
                ->where('material_id', $materialId);
            
            // If we have a current level, filter by it
            if ($currentLevel) {
                $query->whereJsonContains('attributes->current_level', $currentLevel);
            }
            
            $fullContext['correct_count'] = (clone $query)->where('is_correct', true)->count();
            $fullContext['total_count'] = (clone $query)->count();
            $fullContext['wrong_count'] = $fullContext['total_count'] - $fullContext['correct_count'];
        } 
        elseif ($formula->scope === 'global') {
            // Global scope - all materials
            $fullContext['correct_count'] = Progress::where('user_id', $userId)
                ->where('is_correct', true)
                ->count();
            $fullContext['total_count'] = Progress::where('user_id', $userId)->count();
            $fullContext['wrong_count'] = $fullContext['total_count'] - $fullContext['correct_count'];
        }
        
        return $fullContext;
    }
    
    /**
     * Evaluate expression safely
     */
    private function evaluateExpression(string $expression, array $context)
    {
        // Replace variables with values
        $expr = $expression;
        foreach ($context as $key => $value) {
            // Handle numeric values
            if (is_numeric($value)) {
                $expr = preg_replace('/\b' . preg_quote($key, '/') . '\b/', $value, $expr);
            } else {
                // Handle string values (wrap in quotes)
                $expr = preg_replace('/\b' . preg_quote($key, '/') . '\b/', "'" . addslashes($value) . "'", $expr);
            }
        }
        
        // Replace functions with PHP equivalents
        $expr = $this->replaceFunctions($expr);
        
        // Safely evaluate
        return $this->safeEval($expr);
    }
    
    /**
     * Replace custom functions with PHP code
     */
    private function replaceFunctions(string $expr)
    {
        // PERCENTAGE(a, b) -> (a / b) * 100
        $expr = preg_replace_callback('/PERCENTAGE\(([^,]+),\s*([^)]+)\)/', function($matches) {
            return "({$matches[1]} > 0 && {$matches[2]} > 0 ? (({$matches[1]}) / ({$matches[2]})) * 100 : 0)";
        }, $expr);
        
        // IF(condition, true_value, false_value) -> (condition ? true_value : false_value)
        $expr = preg_replace('/IF\(([^,]+),\s*([^,]+),\s*([^)]+)\)/', '(($1) ? ($2) : ($3))', $expr);
        
        // ROUND(value, decimals) -> round(value, decimals)
        $expr = preg_replace('/ROUND\(([^,]+),\s*([^)]+)\)/', 'round($1, $2)', $expr);
        
        // ROUND(value) with single argument -> round(value, 0)
        $expr = preg_replace('/ROUND\(([^)]+)\)/', 'round($1, 0)', $expr);
        
        // ABS(value) -> abs(value)
        $expr = preg_replace('/ABS\(([^)]+)\)/', 'abs($1)', $expr);
        
        // MIN(a, b) -> min(a, b)
        $expr = preg_replace('/MIN\(([^,]+),\s*([^)]+)\)/', 'min($1, $2)', $expr);
        
        // MAX(a, b) -> max(a, b)
        $expr = preg_replace('/MAX\(([^,]+),\s*([^)]+)\)/', 'max($1, $2)', $expr);
        
        return $expr;
    }
    
    /**
     * Safely evaluate expression
     */
    private function safeEval(string $expr)
    {
        // Validate expression is safe
        if (!$this->isExpressionSafe($expr)) {
            throw new \Exception("Unsafe expression detected: {$expr}");
        }
        
        // Suppress errors and use try-catch
        try {
            $result = @eval("return {$expr};");
            return $result;
        } catch (\Throwable $e) {
            throw new \Exception("Expression evaluation failed: " . $e->getMessage());
        }
    }
    
    /**
     * Validate expression is safe (basic security check)
     */
    private function isExpressionSafe(string $expr)
    {
        // Block dangerous functions and keywords
        $dangerous = [
            'exec', 'system', 'passthru', 'shell_exec', 'popen', 'proc_open',
            'file', 'fopen', 'include', 'require', 'eval', 'assert',
            'unlink', 'rmdir', 'mkdir', 'chmod', 'chown',
            'file_get_contents', 'file_put_contents', 'curl_exec',
            '__', 'call_user_func', 'create_function'
        ];
        
        foreach ($dangerous as $func) {
            if (stripos($expr, $func) !== false) {
                Log::warning("Dangerous function detected in expression", [
                    'function' => $func,
                    'expression' => $expr
                ]);
                return false;
            }
        }
        
        // Check for variable variables or other PHP tricks
        if (preg_match('/\$\$|\$\{|\$_/', $expr)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Cast result to specified type
     */
    private function castResult($value, string $type)
    {
        return match($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => (bool) $value,
            'string' => (string) $value,
            default => $value
        };
    }
}
