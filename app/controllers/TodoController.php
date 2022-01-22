<?php

class TodoController extends ApplicationController {

    public function __construct(){
        parent::__construct();
        $this->todoDB = new TodoModel();

        if(!$this->isTempUser()){
            unset($_SESSION['allowAssign']);
        }

        $this->loadModal();
    }

    public function listAction(){

        if($this->isUser()){

            $this->userId = $_SESSION['loggedUser']['id'];

            $userTodos = [];

            $userTodos = array_filter($this->todoDB->getTodos(), function($todo){ 
                if($todo['createdBy'] === $this->userId){ 
                    return $todo; 
                }
            });

            $userTodos = array_splice($userTodos, 0);
            
        } else if($this->isTempUser()) {

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
            $this->redirect('/todo/list');
        }
        
        $todo = $this->todoDB->getTodoById($todoId);

        if(count($todo) === 0){
            $this->redirect('/todo/list');
        };

        $isValidUser = ($this->isUser() && $todo['createdBy'] === $_SESSION['loggedUser']['id']);
        $isValidTempUser = ($this->isTempUser() && in_array($todo['id'], $_SESSION['tempUser']));

        if($isValidUser || $isValidTempUser){

            if(count($_POST) === 2){
    
                $validStatuses = ['Pending', 'In Process', 'Completed'];
                $newTitle = trim($_POST['title']);
                $newStatus = $_POST['status'];
    
                if(!strlen($newTitle)){
                    $this->appMsg('error', 'The Todo can not be empty!');
                } else if (!in_array($newStatus, $validStatuses)) {
                    $this->appMsg('error', 'The Todo status is incorrect!');
                } else {
                    $todo = $this->todoDB->modifyTodo($todo, $newTitle, $newStatus);
                    $this->appMsg('success', 'The Todo was updated correctly');
                    $this->selfRedirect();
                }
            }
            
            $this->view->todo = $todo;
        } else {
            $this->redirect();
        }

    }

    public function newAction(){
        
        if(!isset($_POST['newTodo'])) return false;

        $newTodo = trim($_POST['newTodo']);

        if(!strlen($newTodo)){
            $this->appMsg('error', 'The todo cannot be empty');
            $this->selfRedirect();
        }

        $result = $this->todoDB->createTodo($newTodo);

        if($result){

            if(!$this->isUser()){
                $this->appMsg('info', 'You created a todo, but you don\'t have an account!<br/>
                                       TODO\'s created without account are deleted in 24h. <br/>
                                       <a href=' . WEB_ROOT . "/auth/register" . ' class="underline underline-offset-2 text-blue-500">Register now</a> to keep your TODO\'s for ever!');
            } else {
                $this->sumTodo($_SESSION['loggedUser']['id']);
            }

            $this->appMsg('success', 'You created the todo: <strong>' . $newTodo . '</strong>');
            $this->selfRedirect();
        } else {
            $this->appMsg('error', 'Error creating the todo, please try again');
        }
    }

    public function deleteAction(){

        $this->view->disableView();

        if(!isset($_POST['deleteTodoId'])){
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? WEB_ROOT);
            die();
        }

        $todoId = $_POST['deleteTodoId'];
        $todo = $this->todoDB->getTodoById($todoId);

        $isValidUser = ($this->isUser() && $todo['createdBy'] === $_SESSION['loggedUser']['id']);
        $isValidTempUser = ($this->isTempUser() && in_array($todo['id'], $_SESSION['tempUser']));

        if($isValidUser || $isValidTempUser) {

            $result = $this->todoDB->deleteTodo($todoId);

            if(!$result){
                $this->appMsg('error', 'Error deleting the todo');
            }

            if($isValidTempUser && $result){
                $key = array_search($todoId, $_SESSION['tempUser']);
                unset($_SESSION['tempUser'][$key]);
                $_SESSION['tempUser'] = array_splice($_SESSION['tempUser'], 0);
            }
        }

        $location = preg_replace('/\?(.*)/','', $_SERVER['HTTP_REFERER']);
        header('Location: ' . $location);
        die();
    }

    public function assignAction(){

        $this->view->disableView();

        if(isset($_SESSION['allowAssign']) && $_SESSION['allowAssign'] === true){
            $newUserData = $this->todoDB->assignTodos();
            $this->sumTodo($newUserData['userId'], $newUserData['todosCount']);
            
            unset($_SESSION['tempUser']);
            unset($_SESSION['allowAssign']);

            $_SESSION['assignedSuccess'] = true; 
            $this->appMsg('success', 'Todo\'s cretead while you were not authenticated had been successfully added to this account!');
        }

        $this->redirect();
    }

    public function loadModal(){
        
        if(isset($_GET['delete'])){

            if(!isset($_SERVER['HTTP_REFERER'])) $this->refererRedirect();
            
            $todo = $this->todoDB->getTodoById($_GET['delete']);

            $isInvalidUser = $this->isUser() && $todo['createdBy'] !== $_SESSION['loggedUser']['id'];
            $isInvalidTempUser = ($this->isTempUser() && !in_array($todo['id'], $_SESSION['tempUser']));

            if($isInvalidTempUser || $isInvalidUser) $this->refererRedirect();

            $this->formData = "
            <form action=" . WEB_ROOT . '/todo/delete/' . $todo['id'] . " method='POST'>
                <p>Are you sure you want to delete <strong> " . $todo['title'] . "</strong>?</p>
            ";

            $this->modal = [];

            $this->modal['content'] = $this->formData;
            $this->modal['title'] = 'Delete todo';
            $this->modal['type'] = 'Delete';
            $this->modal['id'] = $todo['id'];
            
            $this->afterFilters('view', 'modal', $this->modal);
            
        } else if(isset($_GET['assign'])) {

            if(!$_SESSION['allowAssign']) $this->redirect();

            $this->formData = "
            <form action=" . WEB_ROOT . '/todo/assign' . ">
                <p>You created <strong>". count($_SESSION['tempUser']) . "</strong> todo while you were not authenticated. Would you like to add them to this account?</p><br>
                <p>If you want to do it later, you will find a link in the menu to assign them to your account.</p>
            ";

            $this->modal = [];

            $this->modal['content'] = $this->formData;
            $this->modal['title'] = 'Assign todo' . (count($_SESSION['tempUser']) > 1 ? 's' : '');
            $this->modal['type'] = 'Assign';

            $this->afterFilters('view', 'modal', $this->modal);
        }
    }

}

?>