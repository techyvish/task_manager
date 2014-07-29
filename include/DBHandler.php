<?php
/**
 * Created by PhpStorm.
 * User: Vishal
 * Date: 28/07/2014
 * Time: 1:40 PM
 */

require 'medoo.php';


class DBHandler {

    private  $conn;
    private  $database;

    function __construct()
    {
        var_dump(dirname(__FILE__) . '/DBConnect.php');
        require_once dirname(__FILE__) . '/DBConnect.php';

        $db = new DbConnect();
        $this->conn = $db->connect();


        $this->database = new medoo([
            // required
            'database_type' => 'mysql',
            'database_name' => 'task_manager',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'root',

            // optional
            'port' => 3306,
            'charset' => 'utf8',
            // driver_option for connection, read more from http://www.php.net/manual/en/pdo.setattribute.php
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ]
        ]);
    }

    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     * @return int
     */

    public  function createUser($name,$email,$password)
    {
        $response_arr = array();

        if ( !$this->isUserExist($email))
        {
            $password_hash = $password;
            $api_key = $this->generateApiKey();
            $stmt = $this->conn->prepare("INSERT INTO users(name,email,password_hash,api_key,status) values ( ?,?,?,?,1)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $api_key);

            $result = $stmt->execute();

            $stmt->close();

            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }

        return $response;
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExist($email) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }

    public  function  checkLogin($email,$password)
    {
        $password_hash = NULL;
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->bind_result($password_hash);
        $stmt->store_result();

        if ( $stmt->num_rows > 0)
        {
            $stmt->fetch();
            $stmt->close();

            if ( ($password == $password_hash))
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            $stmt->close();
            return FALSE;
        }

    }

    public  function  getUserByEmail($email)
    {
        $name = NULL;
        $name = $this->database->get("users","name",["email" => $email]);
        if ( $name != FALSE )
        {
            return $name;
        }
        else
        {
            return NULL;
        }
    }

    public function getApiKeyById($user_id)
    {
        $api_key = NULL;
        $api_key = $this->database->get("users","api_key",["id" => $user_id]);
        if ( $api_key != FALSE )
        {
            return $api_key;
        }
        else
        {
            return NULL;
        }
    }

    public function getUserId($api_key)
    {
        $user_id = NULL;
        $user_id = $this->database->get("users","id",["api_key" => $api_key]);
        if ( $user_id != FALSE )
        {
            return $user_id;
        }
        else
        {
            return NULL;
        }
    }

    public function isValidApiKey($api_key)
    {
        $user_id = NULL;
        $user_id = $this->database->get("users","id",["api_key" => $api_key]);
        if ( $user_id != FALSE )
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

}

$handler = new DBHandler();
//$handler->createUser('test.user','test.user@gmail.com','test123');
//$handler->checkLogin('test.user@gmail.com','test123');
//$handler->getUserByEmail('vis8051@gmail.com');
//$handler->getApiKeyById(1);
//$handler->getUserId("d3de848e17b474ef2965301d3b9ba817");
//$handler->isValidApiKey("d3de848e17b474ef2965301d3b9ba817");