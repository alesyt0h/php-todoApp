<?php

class UserModel extends Model {

    public function checkCredentials(string $username, string $password){
        $match = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['username']) === $username && $this->_users[$i]['password'] === $password){
                $match = true;
                $this->_loggedUser = $this->_users[$i];
            }
        }

        return $match;
    }

    public function checkUsername(string $username){

        $userTaken = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['username']) === $username){
                $userTaken = true;
            }
        }

        return $userTaken;
    }

    public function getLoggedUser(){
        return $this->_loggedUser;
    }

    public function purgeModelUser(){
        $this->_loggedUser = [];
    }

}


?>