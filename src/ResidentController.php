<?php
namespace App;

class ResidentController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addResident($name, $iddsiLevel)
    {
		$this->pdo->beginTransaction();
		try {
			$sql = 'INSERT INTO residents (name, iddsiLevel) VALUES (:name, :iddsiLevel)';
			$query = $this->pdo->prepare($sql);
			$result = $query->execute(['name' => $name, 'iddsiLevel' => $iddsiLevel]);
			
			if (!$result) {
				throw new Exception('Failed to insert Resident item.');
			}

			$lastInsertId = $this->pdo->lastInsertId();
			
			$sql = 'SELECT 
					residents.id,
					residents.name AS residentName,
					residents.iddsiLevel AS iddsiLevel,
					iddsilevel.level AS iddsiLevelLevel,
					iddsilevel.name AS iddsiLevelName,
					residents.created_at
				FROM 
					residents
				JOIN 
					iddsilevel ON residents.iddsiLevel = iddsilevel.id
				WHERE 
						residents.id = :id';
			
			$query = $this->pdo->prepare($sql);
			$query->execute(['id' => $lastInsertId]);
			$newResidentItem = $query->fetch(\PDO::FETCH_ASSOC);

			$this->pdo->commit();

			\App\Response::json(['success' => true, 'data' => $newResidentItem]);
		} catch (Exception $e) {
			$this->pdo->rollBack();
			\App\Response::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
    }

    public function getResidents()
    {		
        $sql = 'SELECT 
					residents.id,
					residents.name AS residentName,
					residents.iddsiLevel AS iddsiLevel,
					iddsilevel.level AS iddsiLevelLevel,
					iddsilevel.name AS iddsiLevelName,
					residents.created_at
				FROM 
					residents
				JOIN 
					iddsilevel ON residents.iddsiLevel = iddsilevel.id';

        $query = $this->pdo->query($sql);
        $residents = $query->fetchAll(\PDO::FETCH_ASSOC);
        \App\Response::json($residents);
    }
}
?>