<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{
    private $id;
    private $id_user;
    private $tweet;
    private $date;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function saveTweet()
    {
        $query = "insert into tweets(id_user, tweet)values(:id_user, :tweet)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    public function getAll()
    {
        $query = "
            select 
                t.id, 
                t.id_user, 
                u.name, 
                t.tweet, 
                DATE_FORMAT(t.date, '%d/%m/%Y %H:%i') as date
            from 
                tweets as t
                left join users as u on (t.id_user = u.id)
            where 
                t.id_user = :id_user
                or t.id_user in (SELECT id_following_user FROM users_followers WHERE id_user = :id_user)
            order by
                t.date desc
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPerPage($limit, $offset)
    {
        $query = "
            select 
                t.id, 
                t.id_user, 
                u.name, 
                t.tweet, 
                DATE_FORMAT(t.date, '%d/%m/%Y %H:%i') as date
            from 
                tweets as t
                left join users as u on (t.id_user = u.id)
            where 
                t.id_user = :id_user
                or t.id_user in (SELECT id_following_user FROM users_followers WHERE id_user = :id_user)
            order by
                t.date desc
            limit
                $limit
            offset
                $offset
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //recuperar total de tweets
    public function getTotalRegistries()
    {
        $query = "
            select 
                count(*) as total
            from 
                tweets as t
                left join users as u on (t.id_user = u.id)
            where 
                t.id_user = :id_user
                or t.id_user in (SELECT id_following_user FROM users_followers WHERE id_user = :id_user)
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id_user'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function removeTweet()
    {
        $query = "delete from tweets where id = :tweet_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':tweet_id', $this->__get('id'));
        $stmt->execute();

        return $this;
    }
}