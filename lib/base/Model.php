<?php

/**
 * Model for creating and parsing JSON files
 * 
 * @author alesyt0h
 * @since 2022-01-03
 */

class Model {

    protected $_jsonData = [];
    protected $_users = [];
    protected $_todos = [];

    protected $_loggedUser;

    protected $dbDir = ROOT_PATH . '/db/';

    public function __construct(){
        // Parse the DB's
        $this->parseJSON('todos');
        $this->_todos = $this->fetchTodos();
        $this->purgeTodos();

        $this->parseJSON('users');
        $this->_users = $this->fetchUsers();
    }

    /**
     * Parses the given JSON file and stores it on $_jsonData
     * @param string the file to parse. Do not include '.json' in the filename
     */
    protected function parseJSON(string $file){
        if(substr($file, -5) !== '.json'){
            $file .= '.json';
        }

        if(!file_exists($this->dbDir . $file)){
            throw new Exception('JSON File (' . $file . ') doesn\'t exist in db folder!');
        }

        $jsonRaw = @file_get_contents($this->dbDir . $file);

        if($jsonRaw === false){
            $err = error_get_last();
            throw new Exception($err['message']);
        }

        $this->_jsonData = json_decode($jsonRaw, true);
    }

    /**
     * Writes the current array state to the respective JSON database
     * @param string $db the database being written. Only users & todos are valid values
     */
    public function writeJSON(string $db){
        $db = $this->dbChecker($db);

        if(!count($this->$db)){
            return;
        }

        $rawData = json_encode($this->$db, JSON_PRETTY_PRINT);

        $result = @file_put_contents($this->dbDir . substr($db, 1) . '.json', $rawData);

        if($result === false){
            $err = error_get_last();
            throw new Exception($err['message']);
        }

        return $result;
    }

    /**
     * Checks if DB is the correct type
     * @param string $db the database
     * @return exception|string returns an exception if DB wasn't users or todos, else append _ to the DB name and returns it 
     */
    protected function dbChecker(string $db){
        return ($db !== 'users' && $db !== 'todos') ? throw new Exception('Not a valid Database!') : $db = '_' . $db; 
    }

    /**
     * Finds and return the match of the given ID
     * @param int $id the id of the todo or user
     * @param string $db the database to search for. Only users & todos are valid values
     */
    public function findOneById(int $id, string $db){

        $db = $this->dbChecker($db);

        $result = [];

        for ($i=0; $i < count($this->$db); $i++) { 
            if($this->$db[$i]['id'] === $id){
                $result = $this->$db[$i];
                break;
            }
        }

        return $result;
    }

    /**
     * Filters and removes the created Todo's by temp users that have more than 24h
     */
    protected function purgeTodos(){

        $this->_todos = array_filter($this->_todos, function($todo){
            if(!$todo['createdBy']){
                $now = new DateTime();
                $todoDate = new DateTime($todo['createdAt']);
    
                $dayPassed = date_diff($now, $todoDate);
    
                if(!$dayPassed->days){
                    return $todo;
                }
            } else {
                return $todo;
            }
        });

        $this->_todos = array_splice($this->_todos, 0);

        $this->writeJSON('todos');
    }

    protected function fetchUsers(){
        $this->_users = $this->_jsonData ?? [];
        $this->_jsonData = [];

        return $this->_users;
    }

    protected function fetchTodos(){
        $this->_todos = $this->_jsonData ?? [];
        $this->_jsonData = [];

        return $this->_todos;
    }

}

?>