<?php
namespace Delivery\Route;

/***
		Class for route validation
		Checks if all route deliveries can be made in time
***/
class RouteValidator{
	
	public $DeliveriesRearranged;
	
	private $Deliveries;
	private $StartTime;
	private $TravelTimeMatrix;
	
	
	public function __construct( $deliveries, $start_time ){
		$this->Deliveries = $deliveries;
		$this->StartTime = $start_time;
		$this->DeliveriesRearranged = false;
	}
	
	// Prohibit outside DeliveriesRearranged setting
	public function __set( $param, $val ){}
	
	
	public function routeIsValid(){
		$time_delivery_to_first = \Delivery\Engine\Courier::getTravelTime(
				\Delivery\Engine\Courier::START_COORDS,
				$this->Deliveries[0]["coords"]
			);
		if( $this->StartTime + $time_delivery_to_first > $this->Deliveries[0]["time_deadline"] ){
			// Deliveries are sorted by entry (and thus, deadline) time by default
			// So if we can't make the first delivery, no need to check further
			return false;
		} elseif( count( $this->Deliveries ) == 1 ){
			return true;
		}
		
		$this->setTravelTimeMatrix();
		// Get all possible delivery orders
		switch( count( $this->Deliveries ) ){
			case 2:
				$placements = [ [0, 1], [1, 0] ];
				break;
			case 3:
				$placements = [ [0, 1, 2], [0, 2, 1], [1, 0, 2], [1, 2, 0], [2, 0, 1], [2, 1, 0] ];
				break;
			// Add more if route delivery limit increases
		}
		for( $i = 0; $i < count( $placements ); $i++ ){
			if( $this->deliveryOrderMeetsDeadlines( $placements[$i] ) ){
				if( $i > 0 ){
					// If we used not default order, rearrange deliveries
					$this->rearrangeDeliveries( $placements[$i] );
				}
				return true;
			}
		}
		// No delivery order meets deadline(s) - route not valid
		return false;
	}
	
	public function getDeliveries(){
		return $this->Deliveries;
	}
	
	
	/***
		Set travel time for all paths to avoid calculating each one from scratch in every check iteration
	***/
	private function setTravelTimeMatrix(){
		$this->TravelTimeMatrix = array();
		for( $i = 0; $i < count( $this->Deliveries ); $i++ ){
			$this->TravelTimeMatrix[$i] = array();
			for( $j = 0; $j < count( $this->Deliveries ); $j++ ){
				$start_coords = ( $i == $j ) ? \Delivery\Engine\Courier::START_COORDS : $this->Deliveries[$j]["coords"];
				$this->TravelTimeMatrix[$i][$j] = 
					( isset( $this->TravelTimeMatrix[$j][$i] ) ) 
						? $this->TravelTimeMatrix[$j][$i] 
						: \Delivery\Engine\Courier::getTravelTime( $start_coords, $this->Deliveries[$i]["coords"] );
			}
		}
	}
	
	private function deliveryOrderMeetsDeadlines( $order ){
		$curr_time = $this->StartTime;
		$prev_delivery_index = $order[0];
		for( $i = 0; $i < count( $order ); $i++ ){
			$curr_time += $this->TravelTimeMatrix[ $prev_delivery_index ][ $order[$i] ];
			if( $curr_time > $this->Deliveries[ $order[$i] ]["time_deadline"] ){
				return false;
			}
			$prev_delivery_index = $order[$i];
		}
		
		return true;
	}
	
	private function rearrangeDeliveries( $order ){
		$deliveries = array();
		for( $i = 0; $i < count( $order ); $i++ ){
			$deliveries[$i] = $this->Deliveries[ $order[$i] ];
		}
		$this->Deliveries = $deliveries;
		$this->DeliveriesRearranged = true;
	}
	
}
?>