<?php
/**Created by MobaTextEditor
   User: gt86
**/
//turn on debugging messages
//ini_set('display_errors', 'On');
//error_reporting(E_ERROR|E_PARSE);
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
        //new connection creation if there is no existing connection
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

echo "<h1>Accounts Table</h1>";
echo "<h2>Search for all records in Accounts Table</h2>";
$records = accounts::findAll();
 //using html table to display records  
  $html = '<table border = 3><tbody>'; 
  $html .= '<tr>'; //table row
    foreach($records[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }     
    $html .= '</tr>';
    
    foreach($records as $key=>$value) {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br>' . '</td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</tbody></table>';
    print_r($html);
  
    echo "<h2>Search account table by unique id</h2>";
   $record = accounts::findOne(11);
  
  $html = '<table border = 3><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';   
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';          
    }
    $html .= '</tbody></table>';
    
    print_r($html);

echo "<h2>Insert One Record in accounts table</h2>";
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
$html = '<table border = 3><tbody>';
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
    $html .= '</tbody></table>';
echo "<h3>After Inserting a record in table</h3>";
print_r($html);
//delete record from todos 
echo "<h2>Delete One Record from the table</h2>";
$record= new account();
$id=6;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';

$record = accounts::findAll();

$html = '<table border = 5><tbody>';
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
    $html .= '</tbody></table>';
echo "<h3>After Deleting</h3>";
print_r($html);
//updating a record in accounts table
echo "<h2>Update One Record with id = 10</h2>";
$id=10;
$record = new account();
$record->id=$id;
$record->email="new@gmail.com";
$record->fname="rose";
$record->lname="mary";
$record->gender="female";
$record->save();
$record = accounts::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 3><tbody>'; 
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
    $html .= '</tbody></table>';
 
 print_r($html);

 echo"<h1>Todos Table</h1>";
 echo "<h2>Search for all records in todos table</h2>";
 $records = todos::findAll();
 //displaying records in html table  
  $html = '<table border = 3><tbody>';
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
    $html .= '</tbody></table>';
    print_r($html);
//searching a record by its unique id
    echo "<h2>Search by unique id</h2>";
    echo "<h3>Searching a record by its unique id 2</h3>";
  $record = todos::findOne(2);  
  $html = '<table border = 3><tbody>';
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
    $html .= '</tbody></table>';    
    print_r($html);
//insert a record in todos table
   echo "<h2>Insert One Record in table</h2>";
        $record = new todo();
        $record->owneremail="gt@njit.edu";
        $record->ownerid=35;
        $record->createddate="10-07-2017";
        $record->duedate="09-14-2017";
        $record->message="Record Created";
        $record->isdone=1;
        $record->save(); //saving record
        $records = todos::findAll();
        echo"<h2>After Inserting a record in table</h2>";
 
     $html = '<table border = 3><tbody>';
     $html .= '<tr>';
      foreach($records[0] as $key=>$value) {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }       
    $html .= '</tr>';
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2) {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</tbody></table>';

print_r($html);
//delete record from todos table
echo "<h2>Delete one record from table</h2>";
$record= new todo();
$id=1;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';
$record = todos::findAll();
$html = '<table border = 3><tbody>';  
$html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';   
    }
    
    $html .= '</tbody></table>';
echo "<h3>After Deleting a record from table</h3>";
print_r($html);

echo "<h2>Update One Record in table</h2>";
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
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 3><tbody>';
  
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
    $html .= '</tbody></table>';
 
 print_r($html);
?>