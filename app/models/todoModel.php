<?php

class TodoModel extends Model {

    public function createTodo(string $title){

        $id = null;

        if($_SESSION['loggedUser']){
            $id = $_SESSION['loggedUser']['id'];
        }

        $newTodo = [
            "id" => time(),
            "title" => $title,
            "status" =>  'pending',
            "created_by" => $id,
            "created_at" => date('c'), 
            "completed_at" => null
        ];

        array_push($this->_todos, $newTodo);

        $this->writeJSON('todos');

    }

}

?>