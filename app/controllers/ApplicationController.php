<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller {

	public function __construct() {
		$this->userDB = new UserModel();
	}

	public function isTempUser(){
		return (isset($_SESSION['tempUser'])) ? true : false;
	}

	public function isUser(){
		return (isset($_SESSION['loggedUser'])) ? true : false;
	}

	public function sumTodo(string $userId, int $count = 1){

		$email = $_SESSION['loggedUser']['email'];
        $pass = $_SESSION['loggedUser']['password'];
        $avatar = $_SESSION['loggedUser']['avatarUrl'];

		$this->userDB->modifyUser($userId, $email, $pass, $avatar, $count);
	
	}

	/**
	 * @param string $uri must start with slash. Eg. '/todo/list'
	 * @return void
	 */
	public function redirect(string $uri = ''){
		header('Location: ' . WEB_ROOT . $uri);
        die();
	}

}

?>