<?php

class TodoController extends ApplicationController {

    public function __construct(){
        $this->todoDB = new TodoModel();
    }

    public function listAction(){

    }

    public function editAction(){

    }

    public function newAction(){
        
        if(isset($_POST['newTodo'])){
            $result = $this->todoDB->createTodo($_POST['newTodo']);

            if($result){
                header('Location: ' . WEB_ROOT . substr($_SERVER['REQUEST_URI'], strlen(WEB_ROOT)));
            } else {
                $this->view->todoError = 'Error creating the todo, please try again';
            }
        }
    }

}

?>