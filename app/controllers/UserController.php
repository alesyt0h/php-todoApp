<?php

class UserController extends ApplicationController{

    public function profileAction(){
        
        if(!isset($_SESSION['loggedUser'])){
            header('Location: ' . WEB_ROOT);
            die();
        }

        $this->view->user = $_SESSION['loggedUser'];

    }

}

?>