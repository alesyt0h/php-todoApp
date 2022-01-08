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

            if($result){
                $_SESSION['successMsg'] = 'Profile updated!';
                header('Location: ' . WEB_ROOT . '/user/profile');
                die();
            }
        }

    }

    public function emailProcedure(){

        $currentMail = $_SESSION['loggedUser']['email'];
        $newMail = $_POST['email'];

        if($newMail === $currentMail){
            return $currentMail;
        }

        if(!strlen($newMail)){
            $this->view->modifyMsg .= 'Email can\'t be empty!<br>';
            return $currentMail;
        }

        $mailExists = $this->userDB->mailExists($newMail);

        if($mailExists){
            $this->view->modifyMsg .= 'Email already exists, choose another<br>';
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

        if(!strlen($formPassword)){
            return $currentPassword;
        }

        if(!password_verify($formPassword, $currentPassword)){
            $this->view->modifyMsg .= 'Current password is incorrect<br>';
            return $currentPassword;
        }

        if($newPassword !== $confirmPassword || !strlen($newPassword)){
            $this->view->modifyMsg .= 'New password don\'t match';
            return $currentPassword;
        }

        return password_hash($newPassword, PASSWORD_DEFAULT);

    }

    public function avatarProcedure(){

        $currentAvatar = $_SESSION['loggedUser']['avatarUrl'];
        $newAvatar = $_POST['avatarUrl'];

        if(!preg_match('/(https?:\/\/|www\.)/', $newAvatar) && strlen($newAvatar)){
            $this->view->modifyMsg .= 'Avatar URL is not a valid URL!<br>';
            return $currentAvatar;
        }

        return $newAvatar;

    }

}

?>