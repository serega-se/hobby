<?php


namespace Hobby\Controller;


class IndexController
{
    public function index(){
        echo file_get_contents(__DIR__ . '/../templates/index.html');
    }
}