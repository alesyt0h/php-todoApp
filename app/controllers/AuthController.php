<?php

class AuthController extends ApplicationController{

    public function __construct(){
        parent::__construct();
    }

    public function loginAction(){

        // TODO
        // $this->isLoggedIn();

        if(isset($_POST['username']) && isset($_POST['password'])){

            $user = strtolower($_POST['username']);
            $pass = $_POST['password'];

            if(strlen($user) < 3 && strlen($pass) < 6){
                $this->view->loginError = 'Incorrect length of user or password';
                return;
            }
            
            $loginResult = $this->userDB->checkCredentials($user, $pass);

            if($loginResult){
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['loggedUser'] = $this->userDB->getLoggedUser();

                // TODO 
                var_dump($_SESSION['loggedUser']);
                // header('Location: ' . WEB_ROOT . '/auth');
            } else {
                $this->view->loginError = 'Invalid Email or password';
            }

        }

    }

    public function registerAction(){
        
        $this->isLoggedIn();

        if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email'])){
            $this->registerUser($_POST['username'], $_POST['password'], $_POST['email']);
        }

    }

    public function logoutAction(){

        $this->view->disableView();

        $this->userDB->purgeModelUser();
        $_SESSION['isLoggedIn'] = false;
        unset($_SESSION['loggedUser']);

        header('Location: ' . WEB_ROOT . '/auth');
    }

}

?>