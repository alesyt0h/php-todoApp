<?php

class UserController extends ApplicationController{

    public function __construct(){
        parent::__construct();
    }

    public function profileAction(){

        if(!isset($_SESSION['loggedUser'])){
            header('Location: ' . WEB_ROOT);
            die();
        }

        $this->view->user = $_SESSION['loggedUser'];
        
        if(count($_POST) > 0){
            
            $userId = $_SESSION['loggedUser']['id'];
            $email = $this->emailProcedure();
            $password = $this->passwordProcedure();
            $avatarUrl = $this->avatarProcedure();
            
            $result = $this->userDB->modifyUser($userId, $email, $password, $avatarUrl);

            if($result['status']){

                ($result['equals']) ? null : $_SESSION['successMsg'] = 'Profile updated!';

                header('Location: ' . WEB_ROOT . '/user/profile');
                die();
            }
        }

    }

    public function emailProcedure(){

        $currentMail = $_SESSION['loggedUser']['email'];
        $newMail = $_POST['email'];
        $emailPattern = '/^[a-z0-9._%+-]+@[a-z0-9.-]{2,}\\.[a-z]{2,4}$/';

        if($newMail === $currentMail){
            return $currentMail;
        }
        
        if(!strlen(trim($newMail))){
            $_SESSION['modifyMsg'] .= 'Email can not be empty!<br>';
            return $currentMail;
        }

        if(!preg_match($emailPattern, $newMail)){
            $_SESSION['modifyMsg'] .= 'Please introduce a valid email';
            return $currentMail;
        }

        $mailExists = $this->userDB->mailExists($newMail);

        if($mailExists){
            $_SESSION['modifyMsg'] .= 'Email already exists, choose another<br>';
            return $currentMail;
        } else {
            return $newMail;
        }

    }

    public function passwordProcedure(){

        $currentPassword = $_SESSION['loggedUser']['password'];
        
        $formPassword = $_POST['password'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if(!strlen(trim($formPassword))){
            return $currentPassword;
        }

        if(strlen($newPassword) < 6){
            $_SESSION['modifyMsg'] .= 'Password must have at least 6 characters!';
            return $currentPassword;
        }

        if(!password_verify($formPassword, $currentPassword)){
            $_SESSION['modifyMsg'] .= 'Current password is incorrect<br>';
            return $currentPassword;
        }

        if($newPassword !== $confirmPassword || !strlen(trim($newPassword))){
            $_SESSION['modifyMsg'] .= 'New password don\'t match';
            return $currentPassword;
        }

        return password_hash($newPassword, PASSWORD_DEFAULT);

    }

    public function avatarProcedure(){

        $currentAvatar = $_SESSION['loggedUser']['avatarUrl'];
        $newAvatar = $_POST['avatarUrl'];

        if(!strlen(trim($newAvatar))){
            return null;
        }

        if(!preg_match('/(https?:\/\/|www\.)/', $newAvatar)){
            $_SESSION['modifyMsg'] .= 'Avatar URL is not a valid URL!<br>';
            return $currentAvatar;
        }

        // ! Requires openSSL - extension=php_openssl.dll
        $result = @getimagesize($newAvatar);
        $result = ($result && strtolower(substr($result['mime'], 0, 5)) == 'image' ? true : false);

        if(extension_loaded('openssl')){
            if(!$result){
                $_SESSION['modifyMsg'] .= 'The Avatar URL you entered is not a valid image!<br>';
                return $currentAvatar;
            }
        }

        return $newAvatar;

    }

}

?>