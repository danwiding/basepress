<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `nomination` (
	`nominationid` int(11) NOT NULL auto_increment,
	`person1` INT NOT NULL,
	`person2` INT NOT NULL,
	`nominator` INT NOT NULL,
	`datecreated` DATETIME NOT NULL,
	`person1status` VARCHAR(255) NOT NULL,
	`person2status` VARCHAR(255) NOT NULL, PRIMARY KEY  (`nominationid`)) ENGINE=MyISAM;
*/

/**
 * <b>nomination</b> class with integrated CRUD methods.
 * @author Php Object Generator
 * @version POG 3.0f / PHP5.1 MYSQL
 * @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
 * @copyright Free for personal & commercial use. (Offered under the BSD license)
 * @link http://www.phpobjectgenerator.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=nomination&attributeList=array+%28%0A++0+%3D%3E+%27person1%27%2C%0A++1+%3D%3E+%27person2%27%2C%0A++2+%3D%3E+%27nominator%27%2C%0A++3+%3D%3E+%27DateCreated%27%2C%0A++4+%3D%3E+%27person1status%27%2C%0A++5+%3D%3E+%27person2status%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527INT%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527INT%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527INT%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527DATETIME%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2529
 */
class nomination extends POG_Base
{
    public $nominationId = '';

    /**
     * @var INT
     */
    public $person1;

    /**
     * @var INT
     */
    public $person2;

    /**
     * @var INT
     */
    public $nominator;

    /**
     * @var DATETIME
     */
    public $DateCreated;

    /**
     * @var VARCHAR(255)
     */
    public $person1status;

    /**
     * @var VARCHAR(255)
     */
    public $person2status;

    public $pog_attribute_type = array(
        "nominationId" => array('db_attributes' => array("NUMERIC", "INT")),
        "person1" => array('db_attributes' => array("NUMERIC", "INT")),
        "person2" => array('db_attributes' => array("NUMERIC", "INT")),
        "nominator" => array('db_attributes' => array("NUMERIC", "INT")),
        "DateCreated" => array('db_attributes' => array("TEXT", "DATETIME")),
        "person1status" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
        "person2status" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
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

    function nomination($person1='', $person2='', $nominator='', $DateCreated='', $person1status='', $person2status='')
    {
        $this->person1 = $person1;
        $this->person2 = $person2;
        $this->nominator = $nominator;
        $this->DateCreated = $DateCreated;
        $this->person1status = $person1status;
        $this->person2status = $person2status;
    }


    /**
     * Gets object from database
     * @param integer $nominationId
     * @return object $nomination
     */
    function Get($nominationId)
    {
        $connection = Database::Connect();
        $this->pog_query = "select * from `nomination` where `nominationid`='".intval($nominationId)."' LIMIT 1";
        $cursor = Database::Reader($this->pog_query, $connection);
        while ($row = Database::Read($cursor))
        {
            $this->nominationId = $row['nominationid'];
            $this->person1 = $this->Unescape($row['person1']);
            $this->person2 = $this->Unescape($row['person2']);
            $this->nominator = $this->Unescape($row['nominator']);
            $this->DateCreated = $row['datecreated'];
            $this->person1status = $this->Unescape($row['person1status']);
            $this->person2status = $this->Unescape($row['person2status']);
        }
        return $this;
    }


    /**
     * Returns a sorted array of objects that match given conditions
     * @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...}
     * @param string $sortBy
     * @param boolean $ascending
     * @param int limit
     * @return array $nominationList
     */
    function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
    {
        $connection = Database::Connect();
        $sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
        $this->pog_query = "select * from `nomination` ";
        $nominationList = Array();
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
            $sortBy = "nominationid";
        }
        $this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
        $thisObjectName = get_class($this);
        $cursor = Database::Reader($this->pog_query, $connection);
        while ($row = Database::Read($cursor))
        {
            $nomination = new $thisObjectName();
            $nomination->nominationId = $row['nominationid'];
            $nomination->person1 = $this->Unescape($row['person1']);
            $nomination->person2 = $this->Unescape($row['person2']);
            $nomination->nominator = $this->Unescape($row['nominator']);
            $nomination->DateCreated = $row['datecreated'];
            $nomination->person1status = $this->Unescape($row['person1status']);
            $nomination->person2status = $this->Unescape($row['person2status']);
            $nominationList[] = $nomination;
        }
        return $nominationList;
    }


    /**
     * Saves the object to the database
     * @return integer $nominationId
     */
    function Save()
    {
        $connection = Database::Connect();
        $this->pog_query = "select `nominationid` from `nomination` where `nominationid`='".$this->nominationId."' LIMIT 1";
        $rows = Database::Query($this->pog_query, $connection);
        if ($rows > 0)
        {
            $this->pog_query = "update `nomination` set
			`person1`='".$this->Escape($this->person1)."', 
			`person2`='".$this->Escape($this->person2)."', 
			`nominator`='".$this->Escape($this->nominator)."', 
			`datecreated`='".$this->DateCreated."', 
			`person1status`='".$this->Escape($this->person1status)."', 
			`person2status`='".$this->Escape($this->person2status)."' where `nominationid`='".$this->nominationId."'";
        }
        else
        {
            $this->pog_query = "insert into `nomination` (`person1`, `person2`, `nominator`, `datecreated`, `person1status`, `person2status` ) values (
			'".$this->Escape($this->person1)."', 
			'".$this->Escape($this->person2)."', 
			'".$this->Escape($this->nominator)."', 
			'".$this->DateCreated."', 
			'".$this->Escape($this->person1status)."', 
			'".$this->Escape($this->person2status)."' )";
        }
        $insertId = Database::InsertOrUpdate($this->pog_query, $connection);
        if ($this->nominationId == "")
        {
            $this->nominationId = $insertId;
        }
        return $this->nominationId;
    }


    /**
     * Clones the object and saves it to the database
     * @return integer $nominationId
     */
    function SaveNew()
    {
        $this->nominationId = '';
        return $this->Save();
    }


    /**
     * Deletes the object from the database
     * @return boolean
     */
    function Delete()
    {
        $connection = Database::Connect();
        $this->pog_query = "delete from `nomination` where `nominationid`='".$this->nominationId."'";
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
            $pog_query = "delete from `nomination` where ";
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