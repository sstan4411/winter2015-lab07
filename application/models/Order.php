<?php

class Order extends CI_Model {
    public $orderInstructions = "";
    protected $xml = null;
        public $burgers = array();
    public $total = 0.00;
    public $customer;
    public $type;



    // Constructor
    public function __construct($filename = null) {
        parent::__construct();
        if ($filename == null)
        {
            return;
        }
        
        $this->load->model('menu');
        
        $this->xml = simplexml_load_file(DATAPATH . $filename);

        // Assign the customer name
        $this->customer = (string) $this->xml->customer;
        
        // Assign the order type
        $this->type = (string) $this->xml['type'];
        
        // Assign the order instructions
        if (isset($this->xml->special))
        {
            $this->orderInstructions = (string) $this->xml->special;
        }
       
        $i = 0;
        
        foreach ($this->xml->burger as $burger)
        {
            $i++;
            
            $nBur = array(
                'patty' => $burger->patty['type']
            );
            $nBur['num'] = $i;
            $cheeses = "";
            
            if (isset($burger->cheeses['top']))
            {
                $cheeses .= $burger->cheeses['top'] . "(top), ";
            }
            
            if (isset($burger->cheeses['bottom']))
            {
                $cheeses .= $burger->cheeses['bottom'] . "(bottom)";
            }
            
            $nBur['cheese'] = $cheeses;

            $toppings = "";
            if (!isset($burger->topping))
            {
                $toppings .= "none";    
            }
            
            foreach($burger->topping as $topping)
            {
                $toppings .= $topping['type'] . ", ";
            }
            
            $nBur['toppings'] = $toppings;
            
            // Set sauces
            $sauces = "";
            
            // If we have no sauces
            if (!isset($burger->sauce))
            {
                $sauces .= "none";    
            }
            
            foreach($burger->sauce as $sauce)
            {
                $sauces .= $sauce['type'] . ", ";
            }
            
            $nBur['sauces'] = $sauces;
            
            // Set instructions if they exist
            if (isset($burger->instructions))
            {
                $nBur['instructions'] = (string) $burger->instructions;
            }
            else
            {
                $nBur['instructions'] = "";
            }
            
            // Assign costs
            $cost = $this->getBurgerCost($burger);
            
            $nBur['cost'] = $cost;
            $this->total += $cost;
                        
            // Add the new burger to the array
            $this->burgers[] = $nBur;
        }
    }
    
    private function getBurgerCost($burger)
    {
        $burTotal = 0.00;
        
        // Add the patty price to the total
        $burTotal += $this->menu->getPatty((string) $burger->patty['type'])->price;
        
        // Add the cheeses price to the total
        if (isset($burger->cheeses['top']))
        {
            $burTotal += $this->menu->getCheese((string) $burger->cheeses['top'])->price; 
        }
        
        if (isset($burger->cheeses['bottom']))
        {
            $burTotal += $this->menu->getCheese((string) $burger->cheeses['bottom'])->price; 
        }
        
        // get topping price and add it to the total
        foreach ($burger->topping as $topping)
        {
            $burTotal += $this->menu->getTopping((string) $topping['type'])->price; 
        }
        
        // get sauce price and add it to the total
        foreach ($burger->sauce as $sauce)
        {
            $burTotal += $this->menu->getSauce((string) $sauce['type'])->price; 
        }
        
        return $burTotal;
    }
            
}
