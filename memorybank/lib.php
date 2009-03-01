<?php
/**
 * Library of functions and constants for module memorybank.
 */
define("MEMORYBANK_DATE_FORMAT", "n/d/Y H:i");  //This formatting works for US Excel, an unfortunate formatting.


$memorybank_DEFAULT_HALFLIFE = 7*24*60*60;
/// Note prefix to keep values unambigious

/**
 * Given an object containing all the necessary data, 
 * (defined by the form in mod.html) this function 
 * will create a new instance and return the id number 
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted memorybank record
 **/
function memorybank_add_instance($memorybank)
{
	
	$memorybank->timecreated = time();
	$memorybank->timemodified = $memorybank->timecreated;
	
	return insert_record("memorybank", $memorybank);
}


function memorybank_update_instance($memorybank)
{
	
	$memorybank->timemodified = time();
	$memorybank->id = $memorybank->instance;
	
	return update_record("memorybank", $memorybank);
}


function memorybank_delete_instance($id)
{
	
	if(!$memorybank = get_record("memorybank", "id", "$id"))
	{
		return false;
	}
	
	$result = true;
	
	
	if(!delete_records("memorybank", "id", "$memorybank->id"))
	{
		$result = false;
	}
	
	//Note that question bank questions are orphaned at this point.
	return $result;
}

/**
 * Return a small object with summary information about what a 
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function memorybank_user_outline($course, $user, $mod, $memorybank)
{
	//return the last time viewed and number of viewings
	$return = true;
	return $return;
}

/**
 * Print a detailed representation of what a user has done with 
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function memorybank_user_complete($course, $user, $mod, $memorybank)
{
	//Return time viewed, number of views, average score?, etc?
	return true;
}

/**
 * Given a course and a time, this module should find recent activity 
 * that has occurred in memorybank activities and print it out. 
 * Return true if there was output, or false is there was none. 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function memorybank_print_recent_activity($course, $isteacher, $timestart)
{
	global $CFG;
	
	return false;
	//  True if anything was printed, otherwise false
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such 
 * as sending out mail, toggling flags etc ... 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function memorybank_cron()
{
	global $CFG;
	
	return true;
}

/**
 * Must return an array of grades for a given instance of this module, 
 * indexed by user.  It also returns a maximum allowed grade.
 * 
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $memorybankid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function memorybank_grades($memorybankid)
{
	//Perhaps this should return a percent of items that were not defered
	return NULL;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of memorybank. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $memorybankid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function memorybank_get_participants($memorybankid)
{
	return false;
}

/**
 * This function returns if a scale is being used by one memorybank
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $memorybankid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function memorybank_scale_used($memorybankid, $scaleid)
{
	$return = false;
	
	//$rec = get_record("memorybank","id","$memorybankid","scale","-$scaleid");
	//
	//if (!empty($rec)  && !empty($scaleid)) {
	//    $return = true;
	//}
	
	return $return;
}

/**
 * Checks if scale is being used by any instance of memorybank.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any memorybank
 */
function memorybank_scale_used_anywhere($scaleid)
{
	return false;
}

