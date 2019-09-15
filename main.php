<?php
namespace Delivery;

spl_autoload_register( function($class){
    // Cut root namespace
    $class = str_replace( __NAMESPACE__."\\", "", $class );
    // Correct directory separator
    $class = str_replace( array( "\\", "/" ), DIRECTORY_SEPARATOR, __DIR__.DIRECTORY_SEPARATOR.$class.".php" );
    // Get file real path
	require_once realpath( $class );
} );

function clearContent(){
	// Clear console content
	if( strncasecmp( PHP_OS, "win", 3 ) === 0 ){
		popen('cls', 'w');
	} else {
		exec('clear');
	}
}

// Preparation
$factory = new Order\OrderFactory();
$orders_count = random_int( 10, 100 );
$orders = [];
// Make orders and output them
clearContent();
print("Orders:\r\n");
for( $i = 0; $i < $orders_count; $i++ ){
	$order = $factory->makeOrder();
	$orders[] = $order;
	echo( "{$order}\r\n" );
}
// Make routes for orders, output them
$routes = ( new Route\RouteMaster($orders) )->getRoutes();
print("\r\nRoutes:\r\n");
for( $i = 0; $i < count($routes); $i++ ){
	print( "{$routes[$i]}\r\n" );
}
?>