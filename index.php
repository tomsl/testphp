<?php
  echo "huhu.tomsl.com";
  exit;
/**
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
 * Description
 *
 * GET Parameter
 * stopp=1		...		will stop also in GOOD Case, and display info.
 * admininfo=1  ...		will redirect to an admin overview page.
 * croncall     ...		will return '' or 'SERVUS failed'
 * 
 * internal Variables
 * 	$dbg		...  true -> will show debug info ( <!-- into html comments on output page --> )
 *
 * @author tomsl
 * @copyright (c) dialog-mail.com
 */
include_once 'INC.php';
include('_smarty/Smarty.class.php');

$ms_start = microtime(true); // start time for time measures.

$dbg = false; /* show some of debug info in <!-- output html comments --> */
$url_ok = "https://secure.dialog-mail.com?a=b";/** target url to forward e.g. https://secure.dialog-mail.com */
$admininfo = false; /* init; getparameter to show admin-info additional */
$stopp = false; /*  init; get parameter to stop auto-forward. */

// used to return shortened information by call with cronjob.
$cron_call= isset($_GET['croncall']) ? true : false;
if($cron_call){
	error_log(" called via CRONCALL ...");
}

$actual_host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

$get_stopp = isset($_GET['stopp']) ? $_GET['stopp'] : '';
if ($get_stopp == "1") {
	$stopp = true;
}
//$stopp = true; // TODO: -

$get_admininfo = isset($_GET['admininfo']) ? $_GET['admininfo'] : '';
if ($get_admininfo == "1")
{
    $admininfo = true;
    $stopp = true;
}
 
$get_test = isset($_GET['testmode']) ? $_GET['testmode'] : '';

$suite_result = "UDEF"; // init with 'UDEF'

$suite = new DmCheckSuite();

// HERE YOU can add the Checktargets you need.
$suite->addCheck("https://secure.dialog-mail.com/backend/healthcheck.php");
$suite->addCheck("https://secure.dialog-mail.de/backend/healthcheck.php");
$suite->addCheck("http://ag.d-mail2.com/backend/healthcheck.php");
//$suite->addCheck($actual_host . '/sleep.php?ms=5534');
	
	 
// used for testmodes ...	
if ($get_test == '') {
	//$suite->addCheck("https://secure.dialog-mail.de/backend/healthcheck.php");
}
else if ($get_test == 'ok') {
	error_log("in testmode OK ".fu());
	$suite->removeAllChecks();
	$suite->addCheck($actual_host . '/sleep.php?ms=213');
	$suite->addCheck($actual_host . '/sleep.php?ms=123');
	$stopp = true;
}
else if ($get_test == 'warn') {
	error_log("in testmode WARN ".fu());
	$suite->removeAllChecks();
	$suite->addCheck($actual_host . '/sleep.php?ms=123');
	$suite->addCheck($actual_host . '/sleep.php?ms=7000');
	$stopp = true;
}
else if ($get_test == 'error') {
	error_log("in testmode ERROR ".fu());
	$suite->removeAllChecks();
	$suite->addCheck($actual_host . '/sleep.php?ms=7000');
	$suite->addCheck($actual_host . '/sleep.php?ms=7000');
	$stopp = true;
}
else{
	error_log("in testmode UNDEFINIERT   ".fu());
}
	 

$suite_result = $suite->performeChecks();

/**
 * DEFAULT CASE ... no erros no stop ... -> forward to Login page ...  ;-)  
 */
	
if ($suite_result == "HEALTH" && !$stopp ) {
	// using 307 ... redirect temporally, against to strong caching (in browsers)
	if($cron_call){
		echo "";
		exit;
	}
	else{
	header('Location: ' . $url_ok, true, 307);	
	exit();
	}
}

if($cron_call){
	echo "SERVUS FAILED";
	exit;
}



// load Displaytext from 'http://www.itlooksorange.at/dialog-mail/notification.html' 
$displaytext_ok = $displaytext_warn = $displaytext_error = " ";
try{
	$displaytext_warn .= file_get_contents(NOTIFICATION_MSG_WARN_URL);
	$displaytext_error .= file_get_contents(NOTIFICATION_MSG_ERROR_URL);
	$displaytext_ok .= file_get_contents(NOTIFICATION_MSG_OK_URL);
}catch(Exception $e){
	// foo
}

// Smarty Stuff 
$smarty = new Smarty;
$smarty->assign('url_ok', $url_ok);
$smarty->assign('testresult', $suite_result);
$smarty->assign('thisyear', date('Y'));



if ($admininfo) {
	// Admininfo
	$smarty->assign('anz_test', $suite->getChecksCount());
	$smarty->assign('checksummary', $suite->getChecksummary());
	$smarty->assign('time_max', $suite->getMaxtesttime());
	$smarty->assign('time_consumed', $suite->getTime_used());
	$smarty->assign('time_performed', $suite->getTimePerforme());
	$smarty->assign('displaytext', "no displaytext for admininfo");
	$smarty->display( 'index_admininfo.htm');
}
else if ($suite_result == "HEALTH") {
	// here we come in, if stopp is TRUE.
	echo($dbg) ? " <h1>HEALTH </h1>" : '';
	echo($dbg) ? "\n<br/> auto forward to : <a href=\"$url_ok\" target=\"_blank\"> $url_ok </a> " : '';

	// everything ok, all tests performed and ok, just forward to target
	$smarty->assign('displaytext', $displaytext_ok);
	$smarty->display('index_state_health.htm');
}
else if ($suite_result == "WARNING") {
	// 
	if ($dbg) {
		echo "<h1>WARNING </h1>";
		echo "\n<br/> try to use : <a href=\"$url_ok\" target=\"_blank\"> $url_ok </a> ";
	}
	$smarty->assign('displaytext', $displaytext_warn);
	$smarty->display('index_state_warning.htm');
}
else if ($suite_result == "ERROR") {
	//
	if ($dbg) {

		echo"<h1>ERROR </h1>";
	}
	$smarty->assign('displaytext', $displaytext_error);
	$smarty->display('index_state_error.htm');
}
else {
	// OH OH ,,, 'FATAL'
	echo($dbg) ? " <h1>FATAL </h1>" : '';
	$smarty->display('index_state_health.htm');
}



if ($dbg) {
	echo "\n<hr/>";
	echo "\n<h1>" . $suite->getResult() . "</h1>";
	echo "<hr/>";
	echo "wait (max) for " . $suite->getTime_max() . " seconds ";
	echo!is_null($suite->getTime_used()) ? " :: effectiv used : " . $suite->getTime_used() . " seconds" : '';
	echo is_null($suite->getTime_used()) ? ' :: used full time ' : '';
	echo " for " . $suite->getChecksCount() . " checks ";
	echo "<hr/>";
	$s_detail = $suite->getChecksummary();
	echo "\n<pre>" . $s_detail . "</pre>";
}
?>