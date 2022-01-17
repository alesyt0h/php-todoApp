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
		return (isset($_SESSION['tempUser']) && count($_SESSION['tempUser'])) ? true : false;
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
		// TODO the alert keeps it's position in the DOM with opacity: 0 and/or visibility: invisible. Display: none should be used in order to remove it's position in the DOM, however this makes the fade out animation unable to play.
		$div = "<div class='${type}-msg transition-all duration-[400ms]'>
					<svg xmlns='http://www.w3.org/2000/svg' class='float-right h-4 w-4 cursor-pointer m-1 text-gray-700' fill='none' viewBox='0 0 24 24' stroke='currentColor' onclick='this.parentElement.classList.add(\"opacity-0\", \"invisible\")'>
						<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' />
					</svg>
					${message}
				</div>";
		$_SESSION[$type] = $div;
	}

}

?>