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
	'//' => 'index#index',
	'/auth/' => 'auth#login',
	'/auth/login/' => 'auth#login',
	'/auth/logout/' => 'auth#logout',
	'/auth/register/' => 'auth#register',
	'/todo/' => 'todo#new',
	'/todo/list/' => 'todo#list',
	'/todo/edit/' => 'todo#edit',
	'/todo/edit/:todoId' => 'todo#edit',
	'/todo/delete/:todoId' => 'todo#delete',
	'/todo/new/' => 'todo#new',
	'/todo/assign/' => 'todo#assign'
);
