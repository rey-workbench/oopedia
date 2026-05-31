<?php
$tables = DB::select('SHOW TABLES');
$tableKey = array_keys(get_object_vars($tables[0]))[0];

$results = [];
foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    if (in_array($tableName, ['migrations', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'])) continue;
    
    $columns = DB::select('SHOW COLUMNS FROM ' . $tableName);
    foreach ($columns as $column) {
        $colName = $column->Field;
        if (in_array($colName, ['id', 'created_at', 'updated_at', 'deleted_at'])) continue;
        
        $output = [];
        $cmd = 'grep -r "' . $colName . '" app/ resources/js/ 2>NUL | find /V "check_columns" /C';
        exec($cmd, $output);
        $count = (int)($output[0] ?? 0);
        
        if ($count == 0) {
            $results['unused'][$tableName][] = $colName;
        } else if ($count <= 2) {
            $results['low_usage'][$tableName][$colName] = $count;
        }
    }
}
echo json_encode($results, JSON_PRETTY_PRINT);
