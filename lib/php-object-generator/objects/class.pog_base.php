<?php
/**
 *
 */

define('JOIN_SEPARATOR',"_this_is_a_separator_");
abstract class POG_Base
{
	/**
	 * Overloading
	 */
	//if function is not recognized, call a plugin
	function __call($method, $argv)
	{
		include_once($GLOBALS['configuration']['plugins_path']."/IPlugin.php");
		include_once($GLOBALS['configuration']['plugins_path']."/plugin.".strtolower($method).".php");
		eval('$plugin = new $method($this,$argv);');
		return $plugin->Execute();
	}

	/**
	 * constructor
	 *
	 * @return POG_Base
	 */
	private function POG_Base()
	{
	}


	function SetFieldAttribute($fieldName, $attributeName, $attributeValue)
	{
        if (isset($this->pog_attribute_type[$fieldName]) && isset($this->pog_attribute_type[$fieldName][$attributeName]))
        {
             $this->pog_attribute_type[$fieldName][$attributeName] = $attributeValue;
        }
	}

	function GetFieldAttribute($fieldName, $attributeName)
	{
        if (isset($this->pog_attribute_type[$fieldName]) && isset($this->pog_attribute_type[$fieldName][$attributeName]))
        {
        	return $this->pog_attribute_type[$fieldName][$attributeName];
        }
        return null;
	}


	///////////////////////////
	// Data manipulation
	///////////////////////////

