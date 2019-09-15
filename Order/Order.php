<?php
namespace Delivery\Order;

class Order{
	
	public $Id;
	public $EntryTime;
	public $CookingTime;
	public $X, $Y;
	
	const TIME_LIMIT = 60;
	
	
	public function __construct( int $id, int $entry_time ){
		$this->Id = $id;
		$this->EntryTime = $entry_time;
		$this->CookingTime = random_int( 10, 30 );
		$this->X = random_int( -1000, 1000 );
		$this->Y = random_int( -1000, 1000 );
	}
	
	// Prohibit outside properties setting
	public function __set( $param, $val ){}
	
	public function __toString() : string{
		return $this->Id." ".$this->EntryTime." ".$this->CookingTime." ".$this->X." ".$this->Y;
	}
	
}
?>