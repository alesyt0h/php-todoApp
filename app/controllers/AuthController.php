<?php

class AuthController extends ApplicationController{

    public function __construct(){
        parent::__construct();
    }

    public function loginAction(){

        $this->isLoggedIn();

        if(isset($_POST['username']) && isset($_POST['password'])){

            $user = strtolower($_POST['username']);
            $pass = $_POST['password'];
            
            $loginResult = $this->userDB->checkCredentials($user, $pass);

            if($loginResult){
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['loggedUser'] = $this->userDB->getLoggedUser();

                var_dump($_SESSION['loggedUser']);
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