<?php


namespace Hobby\Controller;

use Hobby\Entity\Post;
use Hobby\Service\PostService;

class PostController
{
    protected PostService $postService;

    function __construct()
    {
        $this->postService = new PostService();
    }

    public function uploadPosts(array $request)
    {
        $onpage = 5;

        if (!empty($request['onpage'])
            && !filter_var($request['onpage'], FILTER_VALIDATE_INT) === false
        ) {
            $onpage = $request['onpage'];
        }

        $posts = $this->postService->upload($onpage);
        $rowCount = $this->postService->getCount();

        $result = [
            'items' => $posts,
            'count' => $rowCount,
            'onpage' => $onpage,
            'current' => 0
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function getPosts(array $request)
    {
        $onpage = 5;
        $page = 0;

        if (!empty($request['onpage'])
            && !filter_var($request['onpage'], FILTER_VALIDATE_INT) === false
        ) {
            $onpage = $request['onpage'];
        }

        if (!empty($request['page'])
            && !filter_var($request['page'], FILTER_VALIDATE_INT) === false
        ) {
            $page = $request['page'];
        }

        $posts = $this->postService->getList($onpage, $page*$onpage);
        $rowCount = $this->postService->getCount();

        $result = [
            'items' => $posts,
            'count' => $rowCount,
            'onpage' => $onpage,
            'current' => $page
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function getPostContent(array $request)
    {
        if (empty($request['id'])
            || filter_var($request['id'], FILTER_VALIDATE_INT) === false
        ) {
            $result = [];
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            echo json_encode($result);
            exit;
        }

        $id = (int)$request['id'];

        /** @var Post $post */
        $post = $this->postService->getById($id);

        if (!$post) {
            $result = [
                'errors' => 'No post found'
            ];
            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            echo json_encode($result);
            exit;
        }

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $dom->validateOnParse = true;

        if(!$dom->loadHTMLFile($post->getLink(), LIBXML_HTML_NODEFDTD)){

            $result = [
                'errors' => libxml_get_errors()
            ];

            header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
            echo json_encode($result);
            exit;
        }

        $content = $dom->getElementById('post-content-body')->textContent;

        $result = [
            'content' => $content
        ];

        header('Content-Type: application/json');
        echo json_encode($result);
    }

}