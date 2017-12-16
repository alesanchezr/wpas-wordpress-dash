<?php
namespace php\Controllers;

class PostController{
    
    public function renderPost(){
        
        $args = [];

        //any query or backend operation you want to do
        $args['test'] = 'test';

        return $args;
    }
    
}
?>