	/**
	* This function will try to encode $text to base64, except when $text is a number. This allows us to Escape all data before they're inserted in the database, regardless of attribute type.
	* @param string $text
	* @return string encoded to base64
	*/
	public function Escape($text)
	{
		if ($GLOBALS['configuration']['db_encoding'] && !is_numeric($text))
		{
			return base64_encode($text);
		}
		return addslashes($text);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $text
	 * @return unknown
	 */
	public function Unescape($text)
	{
		if ($GLOBALS['configuration']['db_encoding'] && !is_numeric($text))
		{
			return base64_decode($text);
		}
		return stripcslashes($text);
	}


	////////////////////////////////
	// Table -> Object Mapping
	////////////////////////////////

	/**
	 * Executes $query against database and returns the result set as an array of POG objects
	 *
	 * @param string $query. SQL query to execute against database
	 * @param string $objectClass. POG Object type to return
	 * @param bool $lazy. If true, will also load all children/sibling
	 */
	protected function FetchObjects($query, $objectClass, $lazy = true)
	{
		$databaseConnection = Database::Connect();
		$result = Database::Reader($query, $databaseConnection);
		$objectList = $this->CreateObjects($result, $objectClass, $lazy);
		return $objectList;
	}

	private function CreateObjects($mysql_result, $objectClass, $lazyLoad = true)
	{
		$objectList = array();
		if ($mysql_result != null){
			while ($row =  Database::Read($mysql_result))
			{
				$pog_object = new $objectClass();
				$this->PopulateObjectAttributes($row, $pog_object);
				$objectList[] = $pog_object;
			}
		}
		return $objectList;
	}

	private function PopulateObjectAttributes($fetched_row, $pog_object)
	{
		$att = $this->GetAttributes($pog_object);
 		foreach ($att as $column)
		{
			$pog_object->{$column} = $this->Unescape($fetched_row[strtolower($column)]);
		}
		return $pog_object;
	}

	public function GetAttributes($object, $type='')
	{
		$columns = array();
		foreach ($object->pog_attribute_type as $att => $properties)
		{
			if ($properties['db_attributes'][0] != 'OBJECT')
			{
				if (($type != '' && strtolower($type) == strtolower($properties['db_attributes'][0])) || $type == ''){
					$columns[] = $att;
				}
			}
		}
		return $columns;
	}

	//misc
	public static function IsColumn($value)
	{
		if (strlen($value) > 2)
		{
			if (substr($value, 0, 1) == '`' && substr($value, strlen($value) - 1, 1) == '`')
			{
				return true;
			}
			return false;
		}
		return false;
	}

    abstract function Get($id);

    function SafeGet($id){
        $this->Get($id);

        $id = $this->GetId();
        if(empty($id))
            return null;
        else
            return $this;
    }
    function GetId(){
        $arrayKeys = array_keys($this->pog_attribute_type);
        if($this->$arrayKeys[0])
            return $this->$arrayKeys[0];
        else
            return null;
    }

    function GetIdPropertyName(){
        $arrayKeys = array_keys($this->pog_attribute_type);
        return $arrayKeys[0];
    }

    function Exists(){
        return $this->GetId()!=null;
    }

    protected $modelAssociation = array();

    protected $tableName = "";

//    public $pog_query;

    protected function GetTableName(){
        return $this->tableName;
    }

    public function GetInDepth($id){
        $objectList = $this->GetListInDepth(array(array($this->GetIdPropertyName(), '=', $id)));
        if (empty($objectList))
            return null;
        return $objectList[0];
    }

    public function SaveOneDepth(){
        foreach($this->modelAssociation as $relationId =>$relationNameObjectAssociation){
            $savedId = $this->$relationNameObjectAssociation['property']->Save();
            $this->$relationId = $savedId;
        }
        return $this->Save();
    }

    public function GetChildObject($propertyName){
        foreach($this->modelAssociation as $relationId =>$relationNameObjectAssociation){
            if($relationNameObjectAssociation['property']==$propertyName){
                if(empty($this->$propertyName)){
                    $propertyModel = new $relationNameObjectAssociation['object'];
                    $this->$propertyName = $propertyModel->SafeGet($this->$relationId);
                }
                return $this->$propertyName;
            }
        }
        throw new exception("property $propertyName not found on object model");
    }

    abstract function Save();

    //ciruclar references request -> bid, bid -> request
    //don't allow?
    /**
     * @static
     * @param POG_Base $object
     * @param array $tablesAndColumns
     * @param null $parentTable
     * @param null $parentFK
     */
    private static function GetAllColumnsWithProperties($object, $depthLimit=-1, $tablesAndColumns = array(), $parentFK = null){
        if($depthLimit==0)
            return $tablesAndColumns;
        if(!empty($parentFK)){
            $joinClause = "{$parentFK}={$object->GetTableName()}.{$object->GetIdPropertyName()} ";
            $tablesAndColumns[get_class($object)] = array('JoinClause'=>$joinClause, 'Columns'=>self::GetAttributes($object));
        }
        else
            $tablesAndColumns[get_class($object)] = array('JoinClause'=>false,'Columns'=>self::GetAttributes($object));
        foreach($object->modelAssociation as $propertyId => $propertyObjectAssociation){
            if(!array_key_exists($propertyObjectAssociation['object'], $tablesAndColumns)){
                $objectModel = new $propertyObjectAssociation['object'];
                $foreignKey ="{$object->GetTableName()}.{$propertyId}";
                $tablesAndColumns = self::GetAllColumnsWithProperties($objectModel, $depthLimit-1, $tablesAndColumns, $foreignKey);
            }
        }
        return $tablesAndColumns;
    }

    /**
     * @static
     * @param POG_Base $pog_object
     * @param array $fetched_row
     * @return mixed
     */
    private static function PopulateObjectInDepth($pog_object, $fetched_row, $fkClause=''){
        $att = $pog_object->GetAttributes($pog_object);
        foreach ($att as $column)
        {
            $resultKeyName = strtolower($fkClause . $column);
            if(array_key_exists($resultKeyName, $fetched_row)){
                $pog_object->{$column} = $pog_object->Unescape($fetched_row[$resultKeyName]);
            }
            else
                return null;
        }
        foreach($pog_object->modelAssociation as $propertyId => $propertyObjectAssociation){
            $childObjectModel = new $propertyObjectAssociation['object'];
            $fkJoinClause = "{$pog_object->GetTableName()}.{$propertyId}={$childObjectModel->GetTableName()}.{$childObjectModel->GetIdPropertyName()} ";
            $pog_object->{$propertyObjectAssociation['property']}= self::PopulateObjectInDepth($childObjectModel, $fetched_row,$fkJoinClause);
        }
        return $pog_object;
    }

    //more than 1 layer of tables


    /**
     * Returns a sorted array of objects that match given conditions
     * @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...}
     * @param string $sortBy
     * @param boolean $ascending
     * @param int limit
     * @return array $buyerList
     */
    public function GetListInDepth($fcv_array = array(), $sortBy='', $ascending=true, $limit='', $depthLimit=-1)
    {
        $connection = Database::Connect();
        $sqlLimit = ($limit != '' ? "LIMIT $limit" : '');

        $columnsToFetch = '';
        $tablesWithJoinClauses='';
        $tableAndJoinColumns = self::GetAllColumnsWithProperties($this, $depthLimit);
        $pogAttributes=array();
        foreach ($tableAndJoinColumns as $objectClassName => $JoinClauseAndColumns){
            $objectModel = new $objectClassName;
            $pogAttributes=array_merge($pogAttributes,$objectModel->pog_attribute_type);
            $joinClause = $JoinClauseAndColumns['JoinClause'];
            if($joinClause ===false){
                $tablesWithJoinClauses.=" `{$objectModel->GetTableName()}` ";
            }
            else{
                $tablesWithJoinClauses.=" left Join `{$objectModel->GetTableName()}` on $joinClause ";
            }
            foreach($JoinClauseAndColumns['Columns'] as $column){
                $aliasedColumnName = strtolower($joinClause . $column);
                $columnsToFetch.="`{$objectModel->GetTableName()}`.`${column}` as `{$aliasedColumnName}`, ";
            }
        }
        $columnsToFetch = substr($columnsToFetch, 0, strlen($columnsToFetch)-2);

        $pog_query = "select $columnsToFetch from $tablesWithJoinClauses ";
        //array merge pog attribute type

        //todo set final values, make where clause work, test
        $objectModelList = Array();
        if (sizeof($fcv_array) > 0)
        {
            $pog_query .= " where ";
            for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
            {
                if (sizeof($fcv_array[$i]) == 1)
                {
                    $pog_query .= " ".$fcv_array[$i][0]." ";
                    continue;
                }
                else
                {
                    if ($i > 0 && sizeof($fcv_array[$i-1]) != 1)
                    {
                        $pog_query .= " AND ";
                    }
                    if (isset($pogAttributes[$fcv_array[$i][0]]['db_attributes']) && $pogAttributes[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $pogAttributes[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
                    {
                        if ($GLOBALS['configuration']['db_encoding'] == 1)
                        {
                            $value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : "'".$fcv_array[$i][2]."'";
                            $pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
                        }
                        else
                        {
                            $value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$this->Escape($fcv_array[$i][2])."'";
                            $pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
                        }
                    }
                    else
                    {
                        $value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$fcv_array[$i][2]."'";
                        $pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
                    }
                }
            }
        }
        if ($sortBy != '')
        {
            if (isset($pogAttributes[$sortBy]['db_attributes']) && $pogAttributes[$sortBy]['db_attributes'][0] != 'NUMERIC' && $pogAttributes[$sortBy]['db_attributes'][0] != 'SET')
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
            $sortBy = $this->GetIdPropertyName();
        }
        $pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
        $this->pog_query = $pog_query;
        $thisObjectName = get_class($this);

        $cursor = Database::Reader($pog_query, $connection);
        $objectList = array();
        if($cursor==null){
            print_r($pog_query);
            throw new exception('The Query Failed');
        }
        while ($row = Database::Read($cursor))
        {
            $object = new $thisObjectName;
            $objectList[]=self::PopulateObjectInDepth($object,$row);
        }
        return $objectList;
    }
}
?>