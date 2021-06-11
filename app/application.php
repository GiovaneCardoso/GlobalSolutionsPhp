<?php

/**
 * load all classes
 */



ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

function project_path()
{
    return str_replace('/public', '', $_SERVER['DOCUMENT_ROOT']);
}

require_once 'classes.php';


function route( $controller, $action, $request = false, $method = 'GET' )
{
    if( $_SERVER['REQUEST_METHOD'] != $method ) return responseJson([
        'error' => 'request method not allowed'
    ]);

    include_once project_path(). '/controllers/'.$controller.'.php';

    $instance = new $controller();

    if($request)
        return $instance->{$action}( $_REQUEST );

    else
        return $instance->{$action}();
}

/**
 * @param $data
 */
function responseJson( $data )
{
    header('Content-Type: application/json');

    echo json_encode($data);

    return $data;
}