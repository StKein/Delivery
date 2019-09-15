<?php
namespace Delivery\Engine;

class Courier{
	
	const TRAVEL_SPEED = 60;
	const START_COORDS = array( "x" => 0, "y" => 0 );
	
	
	public static function getTravelTime( $start_coords, $dest_coords ){
		return sqrt( 
				( $dest_coords["x"] - $start_coords["x"] ) ** 2 
				+ ( $dest_coords["y"] - $start_coords["y"] ) ** 2 
			) / 60;
	}
	
}
?>