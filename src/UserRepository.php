<?php

require_once __DIR__ . '/../config/database.php';

class UserRepository {

    private PDO $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

    public function getUserByEmail(string $email): array|false {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSimulationsByUser(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM simulations WHERE user_id = :user_id ORDER BY id DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSimulationById(int $simulationId, int $userId): array|false {
        $stmt = $this->db->prepare('SELECT * FROM simulations WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $simulationId, 'user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createSimulation(int $userId, array $data): bool {
        $stmt = $this->db->prepare('INSERT INTO simulations (user_id, wording, "estimatedTotalCost", "dailyCost", "estimatedIncome", "estimatedProfit", "optimalSaleDate", "estimatedWeight", "simulationDate") VALUES (:user_id, :wording, :estimatedTotalCost, :dailyCost, :estimatedIncome, :estimatedProfit, :optimalSaleDate, :estimatedWeight, :simulationDate)');
        return $stmt->execute([
            'user_id' => $userId,
            'wording' => $data['wording'],
            'estimatedTotalCost' => $data['estimatedTotalCost'],
            'dailyCost' => $data['dailyCost'],
            'estimatedIncome' => $data['estimatedIncome'],
            'estimatedProfit' => $data['estimatedProfit'],
            'optimalSaleDate' => $data['optimalSaleDate'],
            'estimatedWeight' => $data['estimatedWeight'],
            'simulationDate' => $data['simulationDate'] ?? date('Y-m-d'),
        ]);
    }

    public function updateSimulation(int $simulationId, int $userId, array $data): bool {
        $stmt = $this->db->prepare('UPDATE simulations SET wording = :wording, "estimatedTotalCost" = :estimatedTotalCost, "dailyCost" = :dailyCost, "estimatedIncome" = :estimatedIncome, "estimatedProfit" = :estimatedProfit, "optimalSaleDate" = :optimalSaleDate, "estimatedWeight" = :estimatedWeight, "simulationDate" = :simulationDate WHERE id = :id AND user_id = :user_id');
        return $stmt->execute([
            'wording' => $data['wording'],
            'estimatedTotalCost' => $data['estimatedTotalCost'],
            'dailyCost' => $data['dailyCost'],
            'estimatedIncome' => $data['estimatedIncome'],
            'estimatedProfit' => $data['estimatedProfit'],
            'optimalSaleDate' => $data['optimalSaleDate'],
            'estimatedWeight' => $data['estimatedWeight'],
            'simulationDate' => $data['simulationDate'] ?? date('Y-m-d'),
            'id' => $simulationId,
            'user_id' => $userId,
        ]);
    }

    public function deleteSimulation(int $simulationId, int $userId): bool {
        $stmt = $this->db->prepare('DELETE FROM simulations WHERE id = :id AND user_id = :user_id');
        return $stmt->execute(['id' => $simulationId, 'user_id' => $userId]);
    }

    public function create(
        string $name,
        string $firstname,
        string $email,
        string $password,
        string $account_type,
        string $localisation = '',
        string $language = 'en'
    ): int|false {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare('
            INSERT INTO users (name, firstname, email, password, account_type, localisation, language)
            VALUES (:name, :firstname, :email, :password, :account_type, :localisation, :language)
            RETURNING id
        ');

        $stmt->execute([
            'name'         => $name,
            'firstname'    => $firstname,
            'email'        => $email,
            'password'     => $hashed,
            'account_type' => $account_type,
            'localisation' => $localisation,
            'language'     => $language,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : false;
    }

    public function createFarmer(int $userId, float $landArea, int $yearOfExperience): bool {
        $stmt = $this->db->prepare('
            INSERT INTO farmers (user_id, land_area, year_of_experience)
            VALUES (:user_id, :land_area, :year_of_experience)
        ');
        return $stmt->execute([
            'user_id' => $userId,
            'land_area' => $landArea,
            'year_of_experience' => $yearOfExperience,
        ]);
    }

    public function createSupplier(int $userId): bool {
        $stmt = $this->db->prepare('
            INSERT INTO suppliers (user_id)
            VALUES (:user_id)
        ');
        return $stmt->execute(['user_id' => $userId]);
    }

    private function createPhones(int $userId, array $phones): bool {
        if (empty($phones)) {
            return true;
        }

        $stmt = $this->db->prepare('
            INSERT INTO phones (phone, user_id)
            VALUES (:phone, :user_id)
        ');

        foreach ($phones as $phone) {
            if (!$stmt->execute(['phone' => $phone, 'user_id' => $userId])) {
                return false;
            }
        }

        return true;
    }

    public function createUserWithDetails(
        string $name,
        string $firstname,
        string $email,
        string $password,
        string $account_type,
        string $localisation = '',
        string $language = 'en',
        float $land_area = 0.0,
        int $year_of_experience = 0,
        string $client_type = '',
        string $preference = '',
        array $phones = []
    ): bool {
        try {
            $this->db->beginTransaction();

            // Insert into users table
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare('
                INSERT INTO users (name, firstname, email, password, account_type, localisation, language)
                VALUES (:name, :firstname, :email, :password, :account_type, :localisation, :language)
                RETURNING id
            ');
            $stmt->execute([
                'name'         => $name,
                'firstname'    => $firstname,
                'email'        => $email,
                'password'     => $hashed,
                'account_type' => $account_type,
                'localisation' => $localisation,
                'language'     => $language,
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                throw new Exception("Failed to insert user");
            }
            $userId = $result['id'];

            // Insert into specific table based on account_type
            switch ($account_type) {
                case 'farmer':
                    $stmt = $this->db->prepare('
                        INSERT INTO farmers (user_id, land_area, year_of_experience)
                        VALUES (:user_id, :land_area, :year_of_experience)
                    ');
                    $stmt->execute([
                        'user_id' => $userId,
                        'land_area' => $land_area,
                        'year_of_experience' => $year_of_experience,
                    ]);
                    break;

                case 'supplier':
                    $stmt = $this->db->prepare('
                        INSERT INTO suppliers (user_id)
                        VALUES (:user_id)
                    ');
                    $stmt->execute(['user_id' => $userId]);
                    break;

                case 'client':
                    $stmt = $this->db->prepare('
                        INSERT INTO clients (user_id, client_type, preference)
                        VALUES (:user_id, :client_type, :preference)
                    ');
                    $stmt->execute([
                        'user_id' => $userId,
                        'client_type' => $client_type,
                        'preference' => $preference,
                    ]);
                    break;

                // For admin and simple, no additional table needed
                case 'admin':
                case 'simple':
                    break;

                default:
                    throw new Exception("Invalid account type");
            }

            if (!$this->createPhones($userId, $phones)) {
                throw new Exception("Failed to insert phone numbers");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}