<?php


	define('DBG',FALSE);

	define('CONF_APP_ROOT', dirname(__file__)."/" );  

	define('CONF_LOG_FILEPATH', CONF_APP_ROOT."_logs/");
	define('CONF_PID_FILEPATH', CONF_APP_ROOT."_pids/");
	define('CONF_APP_PATH', CONF_APP_ROOT.'checkmgr/');

	include_once CONF_APP_PATH . 'DmCheck.php';
	include_once CONF_APP_PATH . 'DmCheckSuite.php';


	// URL OF MESSAGE TEXT 
	// for Warning and Error page
	define('NOTIFICATION_MSG_URL',"http://www.itlooksorange.at/dialog-mail/notification.html");
	define('NOTIFICATION_MSG_OK_URL',"http://www.itlooksorange.at/dialog-mail/notification_ok.html");
	define('NOTIFICATION_MSG_WARN_URL',"http://www.itlooksorange.at/dialog-mail/notification_warn.html");
	define('NOTIFICATION_MSG_ERROR_URL',"http://www.itlooksorange.at/dialog-mail/notification_error.html");



define('CONF_PIDFILE_MAXAGE', 180); // defines the max age to store up pid files // in seconds


/**
 * Script Time for Overall - Script.
 * works in fullAutomation() ...
 * Defines the max Script Runtime for the Tests
 */
/////////////////////////////
////  TIMES AND LIMITS 
/////////////////////////////

//define('CONF_SCRIPT_TIME', 6.2); // Max Check Wait Time // value in Seconds ( e.g. 3.14 )
// 
//
///** */
// 
//define('CONF_LOG_FILE', CONF_APP_ROOT . CONF_APP_PATH . '_logs/'.date("Ymd").'_some.log'); // Path to the LOG FILE.
// 
//define('CONF_PIDFILE_MAXAGE', 60 * 60 * 24 * 10 ); // in seconds
//
///** */
//define('CONF_CHECK_WORKER', CONF_APP_ROOT . CONF_APP_PATH . 'c_check.php'); // worker file for doing checks		
////define("CONF_MAIL_WORKER", CONF_APP_ROOT . CONF_APP_PATH . 'c_mail.php'); // worker file for sending mails


 

/* */

//////////////////////////////////////// END DEFINES ///////////////////////////
/////////////////////////////////////// HELP FUNCTIONS /////////////////////////
/**
 * 
 * @param type $filename
 * @param type $data
 * 
 * https://erikeldridge.wordpress.com/2008/10/26/php-atomic-write-function/
 */
function atomic_put_contents($filename, $data) {
	$fp = fopen($filename, "w+");
	if (flock($fp, LOCK_EX)) {
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
	}
	fclose($fp);
}

function append_log($filename, $data) {
	file_put_contents($filename, $data, FILE_APPEND);
}

/**
 * Returns file name and linenumber of the before func ... for bugtracking.
 * @return type String
 */
function fu() {
	$bb = debug_backtrace();
	$line = $bb[0]['line'];
	$fil = $bb[0]['file'];
	$func = isset($bb[1]['function']) ? "#" . $bb[1]['function'] : '';
	$file = substr($fil, -15);
	$ret = " ~ $file:$line$func";
	return $ret;
}

/**
 * 
 * @return type String like fu(), but with "\n<br/>" in front of.
 */
function fu2() {
	$bb = debug_backtrace();
	$line = $bb[0]['line'];
	$fil = $bb[0]['file'];
	$func = isset($bb[1]['function']) ? "#" . $bb[1]['function'] : '';
	$file = substr($fil, -15);
	$ret = " ~ $file:$line$func";
	return "\n<br/>" . $ret;
}

/*
 * simple echo what fu2() returns 
 */

function fu3() {
	$bb = debug_backtrace();
	$line = $bb[0]['line'];
	$fil = $bb[0]['file'];
	$func = isset($bb[1]['function']) ? "#" . $bb[1]['function'] : '';
	$file = substr($fil, -15);
	$ret = " ~ $file:$line$func";
	echo "\n<br/> " . $ret;
}

/**
 * Returns TRUE if the given URL Exists and returns HEADER 200.
 * @param type $url
 * @return type
 */
function UR_exists($url) {
	$headers = get_headers($url);
	return stripos($headers[0], "200 OK") ? true : false;
}

function my_filter_int($var) {
	return is_int($var) ? $var : null;
}

/// HelpFunction 
function startsWith($haystack, $needle) {
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

/**
 * @return type String with microtime, that has striped out the '.'
 */
function MyMicrotime() {
	$str = microtime(true) . "";
	return str_replace('.', "", $str);
}


/**
 * Returns a file name with the given pid (id)
 * @param string $pid
 * @return type String  'pid_[$pid].info'
 */
function getPidFileName($pid){
	if($pid==""){
		$pid = "nono";
	}
	return "pid_".$pid.".info";
}
function getPidPath(){
	return CONF_PID_FILEPATH;
}
function getLogPath(){
	return CONF_LOG_FILEPATH;
}

function RandomString($length=8)
{
    $characters = '0123456789ABCDEF';
    $randstring = '';
    for ($i = 0; $i < $length; $i++) {
        $randstring .= $characters[ rand(0, strlen($characters)-1) ];
    }
    return $randstring;
}