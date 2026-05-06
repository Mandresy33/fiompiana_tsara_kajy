<?php

function getConnection(): PDO {
    $host     = 'localhost';
    $port     = '5432';
    $dbname   = 'fiompiana_tsara_kajy';
    $user     = 'mandresy';
    $password = 'adminMilay123';

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}