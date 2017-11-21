<?php
/**Created by MobaTextEditor
   User: gt86
**/
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ERROR|E_PARSE);
//constants
define('DATABASE','gt86');
define('USERNAME','gt86');
define('PASSWORD','RAFRIUUsM');
define('SERVER','sql2.njit.edu');

class dbConn{
    //variable to hold connection object
    protected static $db;
  
    private function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO('mysql:host=' . SERVER .';dbname=' . DATABASE, USERNAME, PASSWORD);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            //display error if connection doesn't exist
            echo "Connection Error: " . $e->getMessage();
        }
    }
    
    public static function getConnection() {
        //new connection created if there is no existing connection
        if (!self::$db) {
            //new connection object
            new dbConn();
        }
        //return connection
        return self::$db;
    }
}
class collection {
protected $html;
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
      $db = dbConn::getConnection();
      $tableName = get_called_class();
      $sql = 'SELECT * FROM ' . $tableName;
      $statement = $db->prepare($sql);
      $statement->execute();
      $class = static::$modelName;
      $statement->setFetchMode(PDO::FETCH_CLASS, $class);
      $recordsSet =  $statement->fetchAll();
      return $recordsSet;
    }
    static public function findOne($id) {
      $db = dbConn::getConnection();
      $tableName = get_called_class();
      $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
      $statement = $db->prepare($sql);
      $statement->execute();
      $class = static::$modelName;
      $statement->setFetchMode(PDO::FETCH_CLASS, $class);
      $recordsSet =  $statement->fetchAll();
      return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {

protected $tableName;

public function save() {
        if ($this->id != '') {
            $sql = $this->update($this->id);
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    
    private function insert() {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }

    private function update($id) {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if(! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
    
    public function delete($id) {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
    $tableName='accounts';
    return $tableName;
    }
}

class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
    $tableName='todos';
    return $tableName;
    }
}
/********************Accounts Table********************************/
echo '<h1><u>Accounts Table</u></h1>';
/********************Search for all records in accounts table***********************/
echo '<h2>1. Search for all records in Accounts Table</h2>';
echo '<h3>Display All records:</h3>';

$records = accounts::findAll();
 //using html table to display records  
  $html = '<table border = 1>'; 
  $html .= '<tr>'; //table row
    foreach($records[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>'; //table head
        }     
    $html .= '</tr>';
    
    foreach($records as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br>' . '</td>'; //table data
        }
        $html .= '</tr>';           
    }
    $html .= '</table>';
    print_r($html); //print html table
 
/****************Search a record by its unique id in accounts table*******************/  
    echo '<h2>2. Search table by unique id</h2>';
    echo '<h3>Search by id: 11</h3>';
    
    $record = accounts::findOne(11);
  //display contents in html table
    $html = '<table border = 1>';
    $html .= '<tr>';  //table row
    
    foreach($record[0]as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';  //table head
        }
       
    $html .= '</tr>';   
    
    foreach($record as $key=>$value) {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';  //table data
        } 
        $html .= '</tr>';          
    }
    $html .= '</table>';  
    print_r($html);  //print html table

/***********************Insert records into accounts table*************************/
echo '<h2>3. Insert records into table</h2>';
echo '<h3>Insert new record: gaya123@njit.edu, gaya, tree, 889-000-1111, 1990-07-09, female, 6555</h3>';
$record = new account();
$record->email="gaya123@njit.edu";
$record->fname="gaya";
$record->lname="tree";
$record->phone="889-000-1111";
$record->birthday="1990-07-09";
$record->gender="female";
$record->password="6555";
$record->save(); //saving the inserted record
$records = accounts::findAll(); //finding all the records of accounts table

//display records in html table
$html = '<table border = 1>';
$html .= '<tr>';
    foreach($records[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($records as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</table>';
    print_r($html);
    /*************************Delete records from accounts table*****************************/ 
    echo '<h2>4. Delete One Record from the accounts table</h2>';
    echo '<h3>Delete record: 9<h3>';

    $record = new account();
    $id = 9;
    $record->delete($id);
    $record = accounts::findAll();
    //display records in html table
    $html = '<table border = 1>';
    $html .= '<tr>';  //table row
    
    foreach($record[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';  //tab;e head
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';  //table data
        }
        $html .= '</tr>';      
    }
    $html .= '</table>';
    print_r($html);
/*********************Updating a record in accounts table************************/
    echo '<h2>5. Update a record in table </h2>';
    echo '<h3>Update record: 10</h3>';
    $id=10;
    $record = new account();
    $record->id=$id;
    $record->email="new@gmail.com";
    $record->fname="rose";
    $record->lname="mary";
    $record->gender="female";
    $record->save();
    $record = accounts::findAll();
        
    $html = '<table border = 1>'; 
    $html .= '<tr>';
    
    foreach($record[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }   
    $html .= '</tr>';
    
    foreach($record as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';            
    }
    $html .= '</table>';
    print_r($html);
    echo '<hr>';  //horizontal rule
/**************************************Todos Table*****************************************/
    echo '<h1><u>Todos Table</u></h1>';

/*****************Search all records in Todos table**********************************/
    echo '<h2>1. Search for all records in todos table</h2>';
    echo '<h3>Display all records:</h3>';
    $records = todos::findAll();
 //displaying records in html table  
    $html = '<table border = 1>';
    $html .= '<tr>'; //table row
    foreach($records[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }     
    $html .= '</tr>';

    foreach($records as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';      
    }
    $html .= '</table>';
    print_r($html);
/*************************Searching a record by its unique id in todos table***************************/
    echo '<h2>2. Search by unique id</h2>';
    echo '<h3>Search a record by id: 2</h3>';
    $record = todos::findOne(2);  
    $html = '<table border = 1>';
    $html .= '<tr>';
    
    foreach($record[0]as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
      
    $html .= '</tr>';
       
    foreach($record as $key=>$value) {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';     
    }
    $html .= '</table>';    
    print_r($html);
/*********************Insert a record in todos table************************/
    echo '<h2>3. Insert Records in Todos table</h2>';
    echo '<h3>Insert new record: gt@njit.edu, 35, 10-07-2017, 09-14-2017, Record Created, 1</h3>';
    $record = new todo();
    $record->owneremail="gt@njit.edu";
    $record->ownerid=35;
    $record->createddate="10-07-2017";
    $record->duedate="09-14-2017";
    $record->message="Record Created";
    $record->isdone=1;
    $record->save(); //saving record
    $records = todos::findAll();
 
    $html = '<table border = 1>';
    $html .= '<tr>';
    
    foreach($records[0] as $key=>$value) {
        $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }       
        $html .= '</tr>';
    
    foreach($records as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</table>';
    print_r($html);
/***************Delete record from todos table****************/
    echo '<h2>4. Delete one record from table</h2>';
    echo '<h3>Delete record: 1</h3>';
    $record= new todo();
    $id=1;
    $record->delete($id);
    $record = todos::findAll();
    $html = '<table border = 1>';  
    $html .= '<tr>';
    
    foreach($record[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';   
    }
    
    $html .= '</table>';
    print_r($html);
/*******************Updating a record in todos table**********************/
    echo '<h2>5. Update One Record in table</h2>';
    echo '<h3>Update a record with id: 5</h3>';
    $id=5;
    $record = new todo();
    $record->id=$id;
    $record->owneremail="gt@gmail.com";
    $record->ownerid="87";
    $record->createddate="2017-09-01 00:00:00";
    $record->duedate="2017-07-08 00:00:00";
    $record->message="Updated";
    $record->isdone="1";
    $record->save();
    $record = todos::findAll();
        
    $html = '<table border = 1>';  
    $html .= '<tr>';
    
    foreach($record[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';    
    
    foreach($record as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';            
    }
    $html .= '</table>';
    print_r($html);
?>