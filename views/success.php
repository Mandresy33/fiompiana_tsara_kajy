<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Created</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="card">
        <h2>Account created!</h2>
        <p>Welcome, <strong><?= htmlspecialchars($firstname) ?> <?= htmlspecialchars($name) ?></strong>!</p>
        <p>You registered as: <strong><?= htmlspecialchars($account_type) ?></strong></p>
        <?php if ($account_type === 'farmer' && !empty($land_area)): ?>
            <p>Land Area: <strong><?= htmlspecialchars($land_area) ?> hectares</strong></p>
            <p>Years of Experience: <strong><?= htmlspecialchars($year_of_experience) ?></strong></p>
        <?php elseif ($account_type === 'client' && !empty($client_type)): ?>
            <p>Client Type: <strong><?= htmlspecialchars($client_type) ?></strong></p>
            <?php if (!empty($preference)): ?>
                <p>Preferences: <strong><?= htmlspecialchars($preference) ?></strong></p>
            <?php endif; ?>
        <?php endif; ?>
        <p><a href="index.php">Back to register</a></p>
    </div>
</body>
</html>