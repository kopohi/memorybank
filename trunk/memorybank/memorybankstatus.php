<?php
require_once ('../../config.php');
require_once ('locallib.php');
global $CFG;
$username = optional_param('username');
$user = get_record('user', 'username', $username);
if (empty($user)){
	echo (get_string('no_such_user', 'memorybank', $username));
	//"$username is not setup for using MemoryBanks");
	exit;
}
$currenttime = time();
$qcount = count_records_select('memorybank_schedule', "userid = {$user->id} AND nextviewing <= {$currenttime}");
if (empty($qcount)){
	
	echo (get_string('questions_completed', 'memorybank', userdate(nextViewingDay(),'%H:%M')));
	exit;
	
}
echo ("<a href='$CFG->wwwroot/mod/memorybank/view.php' target='_blank'>$qcount" . get_string("userQcount", "memorybank", $username) . "</a>");
?>
