<?php

session_start();
require_once 'src/UserRepository.php';

$repo = new UserRepository();
$error = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = $repo->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_firstname'] = $user['firstname'];
            $_SESSION['user_account_type'] = $user['account_type'];

            if ($user['account_type'] === 'farmer') {
                header('Location: dashboard.php');
                exit;
            }

            $name = $user['name'];
            $firstname = $user['firstname'];
            $account_type = $user['account_type'];
            $land_area = $year_of_experience = '';
            $client_type = $preference = '';

            require 'views/success.php';
            exit;
        }
    }
}

require 'views/login.php';