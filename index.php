<?php
ob_start();

require __DIR__ . '/ay/includes/bootstrap.inc.php';

// Mini-dispatcher.
$template			= array
(
	'file'			=> 'error',
	'title'			=> NULL,
	'class'			=> NULL
);

$templates			= array('requests', 'request', 'uris', 'hosts', 'function');

if(empty($_GET['ay']['template']))
{
	$_GET['ay']['template']	= 'hosts';
}

if(!in_array($_GET['ay']['template'], $templates))
{
	throw new Exception('Invalid template.');
}

$template['file']	= $_GET['ay']['template'];

// This additional step is taken to make URLs pretty. I am aware that [] should be urlencoded. However,
// that makes the URLs unreadable. It is handly to be able to emmend parameters directly in the URL query.
// Furthermore, this strips out empty parameters.
if(!empty($_POST['ay']['query']))
{
	$query		= array_filter($_POST['ay']['query']);
	
	ay_redirect(xhprof_url($template['file'], $query));
}

// $_GET['xhprof']['query'] is used throughout the code to filter data. NULL value will be ignored.
// This is a convenience method to prevent repetitious variable presence checking.
if(empty($_GET['xhprof']['query']))
{
	$_GET['xhprof']['query']	= NULL;
}
else
{
	foreach($_GET['xhprof']['query'] as $e)
	{
		if(is_array($e))
		{
			throw new Exception('Defining a filter with a multidimensional array is not supported.');
		}
	}
	
	// ay_input() will look for the default input value in this globally accessible variable.
	$input	= array('query' => $_GET['xhprof']['query']);

	if(!empty($_GET['xhprof']['query']['datetime_from']) && !xhprof_validate_datetime($_GET['xhprof']['query']['datetime_from']))
	{
		ay_message('Invalid <mark>from</mark> date-time format.');
	}
	
	if(!empty($_GET['xhprof']['query']['datetime_to']) && !xhprof_validate_datetime($_GET['xhprof']['query']['datetime_to']))
	{
		ay_message('Invalid <mark>to</mark> date-time format.');
	}
	
	if(isset($_GET['xhprof']['query']['host'], $_GET['xhprof']['query']['host_id']))
	{
		ay_message('<mark>host_id</mark> will overwrite <mark>host</mark>. Unset either to prevent unexpected results.');
	}
	
	if(isset($_GET['xhprof']['query']['uri'], $_GET['xhprof']['query']['uri_id']))
	{
		ay_message('<mark>uri_id</mark> will overwrite <mark>uri</mark>. Unset either to prevent unexpected results.');
	}
}

$xhprof_data_obj	= new XHProfData($config['pdo']);

ob_start();
require AY_ROOT . '/ay/templates/' . $template['file'] . '.tpl.php';
$template['body']	= ob_get_clean();

require AY_ROOT . '/ay/templates/frontend.layout.tpl.php';

unset($_SESSION['ay']['flash']);