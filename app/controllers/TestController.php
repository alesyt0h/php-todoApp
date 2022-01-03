<?php

class TestController extends ApplicationController
{
	public function indexAction()
	{
		$this->view->message = "hello from test::index";
        $this->view->setTitle('Test Page');
        // $this->view->appendScript('main.js');
        // $this->view->appendCSS('style.css');
	}
	
	public function checkAction()
	{
		echo "hello from test::check";
	}
}
