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
            "status" =>  'Pending',
            "createdBy" => $userId,
            "createdAt" => date('c'), 
            "completedAt" => null
        ];

        array_push($this->_todos, $newTodo);

        return $this->writeJSON('todos');
    }

    public function assignTodos(){

        $userId = $_SESSION['loggedUser']['id'];
        $tempTodosId = $_SESSION['tempUser'];

        for ($i=0; $i < count($tempTodosId); $i++) {
            for ($j = count($this->_todos) - 1; $j > 0; $j--) { // Reverse lookup - Faster

                if($this->_todos[$j]['id'] === $tempTodosId[$i]){
                    $this->_todos[$j]['createdBy'] = $userId;
                    break;
                }
            }
        }

        $this->writeJSON('todos');
        unset($_SESSION['tempUser']);
        return [$userId, count($tempTodosId)];
    }

    public function modifyTodo(array $todo, string $title, string $status){

        $todo['title'] = $title;
        $todo['status'] = $status;

        if($status === 'Completed'){
            $todo['completedAt'] = date('c');
        } else {
            $todo['completedAt'] = null;
        }

        $this->todo = $todo;

        $this->_todos = array_map( function($oldTodo){ 
            return ($oldTodo['id'] === $this->todo['id']) ? $this->todo : $oldTodo;
        }, $this->_todos);

        $this->writeJSON('todos');

        return $this->todo;
    }

    public function deleteTodo(string $todoId){

        $todo = $this->findOneById($todoId, 'todos');

        // TODO try this changing loggedUser id
        if($todo['createdBy'] !== $_SESSION['loggedUser']['id']){
            return 'You are not the owner of this Todo!';
        }

        $this->todoId = intval($todoId);

        $this->_todos = array_filter($this->_todos, function($oldTodo){ 
            if($oldTodo['id'] !== $this->todoId){ 
                return $oldTodo; 
            } 
        });

        $this->_todos = array_splice($this->_todos, 0);

        return $this->writeJSON('todos');
    }

    public function getTodos(){
        return $this->_todos;
    }

    public function getTodoById(string $todoId){
        return $this->findOneById(intval($todoId), 'todos');
    }
}

?>