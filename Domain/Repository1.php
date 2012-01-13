<?php

require_once('Settings-DB.php');
require_once('Contact.php');

class Repository1
{
	private $db;
	
	public function __construct()
	{
		$this->db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
	}
	
	
	public function getContact($contactID)
	{
		$query = 'select * from `Contact` where `ID` = :ID';
		$preparedQuery = $this->db->prepare($query);
		$preparedQuery->bindValue(':ID', $contactID);
		if(!$preparedQuery->execute()) 
		{
			echo 'ERROR: getContact: ' . print_r($preparedQuery->errorInfo(), true);
			return false;
		}
				
		if(!$row = $preparedQuery->fetch(PDO::FETCH_ASSOC))
		{
			// error
			return false;
		}
		
		$object = new Contact();
		$object->id 		= $row['ID'];
		$object->active 	= ($row['Active'] == '1');
		$object->name 		= $row['Name'];
		$object->address 	= $row['Address'];
		$object->city 		= $row['City'];
		$object->state 		= $row['State'];
		$object->zip 		= $row['Zip'];
		$object->birthday 	= $row['Birthday'];
		
		return $object;
		
	}
	
	
	public function saveContact(Contact $object)
	{
		$query = 'INSERT INTO `Contact` SET
 					`ID` 		= :ID,
					`Active`	= :Active,
					`Name` 		= :Name,
					`Address` 	= :Address,
					`City` 		= :City,
					`State` 	= :State,
					`Zip` 		= :Zip,
					`Birthday` 	= :Birthday
				';
		
		$preparedQuery = $this->db->prepare($query);
		
		$preparedQuery->bindValue(':ID', $object->id);
		$preparedQuery->bindValue(':Active', ($object->active ? '1' : '0'));
		$preparedQuery->bindValue(':Name', $object->name);
		$preparedQuery->bindValue(':Address', $object->address);
		$preparedQuery->bindValue(':City', $object->city);
		$preparedQuery->bindValue(':State', $object->state);
		$preparedQuery->bindValue(':Zip', $object->zip);
		$preparedQuery->bindValue(':Birthday', $object->birthday);
		
		if(!$preparedQuery->execute()) 
		{
			echo 'ERROR: saveContact: ' . print_r($preparedQuery->errorInfo(), true);
			return false;
		}
		
		$object->id = $this->db->lastInsertId();

		return $object->id;
			
	}
	
	
	public function deleteContact($contactID)
	{
		$query = 'delete from `Contact` where `ID` = :ID';
		$preparedQuery = $this->db->prepare($query);
		$preparedQuery->bindValue(':ID', $contactID);
		return $preparedQuery->execute();
	}
	
	
	
}