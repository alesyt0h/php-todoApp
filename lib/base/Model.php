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

    protected $dbDir = ROOT_PATH . '/db/';

    public function __construct(){
        $this->parseJSON('todos');
        $this->_todos = $this->fetchTodos();

        $this->parseJSON('users');
        $this->_users = $this->fetchUsers();
    }

    /**
     * Parses the given JSON file
     * @param string the file to parse. Do not include '.json' in the filename
     */
    public function parseJSON(string $file){
        if(substr($file, -5) !== '.json'){
            $file .= '.json';
        }

        if(!file_exists($this->dbDir . $file)){
            throw new Exception('JSON File (' . $file . ') doesn\'t exist in db folder!');
        }

        $jsonRaw = file_get_contents($this->dbDir . $file);
        $this->_jsonData = json_decode($jsonRaw, true);
    }

    /**
     * Writes the given array to the respective JSON database
     * @param array $data the array data to append to the JSON database
     * @param string $db the database being written. Only users & todos are valid values
     */
    public function writeJSON(array $data, string $db){

        $db = $this->dbChecker($db);

        array_push($this->$db, $data);
        $rawData = json_encode($this->$db, JSON_PRETTY_PRINT);

        file_put_contents($this->dbDir . substr($db, 1) . '.json', $rawData);
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
            }
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
    

    public function fetchUsers(){
        $this->_users = $this->_jsonData;
        $this->_jsonData = [];

        return $this->_users;
    }

    public function fetchTodos(){
        $this->_todos = $this->_jsonData;
        $this->_jsonData = [];

        return $this->_todos;
    }

}



?>