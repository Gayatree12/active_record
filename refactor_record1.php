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
?>