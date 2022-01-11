<?php

class TodoController extends ApplicationController {

    public function __construct(){
        parent::__construct();
        $this->todoDB = new TodoModel();

        if(!isset($_SESSION['tempUser'])){
            unset($_SESSION['allowAssign']);
        }

        $this->loadModal();
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

        if(count($todo) === 0){
            header('Location: ' . WEB_ROOT . '/todo/list');
            die();
        };

        $isValidUser = (isset($_SESSION['loggedUser']) && $todo['createdBy'] === $_SESSION['loggedUser']['id']);
        $isValidTempUser = (isset($_SESSION['tempUser']) && in_array($todo['id'], $_SESSION['tempUser']));

        if($isValidUser || $isValidTempUser){

            if(count($_POST) === 2){
    
                $validStatuses = ['Pending', 'In Process', 'Completed'];
                $newTitle = trim($_POST['title']);
                $newStatus = $_POST['status'];
    
                if(!strlen($newTitle)){
                    $_SESSION['todoError'] = 'The Todo can not be empty!';
                } else if (!in_array($newStatus, $validStatuses)) {
                    $_SESSION['todoError'] = 'The Todo status is incorrect!';
                } else {
                    $todo = $this->todoDB->modifyTodo($todo, $newTitle, $newStatus);
                }
    
            }
    
            $this->view->todo = $todo;
        } else {
            header('Location: ' . WEB_ROOT);
            die();
        }

    }

    public function newAction(){
        
        if(isset($_POST['newTodo'])){

            $newTodo = trim($_POST['newTodo']);

            if(!strlen($newTodo)){
                $_SESSION['todoError'] = 'The todo cannot be empty';
                return;
            }

            $result = $this->todoDB->createTodo($newTodo);

            if($result){

                if(!isset($_SESSION['loggedUser'])){
                    $_SESSION['todoMsg'] = "You created a todo, but you don't have an account! 
                    TODO's created without account are deleted in 24h. 
                    <a href=" . WEB_ROOT . "/auth/register" . ">Register now to keep your TODO for ever!</a>";
                } else {
                    $this->sumTodo($_SESSION['loggedUser']['id']);
                }

                $_SESSION['newTodoTemp'] = $newTodo;
                
                header('Location: ' . WEB_ROOT . substr($_SERVER['REQUEST_URI'], strlen(WEB_ROOT)));
                die();
            } else {
                $_SESSION['todoError'] = 'Error creating the todo, please try again';
            }

        }
    }

    public function deleteAction(){

        $this->view->disableView();

        if(!isset($_POST['deleteTodoId'])){
            if($_SERVER['HTTP_REFERER']){
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                header('Location: ' . WEB_ROOT);
            }
            die();
        }

        $todoId = $_POST['deleteTodoId'];
        $result = $this->todoDB->deleteTodo($todoId);

        if(!$result){
            $_SESSION['deleteError'] = 'Error borrando el todo!';
        }

        $location = preg_replace('/\?(.*)/','', $_SERVER['HTTP_REFERER']);
        header('Location: ' . $location);
        die();
    }

    public function assignAction(){

        $this->view->disableView();

        if(isset($_SESSION['allowAssign']) && $_SESSION['allowAssign'] === true){
            $newUserData = $this->todoDB->assignTodos();
            $this->sumTodo($newUserData[0], $newUserData[1]);
            
            unset($_SESSION['tempUser']);
            unset($_SESSION['allowAssign']);
            $_SESSION['assignedSuccess'] = true; 
        }

        header('Location: ' . WEB_ROOT);
        die();
    }

    public function loadModal(){
        
        // Modal for confirmation prompt when delete a todo
        if(isset($_GET['delete'])){

            if(!isset($_SERVER['HTTP_REFERER'])){

                (isset($_SERVER['HTTP_ORIGIN'])) ? $location = $_SERVER['HTTP_ORIGIN'] : $location = WEB_ROOT;
                header('Location: ' . $location );
                die();
            }

            // TODO check if this is the user who created it, if not don't even display it on modal. Same for TempUsers
            $todo = $this->todoDB->getTodoById($_GET['delete']);

            $formatedDate = date('l, F jS \of Y', strtotime($todo['createdAt']));

            $this->formData = "
            <form action=" . WEB_ROOT . '/todo/delete/' . $todo['id'] . " method='POST'>
                <p>Are you sure you want to delete <strong> " . $todo['title'] . "</strong>?</p>
                <p>You created this Todo on <strong> " . $formatedDate . "</strong></p><br>
                <button type='submit' name='deleteTodoId' value=" . $todo['id'] . " >Delete</button>
                <button type='button'>Cancel</button>
            </form>";
            
            $this->afterFilters('view', 'modalContent', $this->formData);
            
        } else if(isset($_GET['assign'])) {

            $_SESSION['allowAssign'] = true;

            $this->formData = "
            <form action=" . WEB_ROOT . '/todo/assign' . ">
                <p>You created <strong>". count($_SESSION['tempUser']) . "</strong> todo while you were not authenticated. Would you like to add them to this account?</p>
                <p>If you want to do it later, you will find a link in the menu to assign them to your account.</p>
                <button type='submit'>Yes</button>
                <button type='button'>No</button>
            </form>";
            
            $this->afterFilters('view', 'modalContent', $this->formData);
        }
    }

}

?>