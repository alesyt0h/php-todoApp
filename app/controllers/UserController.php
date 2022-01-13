<?php

class UserController extends ApplicationController{

    private bool $isError;
    private string $errMsg;

    public function __construct(){
        parent::__construct();

        $this->isError = false;
        $this->errMsg = '';
    }

    public function profileAction(){

        if(!$this->isUser()) $this->redirect();

        $this->view->user = $_SESSION['loggedUser'];
        
        if(count($_POST) > 0){
            
            $userId = $_SESSION['loggedUser']['id'];
            $email = $this->emailProcedure();
            $password = $this->passwordProcedure();
            $avatarUrl = $this->avatarProcedure();
            
            if($this->isError){
                $this->appMsg('error', $this->errMsg);
                $this->redirect('/user/profile');
            }
            
            $result = $this->userDB->modifyUser($userId, $email, $password, $avatarUrl);
            
            if($result['status']){

                ($result['equals']) ? null : $this->appMsg('success','Profile updated!');

                $this->redirect('/user/profile');
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
            $this->isError = true;
            $this->errMsg .= 'Email can not be empty!<br>';
            return;
        }

        if(!preg_match($emailPattern, $newMail)){
            $this->isError = true;
            $this->errMsg .= 'Please introduce a valid email<br>';
            return;
        }

        $mailExists = $this->userDB->mailExists($newMail);

        if($mailExists){
            $this->isError = true;
            $this->errMsg .= 'Email already exists, choose another<br>';
            return;
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
            $this->isError = true;
            $this->errMsg .= 'Password must have at least 6 characters!<br>';
            return;
        }

        if(!password_verify($formPassword, $currentPassword)){
            $this->isError = true;
            $this->errMsg .= 'Current password is incorrect<br>';
            return;
        }

        if($newPassword !== $confirmPassword || !strlen(trim($newPassword))){
            $this->isError = true;
            $this->errMsg .= 'New password don\'t match<br>';
            return;
        }

        return password_hash($newPassword, PASSWORD_DEFAULT);

    }

    public function avatarProcedure(){

        $newAvatar = $_POST['avatarUrl'];

        if(!strlen(trim($newAvatar))){
            return null;
        }

        if(!preg_match('/(https?:\/\/|www\.)/', $newAvatar)){
            $this->isError = true;
            $this->errMsg .= 'Avatar URL is not a valid URL!<br>';
            return;
        }

        // ! Requires openSSL - extension=php_openssl.dll
        $result = @getimagesize($newAvatar);
        $result = ($result && strtolower(substr($result['mime'], 0, 5)) == 'image' ? true : false);

        if(extension_loaded('openssl')){
            if(!$result){
                $this->isError = true;
                $this->errMsg .= 'The Avatar URL you entered is not a valid image!<br>';
                return;
            }
        }

        return $newAvatar;

    }

}

?>