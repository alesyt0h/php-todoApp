<?php

class IndexController extends ApplicationController {

    public function indexAction(){
        $todo = new TodoController();
        $todo->newAction();

        $this->view->userTodos = $todo->listAction();

        if(isset($_GET['delete']) || isset($_GET['assign'])){
            $this->afterFilters('view', 'modalContent', $todo->formData);
        }
    }

}

?>