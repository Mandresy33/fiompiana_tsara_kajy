<?php

require_once 'src/UserRepository.php';

$repo  = new UserRepository();
$error = '';

// Initialize variables
$name = $firstname = $email = $password = $account_type = $localisation = $language = '';
$land_area = $year_of_experience = $client_type = $preference = '';
$phones = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name         = trim($_POST['name']         ?? '');
    $firstname    = trim($_POST['firstname']    ?? '');
    $email        = trim($_POST['email']        ?? '');
    $password     = trim($_POST['password']     ?? '');
    $account_type = trim($_POST['account_type'] ?? '');
    $localisation = trim($_POST['localisation'] ?? '');
    $language     = trim($_POST['language']     ?? 'en');

    // Additional fields
    $land_area          = trim($_POST['land_area']          ?? '');
    $year_of_experience = trim($_POST['year_of_experience'] ?? '');
    $client_type        = trim($_POST['client_type']        ?? '');
    $preference         = trim($_POST['preference']         ?? '');
    $phones             = array_filter(array_map('trim', $_POST['phones'] ?? []), fn($phone) => $phone !== '');

    // Validation
    if (empty($name) || empty($firstname) || empty($email) || empty($password) || empty($account_type)) {
        $error = "All required fields must be filled.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";

    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";

    } elseif ($repo->emailExists($email)) {
        $error = "This email is already registered.";

    } elseif ($account_type === 'farmer' && (empty($land_area) || !is_numeric($land_area) || empty($year_of_experience) || !is_numeric($year_of_experience))) {
        $error = "For farmers, land area (numeric) and years of experience (numeric) are required.";

    } elseif (empty($phones)) {
        $error = "At least one phone number is required.";

    } elseif ($account_type === 'client' && empty($client_type)) {
        $error = "For clients, client type is required.";

    } else {
        $success = $repo->createUserWithDetails(
            $name, $firstname, $email, $password, $account_type, $localisation, $language,
            (float)$land_area, (int)$year_of_experience, $client_type, $preference,
            $phones
        );
        if ($success) {
            require 'views/success.php';
            exit;
        } else {
            $error = "Failed to create account. Please try again.";
        }
    }
}

require 'views/register.php';
