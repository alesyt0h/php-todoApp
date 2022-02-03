<?php

class UserModel extends Model {

    public function __construct(){
        Model::__construct();
        $this->_setCollection('users');
    }

    public function checkCredentials(string $username, string $password){

        $match = false;

        $user = $this->getOne('username', $username);
        $match = password_verify($password, $user['password']);

        return ($match) ? $user : false;
    }

    public function userExists(string $username){

        $userExists = $this->getOne('username', $username);

        return $userExists;
    }

    public function mailExists(string $email){

        $mailExists = $this->getOne('email', $email);;

        return $mailExists;
    }

    public function insertUser(string $username, string $password, string $email){

        $newUser = [
            "username" => $username,
            "password" => $password,
            "email" => $email,
            "registerDate" => date('Y-m-d H:i:s'),
            "createdTodos" => 0,
            "avatarUrl" => null
        ];

        $id = get_object_vars($this->insertOne($newUser));
        $newUser['id'] = $id['oid'];

        return $newUser;
    }

    public function modifyUser(string $email, string $password, string|null $avatar, int $count = 0){

        $user = $_SESSION['loggedUser'];

        if(!$user) return;

        $user['email'] = $email;
        $user['password'] = $password;
        $user['avatarUrl'] = $avatar;
        $user['createdTodos'] += $count;

        $equals = ($user === $_SESSION['loggedUser']) ? true : false; 

        $result = [ 'status' => $this->modifyOne($user), 
                    'equals' => $equals ];

        if ($result['status']) $_SESSION['loggedUser'] = $user;

        return $result;
    }

}

?>