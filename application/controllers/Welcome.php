<?php

/**
 * Our homepage. Show the most recently added quote.
 * 
 * controllers/Welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Welcome extends Application {

    function __construct()
    {
	parent::__construct();
        $this->load->helper('directory');
    }
    
    function index()
    {
        $this->load->model('order');
        
	// Build a list of orders
        $dir = directory_map('./data/');
        $files = array();
        
        // Loop through each file
        foreach ($dir as $file)
        {
            // check if the file is an XML file
            if (strpos($file, 'order') !== false && strpos($file, '.xml') !== false)
            {        
                $order = new Order($file);
                //add file to the array
                $files[] = array(
                    'filename' => substr($file, 0, strlen($file) - 4),
                    'customer' => $order->customer
                );
            }
        }
	$this->data['orders'] = $files;
	$this->data['pagebody'] = 'homepage';
	$this->render();
    }

    function order($filename)
    {
        $this->load->model('order');
        $order = new Order($filename . '.xml');
	$this->data['filename'] = $filename;
        $this->data['customer'] = $order->customer;
        $this->data['type'] = $order->type;
        $this->data['burgers'] = $order->burgers;
        $this->data['total'] = $order->total;
        $this->data['special'] = $order->orderInstructions;
	$this->data['pagebody'] = 'justone';
	$this->render();
    }
    

}
