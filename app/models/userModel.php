<?php

class UserModel extends Model {

    public function checkCredentials(string $username, string $password){
        $match = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            
            if($this->_users[$i]['username'] === $username){

                $validation = password_verify($password, $this->_users[$i]['password']);

                if($validation){
                    $this->_loggedUser = $this->_users[$i];
                    $match = true;
                    break;
                } 
            }
        }

        return $match;
    }

    public function userExists(string $username){

        $userExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['username']) === $username){
                $userExists = true;
                break;
            }
        }

        return $userExists;
    }

    public function mailExists(string $email){

        $mailExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['email']) === $email){
                $mailExists = true;
                break;
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
            "registerDate" => date('c'),
            "createdTodos" => 0,
            "avatarUrl" => null
        ];

        array_push($this->_users, $newUser);
        $this->_loggedUser = $newUser;

        return $this->writeJSON('users');
    }

    public function modifyUser(int $userId, string $email, string $password, string|null $avatar, int $count = 0){

        $this->user = $this->findOneById($userId, 'users');

        $this->user['email'] = $email;
        $this->user['password'] = $password;
        $this->user['avatarUrl'] = $avatar;
        $this->user['createdTodos'] += $count;
        
        $this->_users = array_map( function($oldUser){ 
            return ($oldUser['id'] === $this->user['id']) ? $this->user : $oldUser;
        }, $this->_users);

        $equals = ($this->user === $_SESSION['loggedUser']) ? true : false; 

        $_SESSION['loggedUser'] = $this->user;

        return [ 'status' => $this->writeJSON('users'), 
                 'equals' => $equals ];
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