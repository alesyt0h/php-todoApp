<?php

class IndexController extends ApplicationController {

    public function indexAction(){
		$this->view->message = "Hello from index#index";
        $this->view->setTitle('Index Page');
        // $this->view->appendScript('main.js');
        // $this->view->appendCSS('style.css');

        $todo = new TodoController();
        $todo->newAction();

        $this->view->userTodos = $todo->listAction();

        if(isset($_GET['delete'])){
            $this->afterFilters('view', 'modalContent', $todo->formData);
        }
    }

}

?>