<?php

class TodoController extends ApplicationController {

    public function __construct(){
        $this->todoDB = new TodoModel();
    }

    public function listAction(){

        if(isset($_SESSION['loggedUser'])){

            $this->userId = $_SESSION['loggedUser']['id'];

            $userTodos = [];

            $userTodos = array_filter($this->todoDB->getTodos(), function($todo){ 
                if($todo['createdBy'] === $this->userId){ 
                    return $todo; 
                }
            });

            $userTodos = array_splice($userTodos, 0);
            
        } else if(isset($_SESSION['tempUser'])) {

            $userTodos = array_filter($this->todoDB->getTodos(), function($todo){ 
                if(in_array($todo['id'], $_SESSION['tempUser'])){ 
                    return $todo; 
                }
            });

            $userTodos = array_splice($userTodos, 0);
        }

        if($this->view){
            $this->view->userTodos = $userTodos ?? [];
        } else {
            return $userTodos ?? [];
        }

    }

    public function editAction(){

		$uri = explode('/',$_SERVER['REQUEST_URI']);
        $todoId = $uri[count($uri) - 1];

        if(!is_numeric($todoId)){
            header('Location: ' . WEB_ROOT . '/todo/list');
            die();
        }
        
        $todo = $this->todoDB->getTodoById($todoId);

        if( count($todo) === 0 ){
            header('Location: ' . WEB_ROOT . '/todo/list');
            die();
        };

        if(count($_POST)){

            $newTitle = $_POST['title'];
            $newStatus = $_POST['status'];

            $todo = $this->todoDB->modifyTodo($todo, $newTitle, $newStatus);
        }

        $this->view->todo = $todo;

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