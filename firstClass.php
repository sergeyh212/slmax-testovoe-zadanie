<?php

class Person
{
	public $id;
	private $firstName;
	private $lastName;
	private $birthday;
	private $sex;
	private $cityOfBirth;

	private $conn;
	private $isExist = false;


	public function __construct($id, $firstName, $lastName, $birthday, $sex, $cityOfBirth)
	{

		$this->connectToDatabase();

		if ($this->conn != null) {
			$sql = "SELECT * FROM People";


			if ($sql != null)
				$res = $this->conn->query($sql);
			foreach ($res as $row) {
				if ($id == $row['id']) {
					$this->id = $row['id'];
					$this->firstName = $row['firstName'];
					$this->lastName = $row['lastName'];
					$this->birthday = $row['birthday'];
					$this->sex = $row['sex'];
					$this->cityOfBirth = $row['cityOfBirth'];

					$this->isExist = true;
					break;
				}
			}

			if (!$this->isExist) {
				try {
					$this->personValidation($firstName, $lastName, $birthday, $sex);
					$this->id = $id;
					$this->firstName = $firstName;
					$this->lastName = $lastName;
					$this->birthday = $birthday;
					$this->sex = $sex;
					$this->cityOfBirth = $cityOfBirth;
				} catch (Exception $e) {
					echo "Error: " . $e->getMessage();
				}
			}
		}
	}

	public function add()
	{
		if (!$this->isExist) {
			$sql = "INSERT INTO People (id, firstName, lastName, birthday, sex, cityOfBirth) 
		   VALUES($this->id, '$this->firstName', '$this->lastName', '$this->birthday', $this->sex, '$this->cityOfBirth')";
			$this->conn->exec($sql);

			echo 'The adding successfully completed';
		} else
			echo 'Data saved';
	}

	public function remove()
	{
		$sql = "DELETE FROM People WHERE id = $this->id";
		$this->conn->exec($sql);

		echo 'The removing successfully completed';
	}

	static function transformBirth($birthday)
	{
		$date = date('Y-m-d');
		$date = explode('-', $date);
		$birthday = explode('-', $birthday);

		$age = ((int) $date[0] - (int) $birthday[0]);

		if ((int) $date[1] - (int) $birthday[1] >= 0) {
			if ((int) $date[2] - (int) $birthday[2] >= 0) {
				return $age;
			} else {
				return $age - 1;
			}
		} else {
			return $age - 1;
		}
	}

	static function transformSex($sex)
	{
		if ($sex == 0)
			return 'Man';
		else if ($sex == 1)
			return 'Woman';
		else
			return 'Wrong argument';
	}

	public function getPerson()
	{
		$person = new stdClass();
		$person->id = $this->id;
		$person->firstName = $this->firstName;
		$person->lastName = $this->lastName;
		$person->birthday = $this->transformBirth($this->birthday);
		$person->sex =	 $this->transformSex($this->sex);
		$person->cityOfBirth = $this->cityOfBirth;

		return $person;
	}

	private function personValidation($firstName, $lastName, $birthday, $sex)
	{
		$regexName = '/[a-zа-яё]/ui';
		$regexBirthday = '/^[0-9]{4}-[0-1]{1}[0-9]{1}-[0-3]{1}[0-9]{1}$/';

		if (!preg_match($regexName, $firstName ?? '') || !preg_match($regexName, $lastName ?? '')) {
			throw new Exception('Wrong first or last name!');
		}

		if (!preg_match($regexBirthday, $birthday ?? '')) {
			throw new Exception('Wrong birthday format!');
		}

		if ($sex != 0 && $sex != 1) {
			throw new Exception('Wrong sex!');
		}
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
