<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleAdditionalFields() {
            const accountType = document.querySelector('select[name="account_type"]').value;
            const farmerFields = document.getElementById('farmer-fields');
            const clientFields = document.getElementById('client-fields');
            
            farmerFields.style.display = (accountType === 'farmer') ? 'block' : 'none';
            clientFields.style.display = (accountType === 'client') ? 'block' : 'none';
        }
        
        function removePhoneField(button) {
            const entry = button.closest('.phone-entry');
            if (entry) {
                entry.remove();
            }
        }

        function addPhoneField() {
            const container = document.getElementById('phone-fields');
            const entry = document.createElement('div');
            entry.className = 'phone-entry';
            entry.innerHTML = `
                <input type="text" name="phones[]" maxlength="30" placeholder="Enter phone number" required />
                <button type="button" class="remove-phone" onclick="removePhoneField(this)">Remove</button>
            `;
            container.appendChild(entry);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('select[name="account_type"]').addEventListener('change', toggleAdditionalFields);
            document.getElementById('add-phone').addEventListener('click', addPhoneField);
            toggleAdditionalFields(); // Initial check
        });
    </script>
</head>
<body>
    <h2>Create Account</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="index.php">

        <label>Last name</label>
        <input type="text" name="name" maxlength="30"
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required />

        <label>First name</label>
        <input type="text" name="firstname" maxlength="30"
               value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" required />

        <label>Email</label>
        <input type="email" name="email" maxlength="100"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required />

        <label>Phone numbers</label>
        <div id="phone-fields">
            <?php
            $submittedPhones = $_POST['phones'] ?? [''];
            foreach ($submittedPhones as $phoneValue):
                $phoneValue = trim($phoneValue);
                if ($phoneValue === '') {
                    continue;
                }
            ?>
                <div class="phone-entry">
                    <input type="text" name="phones[]" maxlength="30" value="<?= htmlspecialchars($phoneValue) ?>" placeholder="Enter phone number" required />
                    <button type="button" class="remove-phone" onclick="removePhoneField(this)">Remove</button>
                </div>
            <?php endforeach; ?>
            <?php if (empty($submittedPhones) || count(array_filter($submittedPhones, fn($phone) => trim($phone) !== '')) === 0): ?>
                <div class="phone-entry">
                    <input type="text" name="phones[]" maxlength="30" placeholder="Enter phone number" required />
                    <button type="button" class="remove-phone" onclick="removePhoneField(this)">Remove</button>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" id="add-phone">Add another phone number</button>

        <label>Password</label>
        <input type="password" name="password" minlength="6" required />

        <label>Account type</label>
        <select name="account_type" required>
            <option value="">-- Select --</option>
            <option value="admin"   <?= (($_POST['account_type'] ?? '') === 'admin')   ? 'selected' : '' ?>>Admin</option>
           <option value="supplier"  <?= (($_POST['account_type'] ?? '') === 'supplier')   ? 'selected' : '' ?>>Supplier</option>
           <option value="client" <?= (($_POST['account_type'] ?? '' ) === 'client') ? 'selected' : ''?>>Client</option>
           <option value="farmer" <?= (($_POST['account_type'] ?? '' ) === 'farmer') ? 'selected' : ''?>>Farmer</option>
           <option value="simple" <?= (($_POST['account_type'] ?? '' ) === 'simple') ? 'selected' : ''?>>Simple</option>
        </select>

        <div id="farmer-fields" class="additional-fields">
            <label>Land Area (hectares)</label>
            <input type="number" name="land_area" min="0" step="0.01"
                   value="<?= htmlspecialchars($_POST['land_area'] ?? '') ?>" />

            <label>Years of Experience</label>
            <input type="number" name="year_of_experience" min="0"
                   value="<?= htmlspecialchars($_POST['year_of_experience'] ?? '') ?>" />
        </div>

        <div id="client-fields" class="additional-fields">
            <label>Client Type</label>
            <input type="text" name="client_type" maxlength="200"
                   value="<?= htmlspecialchars($_POST['client_type'] ?? '') ?>" />

            <label>Preferences</label>
            <textarea name="preference" rows="3" maxlength="1000"
                      placeholder="Describe your preferences..."><?= htmlspecialchars($_POST['preference'] ?? '') ?></textarea>
        </div>

        <label>Localisation <span class="optional">(optional)</span></label>
        <input type="text" name="localisation" maxlength="100"
               value="<?= htmlspecialchars($_POST['localisation'] ?? '') ?>" />

        <label>Language <span class="optional">(optional)</span></label>
        <select name="language">
            <option value="en" <?= (($_POST['language'] ?? 'en') === 'en') ? 'selected' : '' ?>>English</option>
            <option value="fr" <?= (($_POST['language'] ?? '') === 'fr') ? 'selected' : '' ?>>Français</option>
            <option value="mg" <?= (($_POST['language'] ?? '') === 'mg') ? 'selected' : '' ?>>Malagasy</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <p class="info-link">
        Déjà un compte ? <a href="index.php">Connectez-vous ici</a>.
    </p>
</body>
</html>