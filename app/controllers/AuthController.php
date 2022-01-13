<?php

class AuthController extends ApplicationController{

    public function __construct(){
        parent::__construct();
    }

    public function loginAction(){

        if($this->isUser()) $this->redirect();

        if(isset($_POST['username']) && isset($_POST['password'])){

            $user = strtolower(trim($_POST['username']));
            $pass = trim($_POST['password']);

            if(strlen($user) < 3 || strlen($pass) < 6){
                $this->appMsg('error', 'Incorrect length of user or password');
                return;
            }
            
            $loginResult = $this->userDB->checkCredentials($user, $pass);

            if($loginResult){
                $_SESSION['loggedUser'] = $this->userDB->getLoggedUser();
                $_SESSION['allowAssign'] = true;

                ($this->isTempUser()) ? $this->redirect('?assign') : $this->redirect();
            } else {
                $this->appMsg('error', 'Invalid Email or password');
                header('HTTP/1.0 403 Forbidden');
            }

        }

    }

    public function registerAction(){
        
        if($this->isUser()) $this->redirect();
        
        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
            
            $user = strtolower(trim($_POST['username']));
            $pass = trim($_POST['password']);
            $email = trim($_POST['email']);

            $emailPattern = '/^[a-z0-9._%+-]+@[a-z0-9.-]{2,}\\.[a-z]{2,4}$/';

            if(strlen($user) < 3 || strlen($pass) < 6){
                $this->appMsg('error', 'Incorrect length of user or password');
                return;
            }

            if(!preg_match($emailPattern, $email)){
                $this->appMsg('error', 'Please introduce a valid email');
                return;
            }

            if($this->userDB->userExists($user)){
                $this->appMsg('error', 'This username is taken! Please choose another');
                return;
            }

            if($this->userDB->mailExists($email)){
                $this->appMsg('error', 'This email is already in use. Use another');
                return;
            }

            $pass = password_hash($pass, PASSWORD_DEFAULT);
            $result = $this->userDB->insertUser($user, $pass, $email);

            if($result){
                $_SESSION['allowAssign'] = true;
                $_SESSION['loggedUser'] = $this->userDB->getLoggedUser();

                $this->view->accountCreated = true;
                $this->appMsg('success', 'Account created! You will be redirected in few seconds');
                // Redirect to index when account is created, is being done by JavaScript on register.phtml
            } else {
                $this->appMsg('error', 'Unknown error. Please try again');
            }

        }

    }

    public function logoutAction(){

        $this->view->disableView();

        $this->userDB->purgeModelUser();
        unset($_SESSION['loggedUser']);

        $this->redirect('/auth');
    }

}

?>