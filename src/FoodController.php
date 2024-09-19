<?php
namespace App;

class FoodController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addFood($name, $category, $iddsiLevel)
	{
		$this->pdo->beginTransaction();
		
		try {
			$sql = 'INSERT INTO foods (name, category, iddsiLevel) VALUES (:name, :category, :iddsiLevel)';
			$query = $this->pdo->prepare($sql);
			$result = $query->execute([
				'name' => $name,
				'category' => $category,
				'iddsiLevel' => $iddsiLevel
			]);

			if (!$result) {
				throw new Exception('Failed to insert food item.');
			}

			$lastInsertId = $this->pdo->lastInsertId();

			$sql = 'SELECT 
						foods.id,
						foods.name AS foodName,
						foods.iddsiLevel AS iddsiLevel,
						category.name AS categoryName,
						iddsilevel.level AS iddsiLevelLevel,
						iddsilevel.name AS iddsiLevelName,
						foods.created_at
					FROM 
						foods
					JOIN 
						category ON foods.category = category.id
					JOIN 
						iddsilevel ON foods.iddsiLevel = iddsilevel.id
					WHERE 
						foods.id = :id';
			$query = $this->pdo->prepare($sql);
			$query->execute(['id' => $lastInsertId]);
			$newFoodItem = $query->fetch(\PDO::FETCH_ASSOC);

			$this->pdo->commit();

			\App\Response::json(['success' => true, 'data' => $newFoodItem]);
		} catch (Exception $e) {
			$this->pdo->rollBack();
			\App\Response::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

    public function getFoodItems()
    {
        $sql = 'SELECT 
					foods.id,
					foods.name AS foodName,
					foods.iddsiLevel AS iddsiLevel,
					category.name AS categoryName,
					iddsilevel.level AS iddsiLevelLevel,
					iddsilevel.name AS iddsiLevelName,
					foods.created_at
				FROM 
					foods
				JOIN 
					category ON foods.category = category.id
				JOIN 
					iddsilevel ON foods.iddsiLevel = iddsilevel.id;';
        $query = $this->pdo->query($sql);
        $foodItems = $query->fetchAll(\PDO::FETCH_ASSOC);
        \App\Response::json($foodItems);
    }
	
	public function getFoodCategories()
    {
        $sql = 'SELECT * FROM category';
        $query = $this->pdo->query($sql);
        $foodCategories = $query->fetchAll(\PDO::FETCH_ASSOC);
        \App\Response::json($foodCategories);
    }
	
	public function getLevels()
    {
        $sql = 'SELECT * FROM iddsilevel';
        $query = $this->pdo->query($sql);
        $levels = $query->fetchAll(\PDO::FETCH_ASSOC);
        \App\Response::json($levels);
    }
	
	public function importCSVFoods($csvData)
	{
		$dataResult = [];
		try {
			$this->pdo->beginTransaction();
			$csvData = json_decode($csvData);
			foreach ($csvData->csvData as $foodItem) {
				$sql = 'INSERT INTO foods (name, category, iddsiLevel) VALUES (:name, :category, :iddsiLevel)';
				$query = $this->pdo->prepare($sql);
				$result = $query->execute([
					'name' => $foodItem->name,
					'category' => $foodItem->category,
					'iddsiLevel' => $foodItem->iddsiLevel
				]);
				
				if (!$result) {
					throw new Exception('Failed to insert food item.');
				}
				
				$lastInsertId = $this->pdo->lastInsertId();

				$sql = 'SELECT 
							foods.id,
							foods.name AS foodName,
							foods.iddsiLevel AS iddsiLevel,
							category.name AS categoryName,
							iddsilevel.level AS iddsiLevelLevel,
							iddsilevel.name AS iddsiLevelName,
							foods.created_at
						FROM 
							foods
						JOIN 
							category ON foods.category = category.id
						JOIN 
							iddsilevel ON foods.iddsiLevel = iddsilevel.id
						WHERE 
							foods.id = :id';
				$query = $this->pdo->prepare($sql);
				$query->execute(['id' => $lastInsertId]);
				$newFoodItem = $query->fetch(\PDO::FETCH_ASSOC);
				
				$dataResult[] = $newFoodItem;
			}
			$this->pdo->commit();
			\App\Response::json(['success' => true, 'data' => $dataResult]);
		} catch (Exception $e) {
			$this->pdo->rollBack();
			\App\Response::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}
	
	
}
?>