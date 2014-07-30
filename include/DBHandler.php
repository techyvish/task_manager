<?php
/**
 * Created by PhpStorm.
 * User: Vishal
 * Date: 28/07/2014
 * Time: 1:40 PM
 */

require 'medoo.php';


class DBHandler
{

    private $conn;
    private $database;

    function __construct()
    {
        //var_dump(dirname(__FILE__) . '/DBConnect.php');
        //require_once dirname(__FILE__) . '/DBConnect.php';

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

    public function createUser($name, $email, $password)
    {
        if (!$this->isUserExist($email)) {

            $password_hash = $password;
            $api_key = $this->generateApiKey();

            $last_user_id = $this->database->insert("users", [
                "name" => $name,
                "email" => $email,
                "password_hash" => $password_hash,
                "api_key" => $api_key,
                "status" => 1
            ]);

            if ($last_user_id != false) {
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
        return USER_CREATE_FAILED;
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExist($email)
    {
        $name = NULL;
        $name = $this->database->get("users", "name", ["email" => $email]);
        if ($name != FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }

    public function  checkLogin($email, $password)
    {
        $password_hash = NULL;
        $password_hash = $this->database->get("users", "password_hash", ["email" => $email]);
        if ($password_hash != FALSE) {
            if (($password == $password_hash)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function  getUserByEmail($email)
    {
        $name = NULL;
        $name = $this->database->get("users", "name", ["email" => $email]);
        if ($name != FALSE) {
            return $name;
        } else {
            return NULL;
        }
    }

    public function getApiKeyById($user_id)
    {
        $api_key = NULL;
        $api_key = $this->database->get("users", "api_key", ["id" => $user_id]);
        if ($api_key != FALSE) {
            return $api_key;
        } else {
            return NULL;
        }
    }

    public function getUserId($api_key)
    {
        $user_id = NULL;
        $user_id = $this->database->get("users", "id", ["api_key" => $api_key]);
        if ($user_id != FALSE) {
            return $user_id;
        } else {
            return NULL;
        }
    }

    public function isValidApiKey($api_key)
    {
        $user_id = NULL;
        $user_id = $this->database->get("users", "id", ["api_key" => $api_key]);
        if ($user_id != FALSE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function createTask($user_id, $task)
    {
        $task_id = NULL;
        $this->database->insert("tasks",
            ["task" => $task]);

        $task_id = $this->database->get("tasks", "id", ["task" => $task]);

        if ($task_id != FALSE) {
            $name = $this->database->get("users", "name", ["id" => $user_id]);
            if ($name != FALSE) {
                $this->database->insert("user_tasks",
                    ["user_id" => $user_id,
                        "task_id" => $task_id]);
                return $task_id;
            } else {
                return NULL;
            }
        } else {
            var_dump("created task not found");
            return NULL;
        }
    }

    public function getTask($task_number, $user_id)
    {
        /*  Query :
         *  SELECT *
            FROM `tasks`
            LEFT JOIN `user_tasks` ON `tasks`.`id` = `user_tasks`.`task_id`
            WHERE
 	            `user_tasks`.`user_id` = 1
            AND
 	        `tasks`.`id` = 1
         */

        $result = $this->database->select("tasks", [
                "[>]user_tasks" => ["id" => "task_id"]],

            ["tasks.id",
                "tasks.status",
                "tasks.task",
                "tasks.created_at"],

            ["AND" =>
                ["user_tasks.user_id" => $user_id,
                    "tasks.id" => $task_number]]);


        if ($result != NULL) {
            var_dump($result[0]["task"]);
            return $result[0]["task"];
        } else {
            return NULL;
        }
    }

    public function  getAllUserTasks($user_id)
    {
        $result = $this->database->select("tasks", [
                "[>]user_tasks" => ["id" => "task_id"]],

            ["tasks.id",
                "tasks.status",
                "tasks.task",
                "tasks.created_at"],

            ["AND" =>
                ["user_tasks.user_id" => $user_id]]);

        if ($result != NULL) {
            var_dump($result[0]);
            return $result[0];
        } else {
            return NULL;
        }
    }
}

//$handler = new DBHandler();
//$handler->createUser('test.user','test.user@gmail.com','test123');
//$handler->checkLogin('test.user@gmail.com','test123');
//$handler->getUserByEmail('vis8051@gmail.com');
//$handler->getApiKeyById(1);
//$handler->getUserId("d3de848e17b474ef2965301d3b9ba817");
//$handler->isValidApiKey("d3de848e17b474ef2965301d3b9ba817");
//$handler->createTask(1,"Get Milk");
//$handler->getTask(1,1);
//$handler->getAllUserTasks(1);