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

            $validation = new LoginSuperSet($user, $pass);
            $validation = LoginSuperSet::$message;

            if($validation){
                $this->appMsg('error', $validation);
                $this->redirect('/auth/login');
            }
            
            $loginResult = $this->userDB->checkCredentials($user, $pass);

            if($loginResult){
                $_SESSION['loggedUser'] = $this->userDB->getLoggedUser();
                $_SESSION['allowAssign'] = true;

                ($this->isTempUser()) ? $this->redirect('/?assign') : $this->redirect();
            } else {
                $this->appMsg('error', 'Invalid Email or password');
                $this->redirect('/auth/login');
            }

        }

    }

    public function registerAction(){
        
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