/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function memorybank_install()
{
	return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function memorybank_uninstall()
{
	//This would be a good place to remove the preference for time of day for rescheduling.
	return true;
}


function print_memorybank_report1($instid, $course = false)
{
	global $CFG,$USER;
	require_once $CFG->libdir . '/tablelib.php';

    $tablecolumns = array('commands', 'id', 'question', 'number_of_answers', 'average_score');
	$tableheaders = array(get_string('commands', 'memorybank'), get_string('question_id', 'memorybank'), get_string('question_text', 'memorybank'),get_string('number_of_answers', 'memorybank'),get_string('average_score', 'memorybank'));
	$table = new flexible_table('memorybank_report1');
	$table->define_columns($tablecolumns);
	$table->define_headers($tableheaders);
    $table->sortable(true, 'id');
    $table->no_sorting('commands');
	$table->set_attribute('cellspacing', '0');
	$table->set_attribute('cellpadding', '5');
	$table->set_attribute('id', 'memorybank-report');
	$table->set_attribute('class', 'boxaligncenter generaltable');
	$table->column_class('commands', 'controls');
	$table->setup();
	$sort = $table->get_sql_sort();
	
	$SQL = "
        SELECT 
          {$CFG->prefix}memorybank_bank.id,
          {$CFG->prefix}memorybank_bank.question,
          {$CFG->prefix}memorybank_bank.visible,
          {$CFG->prefix}memorybank_submissions.qid,
          COUNT({$CFG->prefix}memorybank_submissions.qid) as number_of_answers,
          AVG({$CFG->prefix}memorybank_submissions.grade) as average_score
        FROM
         {$CFG->prefix}memorybank_bank
         LEFT OUTER JOIN {$CFG->prefix}memorybank_submissions ON ({$CFG->prefix}memorybank_bank.id={$CFG->prefix}memorybank_submissions.qid)
        WHERE 
          {$CFG->prefix}memorybank_bank.modid = {$instid}
        GROUP BY
          {$CFG->prefix}memorybank_bank.id,
          {$CFG->prefix}memorybank_bank.question,
          {$CFG->prefix}memorybank_submissions.qid
        ORDER BY {$sort}
	";
	
	$results = get_records_sql($SQL, 0, 500);
	if (is_array($results)) {
    	foreach($results as $result)
    	{
    		$commands = "<a href=\"view.php?instid={$instid}&amp;what=delete&amp;qid={$result->id}\"><img src=\"{$CFG->pixpath}/t/delete.gif\" /></a>";
    		if($result->visible){
    		  $commands .= "<a href=\"view.php?instid={$instid}&amp;what=showhide&amp;qid={$result->id}&amp;view=edit\"><img src=\"{$CFG->pixpath}/t/hide.gif\" /></a>";
    		}
    		else
    		{
    			$commands .= "<a href=\"view.php?instid={$instid}&amp;what=showhide&amp;qid={$result->id}&amp;view=edit\"><img src=\"{$CFG->pixpath}/t/show.gif\" /></a>";
    		}
    		//if ($result->number_of_answers)
			$commands .= "<a href=\"view.php?what=answerlist&amp;instid={$instid}&amp;qid={$result->id}\"><img src=\"{$CFG->pixpath}/t/log.gif\" /></a>";
    		$commands .= "<a href=\"view.php?what=edit&amp;instid={$instid}&amp;qid={$result->id}\"><img src=\"{$CFG->pixpath}/t/edit.gif\" /></a>";
    		$avg_score = ($result->number_of_answers) ? round($result->average_score, 2) : '-';
    		$table->data[] = array($commands,$result->id, $result->question, (int)$result->number_of_answers, $avg_score);
    	}
	}
    $strroletoassign = get_string("listallbanks", "memorybank");
    $options = array();
//    $options = array('0'=>get_string('listallbanks', 'memorybank').'...');
    foreach (get_all_instances_in_course("memorybank", $course) as $memorybank) {
        $options[$memorybank->id] = $memorybank->name;
    }
    
    echo '<div style="text-align: center; padding: 20px;">';
    popup_form("$CFG->wwwroot/mod/memorybank/view.php?what=questionlist&instid=",
        $options, 'instid', $instid, '', '', '', false, 'self', '');
    echo '
    <style>
        .controls img {
            padding: 0 3px;
            cursor: pointer;
        }
    </style>    
    </div>';
	
	if (isset($table->data)) {
	    $table->print_html();
	} else {
	    echo 'no data';
	}
	print_footer();
	die;
}

function print_memorybank_report2($instid, $course = false)
{
	global $CFG,$USER;
	require_once $CFG->libdir . '/tablelib.php';

    $tablecolumns = array('lastname', 'last_access', 'average_score','number_of_questions');
	$tableheaders = array(get_string('name', 'memorybank'), get_string('last_access', 'memorybank'), get_string('average_score', 'memorybank'),get_string('number_of_questions', 'memorybank'));
	$table = new flexible_table('memorybank_report2');
	$table->define_columns($tablecolumns);
	$table->define_headers($tableheaders);
    $table->sortable(true, 'lastname');
    $table->no_sorting('number_of_questions');
	$table->set_attribute('cellspacing', '0');
	$table->set_attribute('cellpadding', '5');
	$table->set_attribute('id', 'memorybank-report');
	$table->set_attribute('class', 'boxaligncenter generaltable');
	$table->setup();
	$sort = $table->get_sql_sort();
	
	$SQL = "
        SELECT 
         {$CFG->prefix}user.id,
         {$CFG->prefix}user.firstname,
	     {$CFG->prefix}user.lastname,
	     AVG({$CFG->prefix}memorybank_submissions.grade) AS average_score,
	     MAX({$CFG->prefix}memorybank_submissions.timeanswered) AS last_access
        FROM
         {$CFG->prefix}user
         INNER JOIN {$CFG->prefix}memorybank_schedule ON ({$CFG->prefix}user.id={$CFG->prefix}memorybank_schedule.userid)
         LEFT OUTER JOIN {$CFG->prefix}memorybank_submissions ON ({$CFG->prefix}memorybank_schedule.userid={$CFG->prefix}memorybank_submissions.userid) AND ({$CFG->prefix}memorybank_schedule.questionid={$CFG->prefix}memorybank_submissions.qid)
         INNER JOIN {$CFG->prefix}memorybank_bank ON ({$CFG->prefix}memorybank_schedule.questionid={$CFG->prefix}memorybank_bank.id)
        WHERE
          ({$CFG->prefix}memorybank_bank.modid = '{$instid}')
        GROUP BY
          {$CFG->prefix}user.id
        ORDER BY {$sort}
  	";
	$results = get_records_sql($SQL, 0, 500);
	if (is_array($results)) {
    	foreach($results as $result)
    	{
    	    $result->name = $result->firstname . ' ' . $result->lastname;
    	    $data[$result->id] = array($result->name, $result->last_access ? date(MEMORYBANK_DATE_FORMAT, $result->last_access): '', $result->average_score);
    	}
	}	
	
	$SQL = "
        SELECT
          {$CFG->prefix}user.id,
          COUNT({$CFG->prefix}memorybank_bank.id) as number_of_questions
        FROM
         {$CFG->prefix}user
         INNER JOIN {$CFG->prefix}memorybank_schedule ON ({$CFG->prefix}user.id={$CFG->prefix}memorybank_schedule.userid)
         INNER JOIN {$CFG->prefix}memorybank_bank ON ({$CFG->prefix}memorybank_schedule.questionid={$CFG->prefix}memorybank_bank.id)
        WHERE
          ({$CFG->prefix}memorybank_bank.modid = '{$instid}') AND
          ({$CFG->prefix}memorybank_schedule.nextviewing > NOW())
        GROUP BY
          {$CFG->prefix}user.id
    ";
	$results = get_records_sql($SQL, 0, 500);
	if (is_array($results)) {
    	foreach($results as $result)
    	{
    	    $data[$result->id][] = $result->number_of_questions;
    	}
	}	
	$table->data = $data;
	
    $options = array();
//    $options = array('0'=>get_string('listallbanks', 'memorybank').'...');
    foreach (get_all_instances_in_course("memorybank", $course) as $memorybank) {
        $options[$memorybank->id] = $memorybank->name;
    }
    
    echo '<div style="text-align: center; padding: 20px;">';
    popup_form("$CFG->wwwroot/mod/memorybank/view.php?what=studentlist&instid=",
        $options, 'instid', $instid, '', '', '', false, 'self', '');
    echo '</div>';
	
	$table->print_html();
	print_footer();
	die;
}

function print_memorybank_report3($qid)
{
	global $CFG,$USER;
	require_once $CFG->libdir . '/tablelib.php';
                  
    $tablecolumns = array('id', 'username', 'grade', 'timeanswered', 'currenthalflife');
	$tableheaders = array('answer id', 'student', 'grade', 'date', 'halflife, days');
	$tableheaders = array(get_string('answer_id', 'memorybank'), get_string('student', 'memorybank'), get_string('grade', 'memorybank'),get_string('date', 'memorybank'),get_string('halflife', 'memorybank'));
	
	$table = new flexible_table('memorybank_report3');
	$table->define_columns($tablecolumns);
	$table->define_headers($tableheaders);
    $table->sortable(true, 'id');
	$table->set_attribute('cellspacing', '0');
	$table->set_attribute('cellpadding', '5');
	$table->set_attribute('id', 'memorybank-report');
	$table->set_attribute('class', 'boxaligncenter generaltable');
	$table->setup();
	$sort = $table->get_sql_sort();
	
	$SQL = "
        SELECT 
          {$CFG->prefix}memorybank_submissions.id,
          CONCAT({$CFG->prefix}user.lastname, ' ',  {$CFG->prefix}user.firstname) as username,
          {$CFG->prefix}memorybank_submissions.grade,
          {$CFG->prefix}memorybank_submissions.timeanswered,
          {$CFG->prefix}memorybank_submissions.currenthalflife
        FROM
         {$CFG->prefix}memorybank_submissions
         INNER JOIN {$CFG->prefix}user ON ({$CFG->prefix}memorybank_submissions.userid={$CFG->prefix}user.id)
        WHERE 
          {$CFG->prefix}memorybank_submissions.qid = {$qid}
        ORDER BY {$sort}
	";
	
	$results = get_records_sql($SQL, 0, 500);
	$i = sizeof($results);
	foreach($results as $result)
	{
		$table->data[] = array($result->id, $result->username, $result->grade, date(MEMORYBANK_DATE_FORMAT, $result->timeanswered), $result->currenthalflife / 86400);
	}
	
	global $FULLME;
//	print_header_simple('report','','','',"<meta http-equiv='Refresh' content='30;$FULLME'>");
	
/*    echo '<div style="text-align: center; padding: 20px;">';
    popup_form("$CFG->wwwroot/mod/memorybank/view.php?what=answerlist&qid={$qid}&date",
        $options, 'instid', $instid, '', '', '', false, 'self', '');
    echo '</div>'; // here we need to privide some filter
*/	
    echo '
    <style>
        .controls img {
            padding: 0 3px;
            cursor: pointer;
        }
    </style><br>';
	
	$table->print_html();
	print_footer();
	die;
}

?>
