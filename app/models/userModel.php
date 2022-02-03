<?php

class UserModel extends Model {

    public function __construct(){
        Model::__construct();
        $this->_setTable('users');
    }

    public function checkCredentials(string $username, string $password){

        $match = false;

        $user = $this->fetchOne($username, 'username');
        $match = password_verify($password, $user['password']);

        if($match){
            $user['id'] = intval($user['id']);
            $user['created_todos'] = intval($user['created_todos']);
        }

        return ($match) ? $user : false;
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

        $newUser['id'] = intval($this->save($newUser));

        return $newUser;
    }

    public function modifyUser(string $email, string $password, string|null $avatar, int $count = 0){

        $user = $_SESSION['loggedUser'];

        if(!$user) return;

        $user['email'] = $email;
        $user['password'] = $password;
        $user['avatar_url'] = $avatar;
        $user['created_todos'] += $count;

        $equals = ($user === $_SESSION['loggedUser']) ? true : false; 

        $result = [ 'status' => $this->save($user), 
                    'equals' => $equals ];

        if ($result['status']) $_SESSION['loggedUser'] = $user;

        return $result;
    }

}

?>