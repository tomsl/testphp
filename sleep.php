<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$sleeptime = isset($_GET['ms']) ? $_GET['ms'] : 0;

$ms = $sleeptime; // get sleeptime by GET Parameter 'ms'
$ns = $ms * 1000; // from ms -> ns
 
usleep($ns);
?>
ALIVE 
<?php
echo "\n\n<pre>\n$sleeptime\n</pre>";
?>