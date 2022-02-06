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

	protected function sumTodo(int $count = 1){
		
		$email = $_SESSION['loggedUser']['email'];
        $pass = $_SESSION['loggedUser']['password'];
        $avatar = $_SESSION['loggedUser']['avatarUrl'];

		$this->userDB->modifyUser($email, $pass, $avatar, $count);
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

	protected function refererRedirect(){
		(isset($_SERVER['HTTP_ORIGIN'])) ? $location = $_SERVER['HTTP_ORIGIN'] : $location = WEB_ROOT;
		header('Location: ' . $location );
		die();
	}

	/**
	 * @param string success|error|info the type of the msg. Valid values are only: error, info, success
	 * @param string $message the message to display
	 */
	protected function appMsg(string $type, string $message){

		$newMsg = explode("<br>", $message);

		if(count($newMsg) > 1){

			$message = '';

			foreach ($newMsg as $key => $value) {
				if(strlen($value)){
					$result = preg_replace('/^/','<li>', $value);
					$result = preg_replace('/$/','</li>', $result);
	
					$message .= $result;
				}
			}
			
			$padding = 'p-4';
		} else {
			$padding = 'p-2';
		}

		switch ($type) {
			case 'error':
				$typeMsg = 'bg-red-100 text-red-800 border-red-200';
				break;
			case 'success':
				$typeMsg = 'bg-green-100 text-green-800 border-green-200';
				break;
			case 'info':
				$typeMsg = 'bg-sky-100 text-sky-800 border-sky-200';
				break;
		}

		$div = "<div id='appMsg' class='${typeMsg} ml-auto mr-auto max-w-[264px] vsm:max-w-[404px] sm:max-w-[500px] md:max-w-[655px] transition-all rounded-md ${padding} pr-10 duration-[600ms] relative shadow border-2 list-disc'>
					<svg xmlns='http://www.w3.org/2000/svg' class='absolute right-1 top-1 h-4 w-4 cursor-pointer m-1 text-gray-700' fill='none' viewBox='0 0 24 24' stroke='currentColor' onclick='appMsgHidder(this)'>
						<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12' />
					</svg>
					${message}
				</div>";

		$_SESSION[$type] = $div;
	}

}

?>