# PHPCS Skill

## Overview

PHPCS (PHP CodeSniffer) adalah tool untuk memastikan kode PHP mengikuti standar coding. Project ini menggunakan **PSR-12** (PHP Standard Recommendations).

## Quick Commands

```bash
# Check violations
composer phpcs

# Auto-fix violations
composer phpcs:fix

# Check specific folder
composer phpcs app/Http/Controllers

# Auto-fix specific folder
composer phpcs:fix app/Http/Controllers
```

## Configuration

File konfigurasi: `phpcs.xml` di root project.

### Standard Configuration (Recommended)

```xml
<?xml version="1.0"?>
<ruleset name="Oopedia">
    <description>PSR12 coding standard for Oopedia</description>

    <!-- Base standard -->
    <rule ref="PSR12"/>

    <!-- Exclude false positive with strict_types -->
    <rule ref="PSR1.Files.SideEffects.FoundWithSymbols">
        <severity>0</severity>
    </rule>

    <!-- Scan app directory -->
    <file>app/</file>

    <!-- Exclude paths -->
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/node_modules/*</exclude-pattern>
    <exclude-pattern>*/storage/*</exclude-pattern>
</ruleset>
```

## Composer Scripts

Tambahkan di `composer.json`:

```json
{
    "scripts": {
        "phpcs": "phpcs",
        "phpcs:fix": "phpcbf"
    },
    "config": {
        "process-timeout": 1800
    }
}
```

## Common PSR-12 Rules

| Rule               | Description                                        | Example                         |
| ------------------ | -------------------------------------------------- | ------------------------------- |
| **Line Length**    | Max 120 characters                                 | Pecah string panjang dengan `.` |
| **Indentation**    | 4 spaces                                           | -                               |
| **Braces**         | Opening brace di baris yang sama                   | `function foo() {`              |
| **Use Statements** | Alphabetical, multi-line                           | Satu use per line               |
| **Methods**        | camelCase, one parameter per line jika > 120 chars | -                               |

## Common Errors & Fixes

### 1. Line Length Exceeded

```php
// ❌ Bad (> 120 chars)
$state['message'] = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS sebagai Object-Oriented Architect.';

// ✅ Good (concatenation)
$state['message'] = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS '
    . 'sebagai Object-Oriented Architect.';
```

### 2. Constructor with Many Parameters

```php
// ❌ Bad
public function __construct(protected ServiceA $a, protected ServiceB $b, protected ServiceC $c) {}

// ✅ Good
public function __construct(
    protected ServiceA $a,
    protected ServiceB $b,
    protected ServiceC $c,
) {
}
```

### 3. Function with Many Parameters

```php
// ❌ Bad (> 120 chars)
public function doSomething(string $a, string $b, string $c, string $d): void {

// ✅ Good
public function doSomething(
    string $a,
    string $b,
    string $c,
    string $d,
): void {
```

### 4. Missing Parentheses on new Class

```php
// ❌ Bad
return new Collection;

// ✅ Good
return new Collection();
```

### 5. Array Syntax (PSR-12 allows both)

```php
// Both are OK in PSR-12
$arr = ['a', 'b'];
$arr = ['key' => 'value'];

// But if using [] triggers errors, use array()
$arr = array('a', 'b');
```

### 6. strict_types Declaration

```php
// ✅ Good - prevents PSR1.Files.SideEffects warning
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class MyController {
    // ...
}
```

### 7. Ternary/Arrow Functions

```php
// ❌ Bad (> 120 chars)
$result = $collection->where('status', 'active')->map(fn($item) => $item->name)->filter(fn($name) => strlen($name) > 3)->values();

// ✅ Good
$result = $collection
    ->where('status', 'active')
    ->map(fn($item) => $item->name)
    ->filter(fn($name) => strlen($name) > 3)
    ->values();
```

## Workflow

### Step 1: Check Current State

```bash
composer phpcs
```

### Step 2: Auto-Fix (if safe)

```bash
composer phpcs:fix
```

### Step 3: Manual Fix (for complex cases)

Gunakan editor untuk memperbaiki:

1. Line length violations
2. Complex array/closure formatting
3. Custom logic errors

### Step 4: Verify

```bash
composer phpcs
```

Pastikan output: `0 ERRORS`

## PSR Standards Reference

| PSR        | Description                                             |
| ---------- | ------------------------------------------------------- |
| **PSR-1**  | Basic Coding Standard (naming, file structure)          |
| **PSR-2**  | Coding Style (deprecated, merged to PSR-12)             |
| **PSR-4**  | Autoloading Standard                                    |
| **PSR-12** | Extended Coding Style (indentation, braces, namespaces) |

## Strict vs Relaxed Config

### Relaxed (Recommended for mixed codebases)

```xml
<rule ref="PSR12"/>
<rule ref="PSR1.Files.SideEffects.FoundWithSymbols">
    <severity>0</severity>
</rule>
```

### Strict (Requires strict_types everywhere)

```xml
<rule ref="PSR12"/>
<rule ref="Generic">
    <exclude name="Generic.PHP.RequireStrictTypes"/>
</rule>
```

## Troubleshooting

### Error: "Filename doesn't match expected"

Filename harus lowercase semua. Atau exclude di config:

```xml
<rule ref="PSR1.Classes.ClassDeclaration"/>
```

### Error: "Tabs must be used"

Jika codebase pakai spaces, exclude:

```xml
<rule ref="Generic.WhiteSpace.DisallowTabIndent">
    <severity>0</severity>
</rule>
```

### Error: "Missing strict_types declaration"

Tambahkan di awal file:

```php
<?php

declare(strict_types=1);

namespace App\...
```

### Timeout Error

Tambahkan di `composer.json`:

```json
"config": {
    "process-timeout": 1800
}
```

## Tips

1. **Incremental Fixes**: Fix per folder, bukan seluruh codebase sekaligus
2. **Git Stash**: Stash changes sebelum auto-fix besar-besaran
3. **Review Changes**: Selalu review hasil `phpcbf` sebelum commit
4. **CI Integration**: Gunakan `composer phpcs` di CI pipeline
5. **Pre-commit Hook**: Consider adding phpcs ke pre-commit hook
