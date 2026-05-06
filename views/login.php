<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <label>Email</label>
            <input type="email" name="email" maxlength="100"
                value="<?= htmlspecialchars($email) ?>" required />

            <label>Password</label>
            <input type="password" name="password" minlength="6" required />

            <button type="submit">Login</button>
        </form>

        <p class="info-link">
            Don't have an account yet? <a href="register.php">Create one here</a>.
        </p>
    </div>
</body>
</html>
