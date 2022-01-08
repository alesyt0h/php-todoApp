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

        }

    }

    public function emailProcedure(){

        $currentMail = $_SESSION['loggedUser']['email'];
        $newMail = $_POST['email'];

        if($newMail === $currentMail){
            return $currentMail;
        }

        $mailExists = $this->userDB->mailExists($newMail);

        if($mailExists){
            $this->view->modifyMsg .= 'Email already exists, choose another';
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

        if(!password_verify($formPassword, $currentPassword)){
            $this->view->modifyMsg .= 'Current password is incorrect';
            return $currentPassword;
        }

        if($newPassword !== $confirmPassword){
            $this->view->modifyMsg .= 'New password don\'t match';
            return $currentPassword;
        }

        return $newPassword;

    }

    public function avatarProcedure(){

        $currentAvatar = $_SESSION['loggedUser']['avatarUrl'];
        $newAvatar = $_POST['avatarUrl'];

        if(!preg_match('/(https?:\/\/|www\.)/', $newAvatar)){
            $this->view->modifyMsg .= 'Avatar URL is not a valid URL!';
            return $currentAvatar;
        }

        return $newAvatar;

    }

}

?>