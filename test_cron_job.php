<?php

	/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
	
$t = Date("y-m-d_H:m:s") . " cron " . __FILE__ . " done !";
echo $t;
error_log($t);



