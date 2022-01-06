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

                if(!isset($_SESSION['loggedUser'])){
                    $_SESSION['todoMsg'] = "You created a todo, but you don't have an account! 
                    TODO's created without account are deleted in 24h. 
                    <a href=" . WEB_ROOT . "/auth/register" . ">Register now to keep your TODO for ever!</a>";
                }
                
                header('Location: ' . WEB_ROOT . substr($_SERVER['REQUEST_URI'], strlen(WEB_ROOT)));
                die();
            } else {
                $this->view->todoError = 'Error creating the todo, please try again';
            }

        }
    }

    public function assignAction(){

        $this->view->disableView();

        if(isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], -13, 13) === 'auth/register'){
            $this->todoDB->assignTodos();
        }

        header('Location: ' . WEB_ROOT);
        die();
    }

}

?>