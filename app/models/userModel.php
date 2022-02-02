<?php

class UserModel extends Model {

    public function checkCredentials(string $username, string $password){

        $this->getUsers();

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

        $this->_users = [];

        return $match;
    }

    public function userExists(string $username){

        $this->getUsers();

        $userExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['username']) === $username){
                $userExists = true;
                break;
            }
        }

        $this->_users = [];

        return $userExists;
    }

    public function mailExists(string $email){

        $this->getUsers();

        $mailExists = false;

        for ($i=0; $i < count($this->_users); $i++) { 
            if(strtolower($this->_users[$i]['email']) === $email){
                $mailExists = true;
                break;
            }
        }

        $this->_users = [];

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

        $this->_loggedUser = $newUser;

        return $this->writeJSON('users', $newUser);
    }

    public function modifyUser(int $userId, string $email, string $password, string|null $avatar, int $count = 0){

        $this->getUsers();
        $this->user = $this->findOneById($userId, 'users');

        if(!$this->user) return;

        $this->user['email'] = $email;
        $this->user['password'] = $password;
        $this->user['avatarUrl'] = $avatar;
        $this->user['createdTodos'] += $count;
        
        $fullUsers = array_map( function($oldUser){ 
            return ($oldUser['id'] === $this->user['id']) ? $this->user : $oldUser;
        }, $this->_users);

        $equals = ($this->user === $_SESSION['loggedUser']) ? true : false; 

        $_SESSION['loggedUser'] = $this->user;

        $result = [ 'status' => $this->writeJSON('users', $fullUsers, true), 
                    'equals' => $equals ];

        $this->_users = [];

        return $result;
    }

    public function getLastUserId(){

        $this->getUsers();
        
        if(!count($this->_users)) return 1;
        
        // Reverse lookup, since users aren't deleted, this should be faster and efficient
        for ($i= count($this->_users) - 1; $i < count($this->_users); $i--) { 
            $lastId = $this->_users[$i]['id'];
            break;
        }

        $this->_users = [];
        
        return $lastId + 1;
    }

    public function getLoggedUser(){
        return $this->_loggedUser;
    }
    
    protected function getUsers(){
        $this->parseJSON('users');
        $this->fetchUsers();
    }

    public function purgeModelUser(){
        $this->_loggedUser = [];
    }

}

?>