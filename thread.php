<?php
/*
 * Value of counter start from 0 and goes to last ID from mysql table where urls are saved
 */
class Counter extends Threaded {

	public function __construct($value = -1) {
		$this->value = $value;
	}

	protected function increment() { return ++$this->value; }
	protected function decrement() { return --$this->value; }

	protected $value;
}


/*
 *
 */
class Process extends Thread {
	
	protected $counter;

	// konstruktor
	public function __construct($website_url, $trigger, Counter $counter )
	{
		$this->website_url = $website_url;
		$this->trigger = $trigger;
		$this->counter = $counter;
		
	}
	
	// pokretanje threada
	public function run() {	
			
		$address = new Link();

		while (($job = $this->counter->increment() <= 6000))
		{
						
			$url = $address->getLink($this->counter->value);
			if($address->isChecked($url) == false )
			{
				if($url)
				{
					crawl_site($url, $this->trigger);
				}
			}
		}
	
	}
}