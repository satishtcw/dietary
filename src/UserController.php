<?php
namespace App;

class UserController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function login($username, $password)
    {
		$sql = 'SELECT * FROM users WHERE username = :username';
		$query = $this->pdo->prepare($sql);
		$query->execute(['username' => $username]);

		$user = $query->fetch(\PDO::FETCH_ASSOC);
		
		if ($user && password_verify($password, $user['password'])) {
			\App\Response::json(['success' => true]);
		} else {
			\App\Response::json(['success' => false], 401);
		}
    }
	
	public function assignFoodToResident($residentId, $foods)
	{
		$this->pdo->beginTransaction();

		try {
			$deleteSql = 'DELETE FROM resident_foods WHERE resident_id = :resident_id';
			$deleteQuery = $this->pdo->prepare($deleteSql);
			$deleteQuery->execute(['resident_id' => $residentId]);
			
			$foodsJson = json_encode($foods);

			$insertSql = 'INSERT INTO resident_foods (resident_id, foods) VALUES (:resident_id, :foods)';
			$insertQuery = $this->pdo->prepare($insertSql);
			$result = $insertQuery->execute([
				'resident_id' => $residentId,
				'foods' => $foodsJson
			]);

			$this->pdo->commit();

			if ($result) {
				\App\Response::json(['success' => true]);
			} else {
				throw new \Exception('Failed to insert new data');
			}

		} catch (\Exception $e) {
			$this->pdo->rollBack();
			\App\Response::json(['success' => false, 'error' => $e->getMessage()], 500);
		}
	}
	
	public function getAssignedFoods($residentId)
	{
		$sql = 'SELECT foods FROM resident_foods WHERE resident_id = :resident_id';
		$query = $this->pdo->prepare($sql);
		$query->execute(['resident_id' => $residentId]);
		$result = $query->fetch(\PDO::FETCH_ASSOC);

		if ($result) {
			// Decode JSON data
			$assignedFoodsArray = json_decode($result['foods'], true);
			$assignedFoods = array_map('intval', $assignedFoodsArray);
			\App\Response::json(['success' => true, 'assignedFoods' => $assignedFoods]);
		} else {
			\App\Response::json(['success' => false, 'message' => 'No assigned foods found.']);
		}
	}
}
?>