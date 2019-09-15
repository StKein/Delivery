<?php
namespace Delivery\Route;

class Route{
	
	private $Deliveries;
	private $StartTime;
	
	const DELIVERIES_LIMIT = 3;
	
	
	public function __construct(){
		$this->Deliveries = array();
	}
	
	public function __toString(){
		return implode( " ", array_map( function( $delivery ){
			return $delivery["order"]." ".$delivery["time_delivery"];
		}, $this->Deliveries ) );
	}
	
	
	/***
			Try to add delivery to route
			Return:
				-1 - can't add, add this delivery to new route
				0 - can't deliver in time (unlucky arrival/cooking timing), 
					but keep it in route since it's the only one there
				1 - added delivery, all good
	***/
	public function addDelivery( $delivery ){
		if( count( $this->Deliveries ) == self::DELIVERIES_LIMIT ){
			return -1;
		}
		
		$this->Deliveries[] = $delivery;
		$this->StartTime = $delivery["time_ready"];
		if( !$this->routeIsValid() ){
			if( count( $this->Deliveries ) > 1 ){
				array_pop( $this->Deliveries );
				$this->StartTime = $this->Deliveries[ count( $this->Deliveries ) - 1 ]["time_ready"];
				return -1;
			} else {
				return 0;
			}
		}
		
		return 1;
	}
	
	public function finalizeRoute(){
		$this->setDeliveryTimes();
	}
	
	
	// Check if deliveries in route can be made in time
	private function routeIsValid(){
		$route_validator = new RouteValidator( $this->Deliveries, $this->StartTime );
		if( !$route_validator->routeIsValid() ){
			return false;
		}
		// If deliveries are rearranged by validator, apply their new order
		if( $route_validator->DeliveriesRearranged ){
			$this->Deliveries = $route_validator->getDeliveries();
		}
		
		return true;
	}
	
	private function setDeliveryTimes(){
		$curr_time = $this->StartTime;
		$curr_coords = \Delivery\Engine\Courier::START_COORDS;
		for( $i = 0; $i < count( $this->Deliveries ); $i++ ){
			$travel_time = \Delivery\Engine\Courier::getTravelTime(
					$curr_coords,
					$this->Deliveries[$i]["coords"]
				);
			$curr_time += $travel_time;
			$curr_coords = $this->Deliveries[$i]["coords"];
			$this->Deliveries[$i]["time_delivery"] = floor( $curr_time );
		}
	}
	
}
?>