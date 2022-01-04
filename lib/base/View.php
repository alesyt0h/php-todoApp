<?php

/**
 * A class for handling the view logic of the system
 *
 * @author jimmiw
 * @since 2012-06-27
 */
class View
{
	// used for holding the content of the view script
	protected $_content = "";
	// the standard layout
	protected $_layout = 'layout';
	
	protected $_viewEnabled = true;
	protected $_layoutEnabled = true;
	
	// initializes the data array
	protected $_data = array();
	// initializes the page title
	protected $_pageTitle = APP_TITLE;
	
	public $settings = null;
	
	public function __construct()
	{
	  $this->settings = new stdClass();
	}

	/**
	 * Renders the view script, and stores the output
	 * @param string $viewScript the path to the view script. E.g.: 'global/header.phtml'
	 * @param boolean $extraView specify if this is an extra view to return directly the output buffer, default value is false
	 * @return string|false the output buffer in case $extraView was set to true 
	 */
	protected function _renderViewScript($viewScript, $extraView = false)
	{
		// starts the output buffer
		ob_start();
		
		// includes the view script
		include(ROOT_PATH . '/app/views/scripts/' . $viewScript);
		
		// returns the content of the output buffer
		if ($extraView) {
			return ob_get_clean();
		} else {
			$this->_content = ob_get_clean();
		}
	}
	
	/**
	 * Fetches the content of the current view script
	 */
	public function content()
	{
		return $this->_content;
	}
	
	/**
	 * Renders the current view.
	 */
	public function render($viewScript)
	{
	  if ($viewScript && $this->_viewEnabled) {
  		// renders the view script
  		$this->_renderViewScript($viewScript);
	  }
		
	  if ($this->_isLayoutDisabled()) {
	    echo $this->_content;
	  }
	  else {
  		// includes the current view, which uses the "$this->content()" to output the 
  		// view script that was just rendered
  		include(ROOT_PATH . '/app/views/layouts/' . $this->_getLayout() . '.phtml');
	  }
	}
	
	/**
	 * Renders the given data as json
	 * @param mixed $data
	 */
	public function renderJson($data)
	{
	  $this->disableView();
	  $this->disableLayout();
	  
	  // sets the json headers
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/json');
	  
	  echo json_encode($data);
	}
	
	protected function _getLayout()
	{
		return $this->_layout;
	}
	
	public function setLayout($layout)
	{
		$this->_layout = $layout;
		
		if ($layout) {
		  $this->_enableLayout();
		}
	}
	
	public function disableLayout()
	{
	  $this->_layoutEnabled = false;
	}
	
	public function disableView()
	{
	  $this->_viewEnabled = false;
	}
	
	/**
	 * stores the given data on the given key
	 * @param string $key the key to store the data under
	 * @param mixed $value the value to store
	 */
	public function __set($key, $value)
	{
		// stores the data
		$this->_data[$key] = $value;
	}
	
	/**
	 * Returns the data if it exists, else nul
	 * @param string $key the data to look for
	 * @return mixed the data found or null
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->_data)) {
			return $this->_data[$key];
		}
		
		return null;
	}
	
	/**
	 * The base url is used if the application is located in a subfolder. Use
	 * this function when linking to things.
	 * @return string the baseUrl for the application.
	 */
	public function baseUrl()
	{
		return WEB_ROOT;
	}
	
	/**
	 * Sets the page title
	 * @param string $title the page title
	 */
	public function setTitle($title)
	{
		$this->_pageTitle = $title;
	}

	/**
	 * Adds a new JavaScript to the header.
	 * @param string $script the name of the JavaScript to add
	 */
	public function appendScript($script)
	{
		echo '<script type="text/javascript" src="' . $this->baseUrl() . '/js/' . $script . '"></script>';
	}

	/**
	 * Adds a new CSS Stylesheet to the header.
	 * @param string $stylesheet the name of the Stylesheet to add
	 */
	public function appendCSS($stylesheet)
	{
		echo '<link rel="stylesheet" href="' . $this->baseUrl() . '/css/' . $stylesheet . '">';
	}

	/**
	 * Sets the layout to be used
	 */
	protected function _enableLayout()
	{
	  $this->_layoutEnabled = true;
	}
	
	/**
	 * Tests if the layout is disabled
	 */
	protected function _isLayoutDisabled()
	{
	  return !$this->_layoutEnabled;
	}
}
