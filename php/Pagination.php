<?php
class Pagination{
    var $baseURL        = 	'';
    var $totalRows      = 	'';
    var $perPage        = 	10;
    var $numLinks       =  	3;
    var $currentPage    =  	1;
		var $delta					= 	2;
    
    function __construct($params = array()){
        if (count($params) > 0){
            $this->initialize($params);        
        }
    }
    
    function initialize($params = array()){
        if (count($params) > 0){
            foreach ($params as $key => $val){
                if (isset($this->$key)){
                    $this->$key = $val;
                }
            }        
        }
				
				//invalid currentPage value
				if ( ! is_numeric($this->currentPage))
          $this->currentPage = 1;
    }
		
		function createLinks(){
			//zero rows case
			if($this->totalRows == 0 || $this->perPage == 0)
				return '';
			
			//calculate total number of pages
			$totalPages = ceil($this->totalRows / $this->perPage);
			
			$leftSide = $this->currentPage - $this->delta;
			$rightSide = $this->currentPage + $this->delta + 1;
			
			//output content
			$output = '';
			
			//generate previous link if possible
			if($this->currentPage > 1)
				$output .= "<a data-value='" . ($this->currentPage - 1) . "'><i class='fa fa-angle-left' aria-hidden='true'></i></a>";
			
			//generator numbers
			for($i = 1; $i <= $totalPages; $i++){
				if($i == 1 || $i == $totalPages || $i >= $leftSide && $i < $rightSide){
					if($this->currentPage == $i)
						$output .= "<span>" . $i . "</span>";
					else
						$output .= "<a data-value='" . $i . "'>" . $i . "</a>";
					
				}
				if($leftSide - 2 == $i || $rightSide == $i && $rightSide != $totalPages )
					$output .= "...";
			}
			
			//generate next link if possible
			if($this->currentPage < $totalPages)
				$output .= "<a data-value='" . ($this->currentPage+1) . "'><i class='fa fa-angle-right' aria-hidden='true'></i></a>";			
			
			return $output;
		}
    
		function showing(){
			return 'Znaleziono <strong>' . $this->totalRows . '</strong> wyników';
		}
		
}
?>