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

        ($this->view) ? $this->view->setTitle('Todo List | ' . APP_TITLE) : null;

        if($this->isUser()){

            $this->userId = $_SESSION['loggedUser']['_id'];
            $userTodos = $this->todoDB->getTodos($this->userId);
        } else if($this->isTempUser()) {
            
            $tempTodos = $_SESSION['tempUser'];
            $userTodos = $this->todoDB->getTodos($tempTodos);
        }

        if($this->view){
            $this->view->userTodos = $userTodos ?? [];
        } else {
            return $userTodos ?? [];
        }

    }

    public function editAction(){

        $this->view->setTitle('Edit Todo | ' . APP_TITLE);

		$uri = explode('/',$_SERVER['REQUEST_URI']);
        $todoId = $uri[count($uri) - 1];

        $todoId = $this->todoDB->returnObjectId($todoId);
        $todo = $this->todoDB->getTodoById($todoId);

        if(count($todo) === 0){
            $this->selfRedirect();
        };

        $isValidUser = ($this->isUser() && $todo['createdBy'] === $_SESSION['loggedUser']['_id']);
        $isValidTempUser = ($this->isTempUser() && in_array($todo['_id'], $_SESSION['tempUser']));

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
                    $newTitle = $this->htmlReplacer($newTitle);
                    $newTitle = ucfirst($newTitle);

                    $todo = $this->todoDB->modifyTodo($todo, $newTitle, $newStatus);
                    $this->appMsg('success', 'The Todo was updated correctly');
                    $this->selfRedirect();
                }
            } else if(isset($_POST['change'])){
                if($todo['status'] === 'Pending'){
                    $newStatus = 'In Process';
                } else if ($todo['status'] === 'In Process'){
                    $newStatus = 'Completed';
                } else if ($todo['status'] === 'Completed'){
                    $newStatus = 'Pending';
                }
    
                $todo = $this->todoDB->modifyTodo($todo, $todo['title'], $newStatus);
            }
            
            $this->view->todo = $todo;
        } else {
            $this->redirect();
        }

    }

    public function newAction(){

        ($this->view) ? $this->view->setTitle('New Todo | ' . APP_TITLE) : null;
        
        if(!isset($_POST['newTodo'])) return false;

        $this->validation = new Validations(new EmptyRuleSet());
        $newTodo = trim($_POST['newTodo']);

        $this->validation->setValidator(new TodoValidation($newTodo));
        $this->validation->performValidation();

        if($this->validation::$message){
            $this->appMsg('error', $this->validation::$message);
            $this->selfRedirect();
        }

        $newTodo = $this->htmlReplacer($newTodo);
        $newTodo = ucfirst($newTodo);
        
        $result = $this->todoDB->createTodo($newTodo);

        if($result){

            if(!$this->isUser()){
                $this->appMsg('info', 'You created a todo, but you don\'t have an account!<br/>
                                       TODO\'s created without an account are deleted in 24h. <br/>
                                       <a href=' . WEB_ROOT . "/auth/register" . ' class="underline underline-offset-2 text-blue-500">Register now</a> to keep your TODO\'s for ever!');
            } else {
                $this->sumTodo();
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
            $location = $_SERVER['HTTP_REFERER'] ?? WEB_ROOT;
            header('Location: ' . $location);
            die();
        }

        $todoId = $this->todoDB->returnObjectId($_POST['deleteTodoId']);
        $todo = $this->todoDB->getTodoById($todoId);

        $isValidUser = ($this->isUser() && $todo['createdBy'] === $_SESSION['loggedUser']['_id']);
        $isValidTempUser = ($this->isTempUser() && in_array($todo['_id'], $_SESSION['tempUser']));

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
            $this->sumTodo($newUserData['todosCount']);
            
            unset($_SESSION['tempUser']);
            unset($_SESSION['allowAssign']);

            $_SESSION['assignedSuccess'] = true; 
            $this->appMsg('success', 'Todo\'s created while you were not authenticated had been successfully added to this account!');
        }

        $this->redirect();
    }

    public function loadModal(){
        
        if(isset($_GET['delete'])){

            ($this->view) ? $this->view->setTitle('Delete Todo | ' . APP_TITLE) : null;

            if(!isset($_SERVER['HTTP_REFERER'])) $this->refererRedirect();
            
            $todoId = $this->todoDB->returnObjectId($_GET['delete']);
            $todo = $this->todoDB->getTodoById($todoId);

            $isInvalidUser = $this->isUser() && $todo['createdBy'] !== $_SESSION['loggedUser']['_id'];
            $isInvalidTempUser = ($this->isTempUser() && !in_array($todo['_id'], $_SESSION['tempUser']) && $_SESSION['allowAssign'] === false);

            if($isInvalidTempUser || $isInvalidUser) $this->refererRedirect();

            $this->formData = "
            <form action=" . WEB_ROOT . '/todo/delete/' . $todo['_id'] . " method='POST'>
                <p>Are you sure you want to delete <strong> " . $todo['title'] . "</strong>?</p>
            ";

            $this->modal = [];

            $this->modal['content'] = $this->formData;
            $this->modal['title'] = 'Delete todo';
            $this->modal['type'] = 'Delete';
            $this->modal['id'] = $todo['_id'];
            
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

    // HTML Characters Replace
    private function htmlReplacer(string $todo){
        if(preg_match('/[<>"]/', $todo)){
            return str_replace(['<','>','"'], ['&lt;', '&gt;', '&quot;'], $todo);
        } else {
            return $todo;
        }
    }

}

?>