<?php

namespace App\Services\User\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Shared CSV import logic for User and Student services.
 *
 * Classes using this trait must provide a `createUserFromRow(array $rowData): void` method.
 */
trait ImportsCsvUsers
{
    /**
     * Parse and import users from a CSV file.
     *
     * @param callable(array): void $createCallback Receives validated row data ['name','email','password']
     * @return array{success_count: int, error_rows: array}
     */
    protected function importUsersFromCsv(UploadedFile $file, callable $createCallback): array
    {
        $path         = $file->getRealPath();
        $successCount = 0;
        $errorRows    = [];

        if (($handle = fopen($path, 'r')) === false) {
            return ['success_count' => 0, 'error_rows' => []];
        }

        $header          = fgetcsv($handle, 1000, ',');
        $requiredColumns = ['name', 'email', 'password'];
        $missingColumns  = array_diff($requiredColumns, $header);

        if (! empty($missingColumns)) {
            fclose($handle);
            throw new \RuntimeException(
                'File tidak memiliki kolom yang diperlukan: ' . implode(', ', $missingColumns),
            );
        }

        $nameIndex     = array_search('name', $header);
        $emailIndex    = array_search('email', $header);
        $passwordIndex = array_search('password', $header);
        $rowNumber     = 1;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;

            if (empty($row[$nameIndex]) && empty($row[$emailIndex])) {
                continue;
            }

            $rowData = [
                'name'     => $row[$nameIndex]     ?? '',
                'email'    => $row[$emailIndex]    ?? '',
                'password' => $row[$passwordIndex] ?? '',
            ];

            $validator = Validator::make($rowData, [
                'name'     => 'required|string|max:255',
                'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $errorRows[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];
                continue;
            }

            try {
                $createCallback($rowData);
                $successCount++;
            } catch (\Exception $e) {
                $errorRows[] = ['row' => $rowNumber, 'errors' => [$e->getMessage()]];
            }
        }

        fclose($handle);

        return ['success_count' => $successCount, 'error_rows' => $errorRows];
    }

    /**
     * Generate a CSV import template.
     *
     * @return array{headers: array, callback: callable}
     */
    protected function generateCsvTemplate(string $filename, array $exampleRow): array
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($exampleRow) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, $exampleRow);
            fclose($file);
        };

        return ['headers' => $headers, 'callback' => $callback];
    }
}
