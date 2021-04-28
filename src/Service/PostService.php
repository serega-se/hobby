<?php


namespace Hobby\Service;

use Hobby\Conf\DatabaseConnection;
use Hobby\Entity\Post;
use PDO;

class PostService
{
    protected DatabaseConnection $dbConnection;

    function __construct()
    {
        $this->dbConnection = new DatabaseConnection();
    }

    public function getCount()
    {
        $db = $this->dbConnection::instance();
        $sth = $db->prepare("SELECT COUNT(*) as total_rows FROM post");
        $sth->execute();

        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row['total_rows'];
    }

    public function getList(int $limit = 5, int $offset = 0)
    {
        if($limit == 0){
            return [];
        }

        /** @var PDO $db */
        $db = $this->dbConnection::instance();
        $sth = $db->prepare('SELECT * FROM post LIMIT :limit OFFSET :offset');
        $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
        $sth->bindValue(':offset', $offset, PDO::PARAM_INT);
        $sth->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $sth->execute();

        return $sth->fetchAll();
    }

    public function upload(int $limit = 5)
    {
        if($limit == 0){
            return [];
        }

        $rssFeed = simplexml_load_file(getenv("POST_SOURCE_URL"));

        if (empty($rssFeed)) {
            return [];
        }

        /** @var \PDO $db */
        $db = $this->dbConnection::instance();
        $sth = $db->prepare("INSERT INTO post (link, header, text) VALUES (:link, :header, :text)");

        $i = 0;
        foreach ($rssFeed->channel->item as $feedItem) {

            if ($i >= $limit) {
                break;
            }

            try {
                $sth->bindValue(':link', $feedItem->link, PDO::PARAM_STR);
                $sth->bindValue(':header',$feedItem->title, PDO::PARAM_STR);
                $sth->bindValue(':text',$feedItem->description, PDO::PARAM_STR);
                $sth->execute();
            }catch (\PDOException $e){
                //TODO: log
            }
            $i++;
        }

        return $this->getList($limit);
    }

    public function getById(int $id)
    {
        /** @var \PDO $db */
        $db = $this->dbConnection::instance();
        $sth = $db->prepare("SELECT * FROM post WHERE id=:id");
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->setFetchMode(PDO::FETCH_CLASS, Post::class);
        $sth->execute();

        return $sth->fetch();
    }
}