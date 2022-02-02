<?php

class UserController extends ApplicationController{

    public function __construct(){
        $this->userDB = new UserModel();
    }

    public function profileAction(){

        if(!$this->isUser()) $this->redirect();

        $this->view->user = $_SESSION['loggedUser'];
        
        if(count($_POST) > 0){
            
            $userId = $_SESSION['loggedUser']['id'];

            $this->validation = new Validations(new EmptyRuleSet());

            $email = $this->emailProcedure();
            $password = $this->passwordProcedure();
            $avatarUrl = $this->avatarProcedure();
            
            if($this->validation::$message){
                $this->appMsg('error', $this->validation::$message);
                $this->redirect('/user/profile');
            }
            
            $result = $this->userDB->modifyUser($userId, $email, $password, $avatarUrl);
            
            if($result['status'] ?? null){
                ($result['equals']) ? null : $this->appMsg('success','Profile updated!');
                
                $this->redirect('/user/profile');
            } else {
                $this->appMsg('error','Error updating the profile, please try again');
            }
        }
    }
    
    public function passwordProcedure(){

        $currentPassword = $_SESSION['loggedUser']['password'];
        
        $formPassword = trim($_POST['password']);
        $newPassword = trim($_POST['newPassword']);
        $confirmPassword = trim($_POST['confirmPassword']);

        if(!$formPassword){
            return $currentPassword;
        }

        $this->validation->setValidator(new PasswordValidation($formPassword, $currentPassword, $newPassword, $confirmPassword, true));
        $this->validation->performValidation();

        return password_hash($newPassword, PASSWORD_DEFAULT);
    }

    public function emailProcedure(){
        
        $currentMail = $_SESSION['loggedUser']['email'];
        $newMail = trim($_POST['email']);
        
        if($newMail === $currentMail){
            return $currentMail;
        }
        
        $this->validation->setValidator(new EmailValidation($newMail));
        $this->validation->performValidation();

        return $newMail;
    }

    public function avatarProcedure(){

        $newAvatar = trim($_POST['avatarUrl']);

        if(!$newAvatar){
            return null;
        }

        $this->validation->setValidator(new AvatarValidation($newAvatar));
        $this->validation->performValidation();

        return $newAvatar;
    }

}

?>