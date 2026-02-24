<?php
// $DB_HOST = 'sql309.infinityfree.com';
// $DB_NAME = 'if0_40031431_nutri_db';
// $DB_USER = 'if0_40031431';
// $DB_PASS = 'NutriLab2025X';
// $DB_CHARSET = 'utf8mb4';

// Set Philippine timezone (GLOBAL for the app)
// date_default_timezone_set('Asia/Manila');

// $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
// $options = [
//     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
//     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//     PDO::ATTR_EMULATE_PREPARES   => false,
// ];

// try {
//     $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
// } catch (PDOException $e) {
//     exit('Database connection failed: ' . $e->getMessage());
// }
// 

$DB_HOST = 'localhost';   
$DB_NAME = 'nutri_db';      
$DB_USER = 'root';        
$DB_PASS = '';           
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
?> 
