<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller {

	public function __construct() {
		$this->userDB = new UserModel();
	}

	protected function isTempUser(){
		return (isset($_SESSION['tempUser'])) ? true : false;
	}

	protected function isUser(){
		return (isset($_SESSION['loggedUser'])) ? true : false;
	}

	protected function sumTodo(string $userId, int $count = 1){

		$email = $_SESSION['loggedUser']['email'];
        $pass = $_SESSION['loggedUser']['password'];
        $avatar = $_SESSION['loggedUser']['avatarUrl'];

		$this->userDB->modifyUser($userId, $email, $pass, $avatar, $count);
	
	}

	/**
	 * @param string $uri must start with slash. Eg. '/todo/list'
	 * @return void
	 */
	protected function redirect(string $uri = ''){
		header('Location: ' . WEB_ROOT . $uri);
        die();
	}

	protected function selfRedirect(){
		header('Location: ' . WEB_ROOT . substr($_SERVER['REQUEST_URI'], strlen(WEB_ROOT)));
		die();
	}

	/**
	 * @param string success|error|info the type of the msg. Valid values are only: error, info, success
	 * @param string $message the message to display
	 */
	protected function appMsg(string $type, string $message){
		$div = "<div class='${type}-msg'>${message}</div>";
		$_SESSION[$type] = $div;
	}

}

?>