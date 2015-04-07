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
	public function __construct($website_url, $mandatory, Counter $counter )
	{
		$this->website_url = $website_url;
		$this->mandatory = $mandatory;
		$this->counter = $counter;
		
	}
	
	// thread
	public function run() 
	{
			
		$address = new Link();
		$lastID  = 6000;

		while (($job = $this->counter->increment() <= $lastID))
		{
			set_time_limit(30); // reset max execution time of page
			
			$url = $address->getLink($this->counter->value);
			
			if($address->isChecked($url) == false )
			{
				if($url)
				{
					crawl_site($url, $this->mandatory);
				}
			}
		}
	
	}
}