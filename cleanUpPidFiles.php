<?php

include 'INC.php';
/*
 * CLEAN UP old temporary files.
 * Script to be called from a Cron or manually.
 * Clean up ALL Files from CONF_PID_INFO_PATH that are older than CONF_PIDFILE_MAXAGE.
 * Configuration please make in INC.php.
 */
$dbg = DBG;

$time_now = microtime(true);
$my_folder = CONF_PID_FILEPATH;

echo " time now " . $time_now;
echo ($dbg) ? "\n<br/> " : '';
echo ($dbg) ? "Delete all files in <code>" . $my_folder . "</code>" : '';
echo ($dbg) ? " that are older than <code>" . CONF_PIDFILE_MAXAGE . "</code> seconds." : '';
echo ($dbg) ? "\n<br/>" : '';

echo ($dbg) ? "\n<br/> my_folder: $my_folder ... " : '';

$files = scandir($my_folder);
$count_del = 0;

foreach ($files as $filename) {
	$filename = $my_folder . $filename;
	echo ($dbg) ? "\n<br/>" . $filename : "";
	if (!startsWith($filename, $my_folder . "pid_")) {
		echo ($dbg) ? $filename . " ~~ skipped \n" : '';
		continue;
	}
	$file = file($filename);

	if (is_dir($filename)) {
		echo ($dbg) ? " _FOLDER" : "";
	}
	else if (is_file($filename)) {
		// && strpos($a, '.info') !== false
		// wenn es ein File ist, und die Endung  .info lautet.
		echo ($dbg) ? " _FILE " : "";

		$filetime = filectime($filename);
		echo ($dbg) ? " $filetime" : "";
		$myfileage = $time_now - $filetime;
		echo ($dbg) ? " age : " . $myfileage : '';
		if ($myfileage > CONF_PIDFILE_MAXAGE) { /** use seconds */
			echo ($dbg) ? " ready for delete ;-) " : '';
			$del_ok = unlink($filename);
			$count_del ++;
			echo ($dbg) ? " ... und gleich gemacht \n" : '';
		}
	}
	else {
		echo ($dbg) ? " ~ \n" : '';
	}
}
echo ($dbg) ? "\n<br/> " : '';
echo "\n[ $count_del ] \n";
