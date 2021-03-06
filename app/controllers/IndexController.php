<?php

class IndexController extends ApplicationController {

    public function indexAction(){
        $todo = new TodoController();
        $todo->newAction();

        $this->view->userTodos = $todo->listAction();

        if(!isset($_SESSION['proTip'])){
            $_SESSION['proTip'] = true;
        }

        if(isset($_GET['delete']) || isset($_GET['assign'])){
            $this->afterFilters('view', 'modal', $todo->modal);
        }
    }

}

?>