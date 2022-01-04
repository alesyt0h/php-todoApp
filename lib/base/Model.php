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

    protected $dbDir = ROOT_PATH . '/db/';

    public function __construct(){

    }

    public function parseJSON(string $file){

        if(substr($file, -5) !== '.json'){
            $file .= '.json';
        }

        if(!file_exists($this->dbDir . $file)){
            throw new Exception('JSON File (' . $file . ') doesn\'t exist in db folder!');
        }

        $jsonRaw = file_get_contents($this->dbDir . $file);
        $this->_jsonData = json_decode($jsonRaw, true);

        return $this->_jsonData;
    }

    public function findOneById(int $id){

        $result = [];

        for ($i=0; $i < count($this->_jsonData); $i++) { 
            if($this->_jsonData[$i]['id'] === $id){
                $result = $this->_jsonData[$i];
            }
        }

        return $result;

    }

}



?>