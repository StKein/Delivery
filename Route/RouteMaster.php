<?php
namespace Delivery\Route;

class RouteMaster{
	
	const COOKS_LIMIT = 2;
	
	private $Deliveries; // Deliveries info: coords, order, ready/deadline time
	private $Cooks;
	private $Routes;
	
	
	public function __construct( $orders ){
		$this->Cooks = array();
		for( $i = 0; $i < self::COOKS_LIMIT; $i++ ){
			$this->Cooks[] = new \Delivery\Engine\Cook();
		}
		$this->setDeliveriesInfo( $orders );
		$this->setRoutes();
	}
	
	public function getRoutes(){
		return $this->Routes;
	}
	
	
	private function setDeliveriesInfo( $orders ){
		$this->Deliveries = array();
		for( $i = 0; $i < count( $orders ); $i++ ){
			$delivery_info = array();
			$delivery_info["coords"] = array(
					"x" => $orders[$i]->X,
					"y" => $orders[$i]->Y
				);
			$delivery_info["order"] = $orders[$i]->Id;
			$delivery_info["time_deadline"] = $orders[$i]->EntryTime + \Delivery\Order\Order::TIME_LIMIT;
			$this->Cooks[0]->cookOrder( $orders[$i] );
			$delivery_info["time_ready"] = $this->Cooks[0]->CookingEndTime;
			usort( $this->Cooks, function( $cook_1, $cook_2 ){
				if( phpversion()[0] == "7" ){
					return ( $cook_1->CookingEndTime <=> $cook_2->CookingEndTime );
				} else {
					return ( $cook_1->CookingEndTime > $cook_2->CookingEndTime ) ? 1 : -1;
				}
			} );
			$this->Deliveries[] = $delivery_info;
		}
	}
	
	private function setRoutes(){
		$this->Routes = array();
		$i = 0;
		$route = new Route();
		$deliveries_count = count( $this->Deliveries );
		for( $i = 0; $i < $deliveries_count; $i++ ){
			$delivery_added = $route->addDelivery( $this->Deliveries[$i] );
			if( $delivery_added != 1 ){
				// Delivery is not added - save route, start a new one
				$route->finalizeRoute();
				$this->Routes[] = $route;
				$route = new Route();
				if( $delivery_added == -1 ){
					// Delivery not kept in last route - add it to new one
					$route->addDelivery( $this->Deliveries[$i] );
				}
			}
			unset( $this->Deliveries[$i] );
		}
		$route->finalizeRoute();
		$this->Routes[] = $route;
	}
	
}
?>