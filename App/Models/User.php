<?php

namespace App\Models;

use MF\Model\Model;

class User extends Model
{
    private $id;
    private $name;
    private $email;
    private $password;
    
    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function getUserByEmail()
    {
        $query = "select name, email from users where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function validateUser() 
    {
        $errorParameter = [];

        if(strlen($this->__get('name')) < 3) {
            array_push($errorParameter, 'invalidName');
        }

        if(count($this->getUserByEmail()) != 0) {
            array_push($errorParameter, 'usedEmail');
        }

        if(strlen($this->__get('email')) < 5) {
            array_push($errorParameter, 'invalidMail');
        } else {
            $email = $this->__get('email');
            // check if e-mail address is well-formed
            if (!filter_var(filter_var($email, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)) {
                array_push($errorParameter, 'invalidMail');
            }
        }

        if(strlen($this->__get('password')) < 5) {
            array_push($errorParameter, 'shortPassword') ;
        }

        return $errorParameter;
    }

    public function saveUser()
    {
        $query = "insert into users(name, email, password)values(:name, :email, :password)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $this->__get('name'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':password', $this->__get('password'));
        $stmt->execute();

        return $this;
    } 

    public function authenticate()
    {
        $query = "select id, name, email from users where email = :email and password = :password";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':password', $this->__get('password'));
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!empty($user['id']) && !empty($user['name'])) {
            $this->__set('id', $user['id']);
            $this->__set('name', $user['name']);
        }
        return $this;
    }

    public function getAll()
    {
        $query = '
        select 
            u.id, u.name, u.email, 
            (
                select
                    count(*)
                from
                    users_followers as us
                where
                    us.id_user = :id_user and id_following_user = u.id
            ) as following_yn 
        from 
            users as u
        where 
            u.name like :name and u.id != :id_user';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', '%'.$this->__get('name').'%');
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function followUser($id_following_user)
    {
        $query = 'insert into users_followers(id_user, id_following_user)values(:id_user, :id_following_user)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->bindValue(':id_following_user', $id_following_user);
        $stmt->execute();

        return true;
    }

    public function unfollowUser($id_following_user)
    {
        $query = 'delete from users_followers where id_user = :id_user and id_following_user = :id_following_user';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->bindValue(':id_following_user', $id_following_user);
        $stmt->execute();

        return true;
    }

    public function getUserInfo()
    {
        $query = 'select name from users where id = :id_user';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTweetsCount()
    {
        $query = 'select count(*) as total_tweets from tweets where id_user = :id_user';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getFollowingCount()
    {
        $query = 'select count(*) as total_following from users_followers where id_user = :id_user';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getFollowersCount()
    {
        $query = 'select count(*) as total_followers from users_followers where id_following_user = :id_user';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}