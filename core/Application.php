<?php

namespace core;

class Application {

	private Router $router;
	private Request $request;
	private Response $response;

	public function __construct() {
		$this->request = new Request();
		$this->response = new Response();
		$this->router = new Router($this->request, $this->response);
	}
    // Set secure session cookie parameters before starting the session.
	public function run(): void {
		
		session_set_cookie_params([
			'lifetime' => 0,        
			'path'     => '/',
			'domain'   => '',       
			'secure'   => false,    
			'httponly' => true,     
		]);
		session_start();
		$this->router->resolve();
	}
	

}
