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
            $this->todoDB->createTodo($_POST['newTodo']);
        }

    }

}

?>