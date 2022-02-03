<?php

class TodoModel extends Model {

    public function __construct(){
        Model::__construct();
        $this->_setTable('todos');
    }

    public function createTodo(string $title){

        $userId = null;

        if(isset($_SESSION['loggedUser'])){
            $userId = $_SESSION['loggedUser']['id'];
        }

        $todo = [
            'title' => $title, 
            'status' => 'Pending', 
            'created_by' => $userId ?? null, 
            'created_at' => date('Y-m-d H:i:s'), 
            'completed_at' => null
        ];

        $inserted = $this->save($todo);

        if(!isset($_SESSION['loggedUser'])){
            $_SESSION['tempUser'] ?? $_SESSION['tempUser'] = [];
            array_push($_SESSION['tempUser'], $inserted);
        }

        return $inserted;
    }

    public function assignTodos(){

        $userId = $_SESSION['loggedUser']['id'];
        $tempTodosId = $_SESSION['tempUser'];

        $this->assign($userId, $tempTodosId);

        unset($_SESSION['tempUser']);
        return ["userId" => $userId, "todosCount" => count($tempTodosId)];
    }

    public function modifyTodo(array $todo, string $title, string $status){

        $todo['title'] = $title;
        $todo['status'] = $status;

        if($status === 'Completed'){
            $todo['completed_at'] = date('Y-m-d H:i:s');
        } else {
            $todo['completed_at'] = null;
        }

        $this->save($todo);
        
        return $todo;
    }

    public function deleteTodo(string $todoId){

        $this->todoId = intval($todoId);

        $result = $this->delete($todoId);
        return $result;
    }

    public function getTodos(mixed $id){
        return $this->fetchTodos($id);
    }

    public function getTodoById(string $todoId){
        $todo = $this->fetchOne(intval($todoId));
        $todo['id'] = intval($todo['id']);
        $todo['created_by'] = intval($todo['created_by']);
        
        return $todo;
    }
}

?>