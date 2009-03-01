<?php

function nextViewingDay()

/**
 * Finds the time for an item schedule for the next 
 * scheuled viewing session
 * Find today, add the scheduled hour
 * If that time is in the past, add one day
 *
 * @param none
 * @return int time for next viewing day
 **/
{
	global $CFG;
	//echo(time().'<br>');
	$nextTime = strtotime('tomorrow')+$CFG->mod_memorybank_rebuildtime*60*60;
	if ($nextTime > time()+ 24*60*60) $nextTime -= 24*60*60;
	//echo($nextTime);
    return $nextTime;
	
}

function make_question($memorybank){
	global $USER, $COURSE,$CFG;
	$thequestion = optional_param('question', null);
	$answer = optional_param('answer', null);
	$reference = optional_param('reference', null);
	$initialgrade = optional_param('initialgrade', 4);
	$themod = optional_param('category', 0);
	$visible = optional_param('visible', 0);
	//if(empty($visible))
		//$visible = 0;
	$initviewtime = optional_param('initviewtime',0);
	if(!empty($initviewtime)){
		$initviewtime = make_timestamp($initviewtime['year'], $initviewtime['month'], $initviewtime['day'], $initviewtime['hour'], $initviewtime['minute']);
	}
	$question = new object();
	$question->modid = $memorybank->id;
	$question->courseid = $COURSE->id;
	$question->createdby = $USER->id;
	$question->modifiedby = $USER->id;
	$question->timecreated = time();
	$question->timemodified = $question->timecreated;
	$question->initialgrade = $initialgrade;
	$question->initialhalflife = $CFG->mod_memorybank_defaulthalflife;  //one week; perhaps this should be configured someplace.
	$question->question = $thequestion;
	$question->reference = $reference;
	$question->answer = $answer;
	$question->visible = $visible;
	$question->initviewtime = $initviewtime;
	
	if(!empty($question->question)){
		$question->id = insert_record('memorybank_bank', $question);
		schedule_question($question);
		return $question;
	}
}


function update_questionbank()

{
	
	global $USER,$FULLME;
	$myurl = new moodle_url($FULLME);
	$myurl->param('first','second');
	//print_object($myurl->get_query_string());
	//notice('die please');
	$qid = optional_param('qid', null);
	$theQuestion = optional_param('question', null);
	if(empty($theQuestion)) return;
	
	$question = get_record('memorybank_bank','id',$qid);
	print_object($question);

	$question->question = optional_param('question', null);
	$question->answer = optional_param('answer', null);
	$question->reference = optional_param('reference', null);
	$question->initialgrade = optional_param('initialgrade', 4);
	$question->category = optional_param('category', 0);
	$question->visible = optional_param('visible', 0);
	$initviewtime = optional_param('initviewtime', 0);
	$question->initviewtime = make_timestamp($initviewtime['year'], $initviewtime['month'], $initviewtime['day'], $initviewtime['hour'], $initviewtime['minute']);
	$question->modifiedby = $USER->id;
	$question->timemodified = time();
	
	print_object($question);
	update_record('memorybank_bank', $question);
}



