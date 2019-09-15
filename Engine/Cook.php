<?php
namespace Delivery\Engine;

class Cook{
	
	public $CookingEndTime; // When cook will finish cooking their last order
	
	
	public function __construct(){
		$this->CookingEndTime = 0;
	}
	
	// Prohibit outside properties setting
	public function __set( $param, $val ){}
	
	
	public function cookOrder( $order ){
		$cooking_begin_time = max( $this->CookingEndTime, $order->EntryTime );
		$this->CookingEndTime = $cooking_begin_time + $order->CookingTime;
	}
	
}
?>