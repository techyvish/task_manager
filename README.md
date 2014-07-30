

Task Manager
============

RestFUL APIs for task manager App
---------------------------------

> Back-end for taskmanager App writtend in PHP.

Table creation for storing tasks
--------------------------------

![enter image description here][1]


```sql

CREATE DATABASE task_manager;

USE task_manager;
 
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);
 
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);
 
CREATE TABLE IF NOT EXISTS `user_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`)
);
 
ALTER TABLE  `user_tasks` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `task_manager`.`users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
 
ALTER TABLE  `user_tasks` ADD FOREIGN KEY (  `task_id` ) REFERENCES  `task_manager`.`tasks` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
```

APIs
----


| URL           | Method        | Parameters    | Description  |
| ------------- |-------------  | ------------- | -------------|
| /register |	POST|	name, email, password|	User registration|
|/login|	POST|	email, password	User |login
|/tasks	|POST|	task|	To create new task
|/tasks	|GET	|	Fetching all| tasks
|/tasks/:id|	GET	|	Fetching single |task
|/tasks/:id|	PUT	|	Updating single |task
|/tasks/:id|	DELETE|	task, status	|Deleting single task



How to setup
------------

1. Install MAMP on your mac.
2. Turn on the server.
2. Download and add this repo to htdocs folder.
3. Test APIs using any rest client.

Frameworks
----------

1. Slim  http://www.slimframework.com/
2. Medoo http://medoo.in/

  [1]: http://www.androidhive.info/wp-content/uploads/2014/01/android-task-manager-rest-api-database.jpg?0921ab
