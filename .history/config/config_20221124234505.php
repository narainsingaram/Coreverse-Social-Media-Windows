<?php

ob_start(); //Turns on output buffering

session_start();

//Development
//$host = '127.0.0.1';
// $db = 'attendance_db';
// $user = 'root';
// $pass = '';
// $charset= 'utf8mb4';

//remote 
$host = 'remotemysql.com';
    $db = 'NC1HPsPMvA';
    $user = 'NC1HPsPMvA';
    $pass = 'rqUDdNcsXH';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    try{
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch(PDOException $e) {
        throw new PDOException($e->getMessage());
    }



?> 