<?php
/**
 * Created by PhpStorm.
 * User: Vishal
 * Date: 28/07/2014
 * Time: 1:40 PM
 */


class DBHandler {

    private  $conn;

    function __construct()
    {
        var_dump(dirname(__FILE__) . '/DBConnect.php');
        require_once dirname(__FILE__) . '/DBConnect.php';

        $db = new DbConnect();
        $this->conn = $db->connect();
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

}

$handler = new DBHandler();
$handler->createUser('test.user','test.user@gmail.com','test123');