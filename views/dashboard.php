<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleForm() {
            const formBox = document.getElementById('simulation-form-box');
            formBox.style.display = formBox.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="topbar">
        <div>
            <h2>Farmer Dashboard</h2>
            <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_name']) ?></strong></p>
        </div>
        <a class="button secondary" href="register.php">Create account</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="topbar no-space">
            <h3>Simulate a Project</h3>
            <button class="button" type="button" onclick="toggleForm()">Toggle form</button>
        </div>
        <div id="simulation-form-box" class="toggle-box <?= $editSimulation ? 'open' : 'closed' ?>">
            <form method="POST" action="dashboard.php">
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="simulation_id" value="<?= htmlspecialchars($editSimulation['id'] ?? '') ?>" />

                <label>Project title</label>
                <input type="text" name="wording" value="<?= htmlspecialchars($editSimulation['wording'] ?? '') ?>" required />

                <div class="grid">
                    <div>
                        <label>Estimated total cost</label>
                        <input type="number" step="0.01" name="estimatedTotalCost" value="<?= htmlspecialchars($editSimulation['estimatedTotalCost'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Daily cost</label>
                        <input type="number" step="0.01" name="dailyCost" value="<?= htmlspecialchars($editSimulation['dailyCost'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Estimated income</label>
                        <input type="number" step="0.01" name="estimatedIncome" value="<?= htmlspecialchars($editSimulation['estimatedIncome'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Estimated profit</label>
                        <input type="number" step="0.01" name="estimatedProfit" value="<?= htmlspecialchars($editSimulation['estimatedProfit'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Optimal sale date (days)</label>
                        <input type="number" name="optimalSaleDate" value="<?= htmlspecialchars($editSimulation['optimalSaleDate'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Estimated weight</label>
                        <input type="number" name="estimatedWeight" value="<?= htmlspecialchars($editSimulation['estimatedWeight'] ?? '') ?>" required />
                    </div>
                    <div>
                        <label>Simulation date</label>
                        <input type="date" name="simulationDate" value="<?= htmlspecialchars($editSimulation['simulationDate'] ?? date('Y-m-d')) ?>" />
                    </div>
                </div>

                <button class="button spaced" type="submit"><?= $editSimulation ? 'Update' : 'Create' ?> simulation</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h3>Your simulations</h3>
        <?php if (empty($simulations)): ?>
            <p>No simulations yet. Create one using the form above.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Total cost</th>
                        <th>Income</th>
                        <th>Profit</th>
                        <th>Sale days</th>
                        <th>Weight</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($simulations as $simulation): ?>
                        <tr>
                            <td><?= htmlspecialchars($simulation['id']) ?></td>
                            <td><?= htmlspecialchars($simulation['wording']) ?></td>
                            <td><?= htmlspecialchars($simulation['estimatedTotalCost']) ?></td>
                            <td><?= htmlspecialchars($simulation['estimatedIncome']) ?></td>
                            <td><?= htmlspecialchars($simulation['estimatedProfit']) ?></td>
                            <td><?= htmlspecialchars($simulation['optimalSaleDate']) ?></td>
                            <td><?= htmlspecialchars($simulation['estimatedWeight']) ?></td>
                            <td><?= htmlspecialchars($simulation['simulationDate']) ?></td>
                            <td class="actions">
                                <a class="button secondary" href="dashboard.php?edit=<?= htmlspecialchars($simulation['id']) ?>">Edit</a>
                                <form method="POST" action="dashboard.php" onsubmit="return confirm('Supprimer cette simulation ?');">
                                    <input type="hidden" name="action" value="delete" />
                                    <input type="hidden" name="simulation_id" value="<?= htmlspecialchars($simulation['id']) ?>" />
                                    <button class="button danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
