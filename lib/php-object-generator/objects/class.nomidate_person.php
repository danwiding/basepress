<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `nomidate_person` (
	`nomidate_personid` int(11) NOT NULL auto_increment,
	`firstname` VARCHAR(255) NOT NULL,
	`lastname` VARCHAR(255) NOT NULL,
	`fbid` VARCHAR(255) NOT NULL, PRIMARY KEY  (`nomidate_personid`)) ENGINE=MyISAM;
*/

/**
* <b>nomidate_person</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0f / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://www.phpobjectgenerator.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=nomidate_person&attributeList=array+%28%0A++0+%3D%3E+%27firstname%27%2C%0A++1+%3D%3E+%27lastname%27%2C%0A++2+%3D%3E+%27fbid%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2529
*/
class nomidate_person extends POG_Base
{
	public $nomidate_personId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $firstname;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $lastname;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $fbid;
	
	public $pog_attribute_type = array(
		"nomidate_personId" => array('db_attributes' => array("NUMERIC", "INT")),
		"firstname" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"lastname" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"fbid" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		);
	public $pog_query;
	
	
	/**
	* Getter for some private attributes
	* @return mixed $attribute
	*/
	public function __get($attribute)
	{
		if (isset($this->{"_".$attribute}))
		{
			return $this->{"_".$attribute};
		}
		else
		{
			return false;
		}
	}
	
	function nomidate_person($firstname='', $lastname='', $fbid='')
	{
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->fbid = $fbid;
	}
	
	
	/**
	* Gets object from database
	* @param integer $nomidate_personId 
	* @return object $nomidate_person
	*/
	function Get($nomidate_personId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `nomidate_person` where `nomidate_personid`='".intval($nomidate_personId)."' LIMIT 1";
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->nomidate_personId = $row['nomidate_personid'];
			$this->firstname = $this->Unescape($row['firstname']);
			$this->lastname = $this->Unescape($row['lastname']);
			$this->fbid = $this->Unescape($row['fbid']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $nomidate_personList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `nomidate_person` ";
		$nomidate_personList = Array();
		if (sizeof($fcv_array) > 0)
		{
			$this->pog_query .= " where ";
			for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
			{
				if (sizeof($fcv_array[$i]) == 1)
				{
					$this->pog_query .= " ".$fcv_array[$i][0]." ";
					continue;
				}
				else
				{
					if ($i > 0 && sizeof($fcv_array[$i-1]) != 1)
					{
						$this->pog_query .= " AND ";
					}
					if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
					{
						if ($GLOBALS['configuration']['db_encoding'] == 1)
						{
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : "'".$fcv_array[$i][2]."'";
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$this->Escape($fcv_array[$i][2])."'";
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$fcv_array[$i][2]."'";
						$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
					}
				}
			}
		}
		if ($sortBy != '')
		{
			if (isset($this->pog_attribute_type[$sortBy]['db_attributes']) && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
			{
				if ($GLOBALS['configuration']['db_encoding'] == 1)
				{
					$sortBy = "BASE64_DECODE($sortBy) ";
				}
				else
				{
					$sortBy = "$sortBy ";
				}
			}
			else
			{
				$sortBy = "$sortBy ";
			}
		}
		else
		{
			$sortBy = "nomidate_personid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$nomidate_person = new $thisObjectName();
			$nomidate_person->nomidate_personId = $row['nomidate_personid'];
			$nomidate_person->firstname = $this->Unescape($row['firstname']);
			$nomidate_person->lastname = $this->Unescape($row['lastname']);
			$nomidate_person->fbid = $this->Unescape($row['fbid']);
			$nomidate_personList[] = $nomidate_person;
		}
		return $nomidate_personList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $nomidate_personId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$this->pog_query = "select `nomidate_personid` from `nomidate_person` where `nomidate_personid`='".$this->nomidate_personId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows > 0)
		{
			$this->pog_query = "update `nomidate_person` set 
			`firstname`='".$this->Escape($this->firstname)."', 
			`lastname`='".$this->Escape($this->lastname)."', 
			`fbid`='".$this->Escape($this->fbid)."' where `nomidate_personid`='".$this->nomidate_personId."'";
		}
		else
		{
			$this->pog_query = "insert into `nomidate_person` (`firstname`, `lastname`, `fbid` ) values (
			'".$this->Escape($this->firstname)."', 
			'".$this->Escape($this->lastname)."', 
			'".$this->Escape($this->fbid)."' )";
		}
		$insertId = Database::InsertOrUpdate($this->pog_query, $connection);
		if ($this->nomidate_personId == "")
		{
			$this->nomidate_personId = $insertId;
		}
		return $this->nomidate_personId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $nomidate_personId
	*/
	function SaveNew()
	{
		$this->nomidate_personId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `nomidate_person` where `nomidate_personid`='".$this->nomidate_personId."'";
		return Database::NonQuery($this->pog_query, $connection);
	}
	
	
	/**
	* Deletes a list of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param bool $deep 
	* @return 
	*/
	function DeleteList($fcv_array)
	{
		if (sizeof($fcv_array) > 0)
		{
			$connection = Database::Connect();
			$pog_query = "delete from `nomidate_person` where ";
			for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
			{
				if (sizeof($fcv_array[$i]) == 1)
				{
					$pog_query .= " ".$fcv_array[$i][0]." ";
					continue;
				}
				else
				{
					if ($i > 0 && sizeof($fcv_array[$i-1]) !== 1)
					{
						$pog_query .= " AND ";
					}
					if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
					{
						$pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." '".$this->Escape($fcv_array[$i][2])."'";
					}
					else
					{
						$pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." '".$fcv_array[$i][2]."'";
					}
				}
			}
			return Database::NonQuery($pog_query, $connection);
		}
	}
}
?>