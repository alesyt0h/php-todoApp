<?php

class TodoModel extends Model {

    public function createTodo(string $title){

        $id = null;

        if(isset($_SESSION['loggedUser'])){
            $id = $_SESSION['loggedUser']['id'];
        }

        $newTodo = [
            "id" => intval(microtime(true) * 1000),
            "title" => $title,
            "status" =>  'pending',
            "created_by" => $id,
            "created_at" => date('c'), 
            "completed_at" => null
        ];

        array_push($this->_todos, $newTodo);

        return $this->writeJSON('todos');
    }

}

?>