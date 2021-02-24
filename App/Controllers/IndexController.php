<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action 
{
	public function index() {
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function inscreverse() {
		$this->view->user = [
			'name' => '',
			'email' => '',
			'password' => ''
		];

		$this->render('inscreverse');
	}

	public function register()
	{
		$user = Container::getModel('User');
		$user->__set('name', $_POST['name']);
		$user->__set('email', $_POST['email']);
		$user->__set('password', $_POST['password']);

		if(count($user->validateUser()) == 0) {
			$user->__set('password', md5($_POST['password']));
			$user->saveUser();
			$this->render('register');
		} else {
			$this->view->user = [
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'password' => $_POST['password']
			];

			$this->view->error = [
				'registerError' => serialize($user->validateUser())
			];

			$this->render('inscreverse');
		}
	}
}


?>