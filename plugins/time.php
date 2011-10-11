<?php

function returnTimes() {
  global $date_boundaries;
  global $time_boundaries;
  global $time_increments;

  $times = array();

  foreach($date_boundaries as $date => $test) {
  	$date_parts = explode('-', $date);
  	if(count($date_parts) > 2){
    	$day = mktime(0,0,0,$date_parts[1], $date_parts[2], $date_parts[0]);
  		$start = $day + $time_boundaries['start'];
  		$current = $start;
  		$end = $day+$time_boundaries['end'];
  		while($current < $end) {
  			  $times[] = $current;
  	 		 $current += $time_increments;
  		}
	  }
  }
  return $times;
}

function tabularTimes() {
  $times = returnTimes();
  $return = array();
  for($i = 0; $i < count($times); $i++) {
    if (!isset($return[date('i', $times[$i])])) {
      $return[date('i', $times[$i])] = array();
    }
    $return[date('i', $times[$i])][date('H', $times[$i])] = $times[$i];
  }
  return $return;
}
