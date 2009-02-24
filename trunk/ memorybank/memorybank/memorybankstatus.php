<?php
require_once('config.php');
$username = optional_param('username');
$user = get_record('user','username',$username);
if(empty($user))
{
	echo("$username is not setup for using Memory Banks");
	exit;
}
$currenttime = time();
$qcount = count_records_select('memorybank_schedule',"userid = {$user->id} AND nextviewing <= {$currenttime}");
if(empty($qcount))
{
	echo("Memorybanks are up-to-date for $username.  Check back after 3PM");
	exit;
}
echo("<a href='http://moodle2.seattleacademy.org/mod/memorybank/view.php' target='_blank'>$qcount questions for $username.  Click to proceed</a>");
?>
