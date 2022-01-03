<?php 

/**
 * Used to define the routes in the system.
 * 
 * A route should be defined with a key matching the URL and an
 * controller#action-to-call method. E.g.:
 * 
 * '/' => 'index#index',
 * '/calendar' => 'calendar#index'
 * '/test/' => To match the URL exactly as is, it should end in '/', otherwise /tests will redirect to /test
 */
$routes = array(
	'/' => 'index#index'
);
