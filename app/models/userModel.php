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

    public function userExists(string $username){

        $userExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['username']) === $username){
                $userExists = true;
            }
        }

        return $userExists;
    }

    public function mailExists(string $email){

        $mailExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['email']) === $email){
                $mailExists = true;
            }
        }
        return $mailExists;
    }

    public function insertUser(string $username, string $password, string $email){

        $id = $this->getLastUserId();

        $newUser = [
            "id" => $id,
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "isVerified" => false,
            "registeredDate" => date('c'),
            "createdTodos" => 0
        ];

        array_push($this->_users, $newUser);
        $this->_loggedUser = $newUser;

        return $this->writeJSON('users');
    }

    public function getLastUserId(){

        $lastId = 0;

        for ($i=0; $i < count($this->_users); $i++) { 
            if($lastId < $this->_users[$i]['id']){
                $lastId = $this->_users[$i]['id'];
            }
        }

        return $lastId + 1;
    }

    public function getLoggedUser(){
        return $this->_loggedUser;
    }

    public function purgeModelUser(){
        $this->_loggedUser = [];
    }

}

?>