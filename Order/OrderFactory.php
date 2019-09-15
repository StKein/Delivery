<?php
namespace Delivery\Order;

class OrderFactory{
	
	private $LastOrderTime;
	private $LastOrderId;
	
	
	public function construct(){
		// Manual ID incrementing
		$this->LastOrderId = 0;
	}
	
	
	public function makeOrder(): Order{
		// First order arrives at 0
		$this->LastOrderTime = 
			( $this->LastOrderId == 0 ) 
				? 0 
				: $this->LastOrderTime + random_int( 1, 30 );
		$this->LastOrderId++;
		
		return new Order( $this->LastOrderId, $this->LastOrderTime );
	}
	
}
?>