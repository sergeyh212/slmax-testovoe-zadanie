<?php


if (!class_exists('Person')) {
	echo "Error: Class doen't exist";
	die;
}

class People
{
	private $people;

	public $people_array = [];

	private $conn;

	public function __construct($id, $symbol = '=')
	{
		$this->connectToDatabase();

		if ($this->conn != null) {
			$sql = $this->loadPeople($id, $symbol);

			if ($sql != null)
				$this->people = $this->conn->query($sql);
		}
	}

	public function getPeople()
	{
		if ($this->people != null) {
			foreach ($this->people as $person) {
				array_push($this->people_array, new Person($person['id'], $person['firstName'], $person['lastName'], $person['birthday'], $person['sex'], $person['cityOfBirth']));
			}
		}

		return $this->people_array;
	}

	public function removePeople()
	{
		if ($this->getPeople() != null) {
			$people = $this->getPeople();
			foreach ($people as $person) {
				$sql = "DELETE FROM People WHERE id = $person->id";
				$this->conn->exec($sql);
			}
			echo 'The removing cusseccfully completed';
		}
	}

	private function loadPeople($id, $symbol)
	{
		$sql = null;

		switch ($symbol) {
			case '=':
				$sql = "SELECT * FROM People WHERE id = $id";
				break;
			case '>':
				$sql = "SELECT * FROM People WHERE id > $id";
				break;
			case '<':
				$sql = "SELECT * FROM People WHERE id < $id";
				break;
			case '!=':
				$sql = "SELECT * FROM People WHERE id != $id";
				break;
			default:
				echo 'Undefined symbol';
				break;
		}

		return $sql;
	}

	private function connectToDatabase()
	{
		try {
			$this->conn = new PDO("mysql:host=localhost;dbname=testDB", "root", "Fabregas55.");
		} catch (Exception $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
}