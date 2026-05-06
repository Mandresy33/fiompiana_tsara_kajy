<?php

session_start();

if (empty($_SESSION['user_id']) || $_SESSION['user_account_type'] !== 'farmer') {
    header('Location: index.php');
    exit;
}

require_once 'src/UserRepository.php';

$repo = new UserRepository();
$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$editSimulation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $simulationId = isset($_POST['simulation_id']) ? (int)$_POST['simulation_id'] : 0;
    $data = [
        'wording' => trim($_POST['wording'] ?? ''),
        'estimatedTotalCost' => trim($_POST['estimatedTotalCost'] ?? ''),
        'dailyCost' => trim($_POST['dailyCost'] ?? ''),
        'estimatedIncome' => trim($_POST['estimatedIncome'] ?? ''),
        'estimatedProfit' => trim($_POST['estimatedProfit'] ?? ''),
        'optimalSaleDate' => trim($_POST['optimalSaleDate'] ?? ''),
        'estimatedWeight' => trim($_POST['estimatedWeight'] ?? ''),
        'simulationDate' => trim($_POST['simulationDate'] ?? date('Y-m-d')),
    ];

    if ($action === 'delete' && $simulationId > 0) {
        if ($repo->deleteSimulation($simulationId, $userId)) {
            $success = 'Simulation deleted successfully.';
        } else {
            $error = 'Unable to delete simulation.';
        }
    } elseif ($action === 'save') {
        if (empty($data['wording']) || !is_numeric($data['estimatedTotalCost']) || !is_numeric($data['dailyCost']) || !is_numeric($data['estimatedIncome']) || !is_numeric($data['estimatedProfit']) || !is_numeric($data['optimalSaleDate']) || !is_numeric($data['estimatedWeight'])) {
            $error = 'Please fill all fields correctly.';
        } else {
            if ($simulationId > 0) {
                if ($repo->updateSimulation($simulationId, $userId, $data)) {
                    $success = 'Simulation updated successfully.';
                } else {
                    $error = 'Unable to update simulation.';
                }
            } else {
                if ($repo->createSimulation($userId, $data)) {
                    $success = 'Simulation created successfully.';
                } else {
                    $error = 'Unable to create simulation.';
                }
            }
        }
    }

    if ($success && empty($error)) {
        header('Location: dashboard.php');
        exit;
    }
}

if (isset($_GET['edit'])) {
    $editSimulation = $repo->getSimulationById((int)$_GET['edit'], $userId);
}

$simulations = $repo->getSimulationsByUser($userId);
require 'views/dashboard.php';
