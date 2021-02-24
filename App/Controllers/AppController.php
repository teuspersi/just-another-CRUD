<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
    public function timeline()
    {
        $this->validateAuth();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_user', $_SESSION['id']);

        //variaveis de paginacao
        $total_tweets_page = 10;
        $page = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $desloc = ($page - 1)*$total_tweets_page;
       
        $tweets = $tweet->getPerPage($total_tweets_page, $desloc);
        
        $total_tweets = $tweet->getTotalRegistries();
        $this->view->total_pages = $total_pages = ceil($total_tweets['total']/$total_tweets_page);
        $this->view->active_page = $page;

        $this->view->tweets = $tweets;

        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);

        $this->view->userinfo = $user->getUserInfo();
        $this->view->tweetsCount = $user->getTweetsCount();
        $this->view->following = $user->getFollowingCount();
        $this->view->followers = $user->getFollowersCount();

        $this->render('timeline');   
    }

    public function tweet()
    {

        $this->validateAuth();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_user', $_SESSION['id']);

        $tweet->saveTweet();
            
         header('Location: /timeline');
    }

    public function validateAuth()
    {
        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['name']) || $_SESSION['name'] == '') {
            header('Location: /?login=erro');

        } else {
            return true;

        }
    }

    public function connectPeople()
    {
        $this->validateAuth();
       
        $searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : ''; 

        $users = [];

        if($searchBy != '') {
            $user = Container::getModel('User');
            $user->__set('name', $searchBy);
            $user->__set('id', $_SESSION['id']);
            $users = $user->getAll();
        }
        
        $this->view->searchResult = $users;

        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);

        $this->view->userinfo = $user->getUserInfo();
        $this->view->tweetsCount = $user->getTweetsCount();
        $this->view->following = $user->getFollowingCount();
        $this->view->followers = $user->getFollowersCount();

        $this->render('connectPeople');
    }

    public function action()
    {
        $this->validateAuth();

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : ''; 
        $id_following_user = isset($_GET['user_id']) ? $_GET['user_id'] : '';

        $user = Container::getModel('User');
        $user->__set('id', $_SESSION['id']);

        if($action == "follow") {
            $user->followUser($id_following_user);
            header('Location: /connect_people?searchBy='.$searchBy);

        } else if($action == "unfollow") {
            $user->unfollowUser($id_following_user);
            header('Location: /connect_people?searchBy='.$searchBy);

        }
    }

    public function deleteTweet()
    {
        $this->validateAuth();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id', $_POST['tweet_id']);

        $tweet->removeTweet();
            
         header('Location: /timeline');
    }
}