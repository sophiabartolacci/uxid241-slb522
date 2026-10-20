<?php
declare(strict_types=1);

$config = require_once 'config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect(
        $config['db_host'], 
        $config['db_user'], 
        $config['db_pass'], 
        $config['db_name'], 
        (int) $config['db_port']
    );
    mysqli_set_charset($conn, $config['db_charset']);

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Connection failed: ' . $e->getMessage());
}

return $conn;

?>