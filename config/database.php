<?php
$host = 'localhost';
$db   = 'assessment_paytab_db';
$user = 'root'; // or whatever your DB username is
$pass = '';     // your DB password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // show errors, please
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch as assoc array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // real prepared statements
];

try {
   $pdo = new PDO($dsn, $user, $pass, $options);


} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
