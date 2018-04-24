<?php

	/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//$r = exec("tail _logs/".date("Ymd")."_chkmgr.log",$a,$b);
//$r = exec("cat ".__FILE__);
//$r = file_get_contents("_logs/".date("Ymd")."_chkmgr.log",FALSE,NULL,-10);
$r = file_get_contents("_logs/".date("Ymd")."_chkmgr.log",FALSE,NULL,0);
$r.="\n";
$r.= exec("date");

echo "<pre>";
echo $r;
echo "\n</pre>";



