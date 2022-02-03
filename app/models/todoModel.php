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

        return $this->writeJSON('todos', $newTodo);
    }

    public function assignTodos(){

        $userId = $_SESSION['loggedUser']['id'];
        $tempTodosId = $_SESSION['tempUser'];

        $fullTodos = $this->parseJSON('todos');

        for ($i=0; $i < count($tempTodosId); $i++) {
            for ($j = count($fullTodos) - 1; $j >= 0; $j--) { // Reverse lookup - Faster

                if($fullTodos[$j]['id'] === $tempTodosId[$i]){
                    $fullTodos[$j]['createdBy'] = $userId;
                    break;
                }
            }
        }

        $this->writeJSON('todos', $fullTodos, true);

        unset($_SESSION['tempUser']);
        return ["userId" => $userId, "todosCount" => count($tempTodosId)];
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

        $fullTodos = $this->parseJSON('todos');

        $fullTodos = array_map( function($oldTodo){ 
            return ($oldTodo['id'] === $this->todo['id']) ? $this->todo : $oldTodo;
        }, $fullTodos);

        $this->writeJSON('todos', $fullTodos, true);

        return $this->todo;
    }

    public function deleteTodo(string $todoId){

        $this->todoId = intval($todoId);

        $fullTodos = $this->parseJSON('todos');
        $fullTodos = array_filter($fullTodos, function($oldTodo){ 
            if($oldTodo['id'] !== $this->todoId){ 
                return $oldTodo; 
            }
        });

        $fullTodos = array_splice($fullTodos, 0);

        return $this->writeJSON('todos', $fullTodos, true);
    }

    public function getTodos(){
        return $this->_todos;
    }

    public function getTodoById(string $todoId){
        return $this->findOneById(intval($todoId), 'todos');
    }
}

?>