<?php

class UserModel extends Model {

    public function __construct(){
        Model::__construct();
        $this->_setTable('users');
    }

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

        $userExists = $this->fetchOne($username, 'username');

        return $userExists;
    }

    public function mailExists(string $email){

        $mailExists = $this->fetchOne($email, 'email');

        return $mailExists;
    }

    public function insertUser(string $username, string $password, string $email){

        $newUser = [
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "register_date" => date('Y-m-d H:i:s'),
            "created_todos" => 0,
            "avatar_url" => null
        ];

        $this->_loggedUser = $newUser;

        return $this->save($newUser);
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

    public function getLoggedUser(){
        return $this->_loggedUser;
    }

    public function purgeModelUser(){
        $this->_loggedUser = [];
    }

}

?>