function print_main_page($instid,$action='a')
{
    global $CFG,$USER;


    $currenttime = time();
    $thequestion = '-';
    $thereference = '-';
    $theanswer = '-';
    $qid = '';
	$thecategory = 0;
	$id = optional_param('id',0);
	
    $qcount = count_records_select('memorybank_schedule',"userid = {$USER->id} AND nextviewing <= {$currenttime}");


    $select = "SELECT s.*";
    $from = "  FROM {$CFG->prefix}memorybank_schedule s
        INNER JOIN {$CFG->prefix}memorybank_bank b ON (s.questionid=b.id)    
    ";
	
    $where = " WHERE s.userid = {$USER->id} AND s.nextviewing <= {$currenttime} AND b.modid = '{$instid}'";
    $order = " ORDER BY s.nextviewing ASC";

    $sql = $select.$from.$where.$order;
    if (!$schedules = get_records_sql($sql)) {
       // echo('Error: No questions');
		//die;
    }
    else
    {
        $qcount = count($schedules);
        $schedule = array_shift($schedules);
		
        $select = "SELECT q.*";
        $from = "  FROM {$CFG->prefix}memorybank_bank q";
        $where = " WHERE q.id = {$schedule->questionid}";
        $order = " ORDER BY q.id DESC";
        $limit = " LIMIT 1";
		$sql = $select.$from.$where.$order.$limit;
         
        if (!$questions = get_records_sql($sql)) {
            echo('ERROR: Questions missing');
        }
        foreach ($questions as $question) {
            break;
        }

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        
		$qid = $question->id;
        $thequestion = format_text($question->question,FORMAT_MOODLE,$formatoptions);
        $thereference = format_text($question->reference,FORMAT_MOODLE,$formatoptions);
        $theanswer = format_text($question->answer,FORMAT_MOODLE,$formatoptions);
		$thecategory = $question->modid;
    }
    $catlock = optional_param('catlock','false')=='true'; 
    $catmenu = choose_from_menu(category_menu(), 'category',$thecategory,get_string('all_categories', 'memorybank'),'','0',true,$catlock);
	$isteacher = false;
	global $USER;
	//echo $USER->username;
	//echo $CFG->mod_memorybank_teachers;
	$teachers = array('ganderson', 'bwoodman', 'admin','dchapin','connorgrosnick');
	//echo(count($teachers));
	//echo(' ');
	if (!empty($CFG->mod_memorybank_teachers)) $teachers = explode(',',$CFG->mod_memorybank_teachers);
	if (in_array($USER->username, $teachers,false)) {
    $isteacher = true;
	}
	//print_r( $teachers);
	//echo(count($teachers));
	//$isteacher = true;
    include('mainform.html');
}






function schedule_question($question)

{
    global $USER,$COURSE;

    if(!$question->question)
    {
        echo ('ERROR: no question here!');

        return; //Temp fix
    }
if(empty($question->id))
{
    echo('zero quesiton id');
    return;
}

    $users = get_users_by_capability(get_context_instance(CONTEXT_COURSE, $COURSE->id), 'moodle/course:view','u.id, u.firstname, u.lastname', 'lastname ASC, firstname DESC', '', '','','',true);

    	
    if ($users){
        foreach($users as $user){
            $schedule->userid = $user->id;
            $schedule->questionid = $question->id;
            $schedule->halflife = $question->initialhalflife;
            $schedule->nextviewing = $question->initviewtime;
            $schedule->lastgrade = $question->initialgrade;
			$schedule->viewcount = 0;
            if (!insert_record('memorybank_schedule', $schedule)){
                error ("Could not schedule question for user $user->id");
            }
        }
    }
}

function print_edit_page($instid,$action='a')
{
		
    global $CFG,$USER;

    $thequestion = '';
    $thereference = '';
    $theanswer = '';

	$what  = optional_param('what');
	$qid  = optional_param('qid',0);
	
	if($qid){

    $select = "SELECT q.*";
    $from = "  FROM {$CFG->prefix}memorybank_bank q";
    $where = " WHERE q.id = {$qid}";
    $order = " ORDER BY q.id DESC";
    $limit = " LIMIT 1";


    $sql = $select.
    $from.
    $where.
    $order.
    $limit;
     
    if (!$questions = get_records_sql($sql)) {
        echo('ERROR: Questions missing');
    }
    foreach ($questions as $question) {
        break;
    }

    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $formatoptions->para = false;
    	
    $thequestion = format_text($question->question,FORMAT_MOODLE,$formatoptions);
    $thereference = format_text($question->reference,FORMAT_MOODLE,$formatoptions);
    $theanswer = format_text($question->answer,FORMAT_MOODLE,$formatoptions);
	}
	
    $catlock = optional_param('catlock');


    include('editquestion.html');
}

function category_menu()
{
	global $CFG;
	    $sql = "SELECT id as modid,course as courseid,name as modname from {$CFG->prefix}memorybank";
    $cats = get_records_sql($sql);
	
	$catlist = array();
    foreach ($cats as $cat) {
        $cat->coursename = get_field('course','shortname','id',$cat->courseid);
		$catlist[$cat->modid]= $cat->coursename.'-'.$cat->modname;//.'('.$cat->thecount.')';
    }

	return $catlist;
}
?>