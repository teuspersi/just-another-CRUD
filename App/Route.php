<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['inscreverse'] = array(
			'route' => '/inscreverse',
			'controller' => 'indexController',
			'action' => 'inscreverse'
		);

		$routes['register'] = array(
			'route' => '/register',
			'controller' => 'indexController',
			'action' => 'register'
		);

		$routes['authenticate'] = array(
			'route' => '/authenticate',
			'controller' => 'AuthController',
			'action' => 'authenticate'
		);

		$routes['timeline'] = array(
			'route' => '/timeline',
			'controller' => 'AppController',
			'action' => 'timeline'
		);

		$routes['exit'] = array(
			'route' => '/exit',
			'controller' => 'AuthController',
			'action' => 'exit'
		);

		$routes['tweet'] = array(
			'route' => '/tweet',
			'controller' => 'AppController',
			'action' => 'tweet'
		);

		$routes['connect_people'] = array(
			'route' => '/connect_people',
			'controller' => 'AppController',
			'action' => 'connectPeople'
		);

		$routes['action'] = array(
			'route' => '/action',
			'controller' => 'AppController',
			'action' => 'action'
		);

		$routes['deleteTweet'] = array(
			'route' => '/deleteTweet',
			'controller' => 'AppController',
			'action' => 'deleteTweet'
		);

		$this->setRoutes($routes);
	}

}

?>