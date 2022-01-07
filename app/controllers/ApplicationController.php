<?php

/**
 * Base controller for the application.
 * Add general things in this controller.
 */
class ApplicationController extends Controller {

	public function __construct() {
		$this->userDB = new UserModel();
	}

	protected function isLoggedIn(){

		if($this->_action === 'logout'){
			return false;
		}

		if(isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true){
			header('Location: ' . WEB_ROOT);
		} else {
			return false;
		}
	}

}
