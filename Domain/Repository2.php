<?php

require_once('Settings-DB.php');
require_once('Contact.php');
require_once('ContactCriteria.php');

class Repository2
{
	private $db;
	
	public function __construct()
	{
		$this->db = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
	}
	
	
	public function getContact($criteria)
	{
		// start building query string
		$query = 'select * from `Contact`';
		
		if($criteria != null)
		{
			// get where clause based on criteria properties
			if($whereClause = $this->getWhereClauseFromContactCriteria($criteria))
			{
				$query .= ' where ' . $whereClause;
			}
			
			// get order clause based on criteria properties
			if(isset($criteria->sort)) 
			{
				$query .= ' order by ' . $criteria->sort;
			}
		}
		
		// add limit if set in criteria object
		$limit = false;
		if($criteria != null and isset($criteria->limit))
		{
			$limit = intval($criteria->limit);
			if($limit)
			{
				$query .= ' LIMIT ' . $limit;
			}
		}
		
		// prepare and run query
		$preparedQuery = $this->db->prepare($query);
		$preparedQuery->execute();
		
		// build array of objects from query results
		$contacts = array();
		while($row = $preparedQuery->fetch(PDO::FETCH_ASSOC))
		{
			$object = new Contact();
			$object->id 		= $row['ID'];
			$object->active 	= ($row['Active'] == '1');
			$object->name 		= $row['Name'];
			$object->address 	= $row['Address'];
			$object->city 		= $row['City'];
			$object->state 		= $row['State'];
			$object->zip 		= $row['Zip'];
			$object->birthday 	= $row['Birthday'];
			
			if($limit == 1)
			{
				// only one object requested, so return it
				return $object;
			}
			
			$contacts[] = $object;
		}
		
		return $contacts;
		
	}
	
	
	public function saveContact(Contact $object)
	{
		if($object->id)
		{
			$query = 'UPDATE `Contact` SET ';
		}
		else
		{
			$query = 'INSERT INTO `Contact` SET ';
		}
		
		$query .= ' `Active`	= :Active,
					`Name` 		= :Name,
					`Address` 	= :Address,
					`City` 		= :City,
					`State` 	= :State,
					`Zip` 		= :Zip,
					`Birthday` 	= :Birthday
				';
		
		if($object->id)
		{
			$query .= 'WHERE `ID` = ' . intval($object->id);			
		}
		
		$preparedQuery = $this->db->prepare($query);
		
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

		if(!$object->id) 
		{
			$object->id = $this->db->lastInsertId();
		}
		
		return $object->id;
			
	}
	
	
	public function deleteContact($contactID)
	{
		$query = 'delete from `Contact` where `ID` = :ID';
		$preparedQuery = $this->db->prepare($query);
		$preparedQuery->bindValue(':ID', $contactID);
		return $preparedQuery->execute();
	}
	
	
	
	private function getWhereClauseFromContactCriteria(ContactCriteria $criteria)
	{
		$clauses = array();
		
		if($criteria->id)
		{
			// that's unique, no reason to go further
			return "`Contact`.`ID` = '" . intval($criteria->id) . "'";
		}
		
		if(isset($criteria->nameLike))
		{
			$clauses[] = "`Contact`.`Name` LIKE '%" . mysql_escape_string($criteria->nameLike) . "%'";
		}
		
		if(isset($criteria->state))
		{
			$clauses[] = "`Contact`.`State` = '" . mysql_escape_string($criteria->state) . "'";
		}
		
		if(isset($criteria->zip))
		{
			$clauses[] = "`Contact`.`Zip` = '" . mysql_escape_string($criteria->zip) . "'";
		}
		
		if(isset($criteria->birthday))
		{
			$clauses[] = "`Contact`.`Birthday` = '" . mysql_escape_string($criteria->birthday) . "'";
		}
		
		if(isset($criteria->birthdayOnOrBefore))
		{
			$clauses[] = "`Contact`.`Birthday` <= '" . mysql_escape_string($criteria->birthdayOnOrBefore) . "'";
		}
		
		if(isset($criteria->birthdayOnOrAfter))
		{
			$clauses[] = "`Contact`.`Birthday` >= '" . mysql_escape_string($criteria->birthdayOnOrAfter) . "'";
		}
		
		
		
		$whereClause = implode(' AND ', $clauses);
		
		return $whereClause;
	
	}
		
	
}