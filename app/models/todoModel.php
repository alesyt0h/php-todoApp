<?php

class TodoModel extends Model {

    public function __construct(){
        Model::__construct();
        $this->_setCollection('todos');
    }

    public function createTodo(string $title){

        $userId = null;

        if(isset($_SESSION['loggedUser'])){
            $userId = $_SESSION['loggedUser']['_id'];
        }

        $todo = [
            'title' => $title, 
            'status' => 'Pending', 
            'createdBy' => $userId ?? null,
            'createdAt' => date('Y-m-d H:i:s'),
            'completedAt' => null
        ];

        $inserted = $this->insertOne($todo);
        
        if(!isset($_SESSION['loggedUser'])){
            $_SESSION['tempUser'] ?? $_SESSION['tempUser'] = [];
            array_push($_SESSION['tempUser'], $inserted);
        }

        return $inserted;
    }

    public function assignTodos(){

        // $userId = $_SESSION['loggedUser']['_id'];
        // $tempTodosId = $_SESSION['tempUser'];

        // $this->assign($userId, $tempTodosId);

        // unset($_SESSION['tempUser']);
        // return ["userId" => $userId, "todosCount" => count($tempTodosId)];
    }

    public function modifyTodo(array $todo, string $title, string $status){

        $todo['title'] = $title;
        $todo['status'] = $status;

        if($status === 'Completed'){
            $todo['completedAt'] = date('Y-m-d H:i:s');
        } else {
            $todo['completedAt'] = null;
        }

        $todo = $this->modifyOne($todo);
        
        return $todo;
    }

    public function deleteTodo(string $todoId){

        // $this->todoId = intval($todoId);

        // $result = $this->delete($todoId);
        // return $result;
    }

    public function getTodos(mixed $id){
        
        $isArray = (gettype($id) === 'array') ? true : false;
        $field = ($isArray) ? 'id' : 'createdBy';

        return $this->getMany($field, $id);
    }

    public function getTodoById(mixed $todoId){
        $todo = $this->getOne('id', $todoId);
        
        return $todo;
    }
}

?>