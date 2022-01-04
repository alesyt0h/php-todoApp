<?php

class JsonModel extends Model {

    public function test(){
        var_dump($this->findOneById(2, 'users'));
    }

}

?>