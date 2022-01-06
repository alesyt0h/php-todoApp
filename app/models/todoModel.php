<?php

class TodoModel extends Model {

    public function createTodo(string $title){

        $userId = null;
        $todoId = intval(microtime(true) * 1000);

        if(isset($_SESSION['loggedUser'])){
            $userId = $_SESSION['loggedUser']['id'];
        } else {
            $_SESSION['tempUser'] ?? $_SESSION['tempUser'] = [];
            array_push($_SESSION['tempUser'], $todoId);
        }

        $newTodo = [
            "id" => $todoId,
            "title" => $title,
            "status" =>  'pending',
            "createdBy" => $userId,
            "createdAt" => date('c'), 
            "completedAt" => null
        ];

        array_push($this->_todos, $newTodo);

        return $this->writeJSON('todos');
    }

}

?>