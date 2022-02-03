<?php

class AuthController extends ApplicationController{

    public function __construct(){
        parent::__construct();
    }

    public function loginAction(){

        $this->view->setTitle('Sign in | ' . APP_TITLE);

        if($this->isUser()) $this->redirect();

        if(isset($_POST['username']) && isset($_POST['password'])){

            $user = strtolower(trim($_POST['username']));
            $pass = trim($_POST['password']);

            $validation = new LoginSuperSet($user, $pass);
            $validation = LoginSuperSet::$message;

            if($validation){
                $this->appMsg('error', $validation);
                $this->redirect('/auth/login');
            }
            
            $loggedUser = $this->userDB->checkCredentials($user, $pass);

            if($loggedUser){
                $_SESSION['loggedUser'] = $loggedUser;
                $_SESSION['allowAssign'] = true;

                ($this->isTempUser()) ? $this->redirect('/?assign') : $this->redirect();
            } else {
                $this->appMsg('error', 'Invalid Email or password');
                $this->redirect('/auth/login');
            }

        }

    }

    public function registerAction(){
        
        $this->view->setTitle('Sign up | ' . APP_TITLE);

        if($this->isUser()) $this->redirect();
        
        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
            
            $user = strtolower(trim($_POST['username']));
            $pass = trim($_POST['password']);
            $email = trim($_POST['email']);

            $validation = new RegisterSuperSet($user, $pass, $email);
            $validation = RegisterSuperSet::$message;

            if($validation){
                $this->appMsg('error', $validation);
                $this->redirect('/auth/register');
            }

            $pass = password_hash($pass, PASSWORD_DEFAULT);
            $registeredUser = $this->userDB->insertUser($user, $pass, $email);

            if($registeredUser){
                $_SESSION['allowAssign'] = true;
                $_SESSION['loggedUser'] = $registeredUser;

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

        unset($_SESSION['loggedUser']);
        $this->redirect('/auth');
    }

}

?>