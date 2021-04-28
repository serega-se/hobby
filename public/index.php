<?php

use Hobby\Controller\IndexController;
use Hobby\Controller\PostController;

require_once '../vendor/autoload.php';

putenv("DSN=mysql:host=localhost;dbname=hobby;charset=UTF8");
putenv("DB_USER=symfony");
putenv("DB_PASS=symfony");
putenv("POST_SOURCE_URL=https://habr.com/ru/rss/hubs/all/");

$url = parse_url($_SERVER["REQUEST_URI"]);
switch ($url['path']){
    case '/api/v1/posts':
        (new PostController())->getPosts($_REQUEST);
        break;
    case '/api/v1/posts/upload':
        (new PostController())->uploadPosts($_REQUEST);
        break;
    case '/api/v1/posts/content':
        (new PostController())->getPostContent($_REQUEST);
        break;
    default:
        (new IndexController())->index();
}
