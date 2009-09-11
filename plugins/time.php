<?php

function return_times() {
  global $date_boundaries;
  global $time_boundaries;
  global $time_increments;

  $times = array();

  foreach($date_boundaries as $date => $test) {
  	$date_parts = explode('-', $date);
  	$day = mktime(0,0,0,$date_parts[1], $date_parts[2], $date_parts[0]);
  	$start = $day + $time_boundaries['start'];
  	$current = $start;
  	$end = $day+$time_boundaries['end'];
  	while($current < $end) {
  	  $times[] = $current;
  	  $current += $time_increments;
  	}
  }
  return $times;
}
