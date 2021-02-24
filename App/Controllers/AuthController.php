<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action
{
    public function authenticate()
    {
        $user = Container::getModel('User');
        $user->__set('email', $_POST['email']);
        $user->__set('password', md5($_POST['password']));

        $user->authenticate();

        if($user->__get('id') != '' && $user->__get('name') != '') {
            session_start();

            $_SESSION['id'] = $user->__get('id');
            $_SESSION['name'] = $user->__get('name');

            header('Location: /timeline');
        } else {
            header('Location: /?login=erro');
        }
    }

    public function exit() {
        session_start();
        session_destroy();
        header('Location: /');
    }
}