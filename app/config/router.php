<?php

/* setup the routes */
$config['routes'] = array();

/*
these are test as follows
current request + currently matched route from above regex
ie. get/main/user/don/18
get is prepended to the matched url
*/

$config['requests'] = array(
	'#^get(.*)$#i' => ''
);
