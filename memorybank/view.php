<?php
/**
 * This page prints a particular instance of memorybank
 *
 * @author Gary Anderson
 * @package memorybank
 **/

/// 
    require_once("../../config.php");
    require_once("lib.php");
	require_once("locallib.php");
	require_once($CFG->libdir . '/formslib.php');
	global $USER;

	$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $instid  = optional_param('instid', 0, PARAM_INT);  // memorybank ID
    $qid  = optional_param('qid', 0, PARAM_INT);  // question ID
	$lasttime  = optional_param('starttime');  // From last time
	$what  = optional_param('what');
	//$items  = optional_param('items');
	$level  = optional_param('level');
	$deleteconfirmed  = optional_param('deleteconfirmed');
	$qid  = optional_param('qid',0, PARAM_INT);
if(empty($id) && empty($instid)){
	$instid = 1;
	$username  = optional_param('username');
	global $FULLME;
	//print_r($_SERVER['HTTP_REFERER']);
	//echo('hhhhh');die($username);

	if (!empty($username)){
	require_logout();
	$user = get_record('user','username',$username);
	  //print_r($user);
	  //authenticate_user_login($username, $user->password);
	//die;
	complete_user_login($user);
	}
	require_login();
	}
    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $memorybank = get_record("memorybank", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } 
    if ($qid) {
        if (! $memorybank_bank = get_record("memorybank_bank", "id", $qid)) {
            error("Question id is incorrect");
        }
        if (! $course = get_record("course", "id", $memorybank_bank->courseid)) {
            error("Course is misconfigured");
        }
    } 
    if ($instid) {
        if (! $memorybank = get_record("memorybank", "id", $instid)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $memorybank->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("memorybank", $memorybank->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    if(!empty($what))
	{
		if($what==='delete')
		{
			global $FULLME;
			if(empty($deleteconfirmed)){
		notice_yesno(get_string("ask_delete_item", "memorybank"),
                                   strip_querystring($FULLME)."?instid=$memorybank->id&qid=$qid&what=delete&deleteconfirmed=true",
                                    strip_querystring($FULLME)."?instid=$memorybank->id&qid=$qid");
							  }else
							  {
			delete_records("memorybank_schedule", "questionid", $qid);
			delete_records("memorybank_bank", "id", $qid);
			delete_records("memorybank_submissions", "qid", $qid);
			unset($qid);
			}
		}
		
		if($what==='catlock')
		{
			$catlock=true;
		}
		
		if($what==='showhide')
		{
			global $FULLME;

			$qid  = optional_param('qid',null);
			
	
			$question = get_record('memorybank_bank','id',$qid);
			$question->visible = !$question->visible;
	
			$question->modifiedby = $USER->id;
			$question->timemodified = time();
			update_record('memorybank_bank', $question);
			global $FULLME;
			redirect(strip_querystring($FULLME).'?what=questionlist&instid=' . $instid);
		}
		
		
		if(in_array($what, array('questionlist', 'answerlist', 'studentlist'))) {
		    $strmemorybanks = get_string("modulenameplural", "memorybank");
            $navlinks = array();
            $navlinks[] = array('name' => $strmemorybanks, 'link' => "index.php?id=$course->id", 'type' => 'activity');
            if (!empty($memorybank->id)) $navlinks[] = array('name' => format_string($memorybank->name), 'link' => "{$GLOBALS['CFG']->wwwroot}/mod/memorybank/view.php?id={$cm->id}", 'type' => 'activityinstance');
		}
		if($what==='questionlist')
		{
            $navlinks[] = array('name' => get_string("question_report", "memorybank"), 'link' => '', 'type' => '');
            $navigation = build_navigation($navlinks);
        	print_header_simple('report','',$navigation,'',"<meta http-equiv='Refresh' content='30;$FULLME'>");
			print_memorybank_report1($memorybank->id, $course);
		}
		if($what==='answerlist')
		{
            $navlinks[] = array('name' => get_string("answer_report", "memorybank"), 'link' => '', 'type' => '');
            $navigation = build_navigation($navlinks);
        	print_header_simple('report','',$navigation,'',"<meta http-equiv='Refresh' content='30;$FULLME'>");
			print_memorybank_report3($qid);
		}
		if($what==='studentlist')
		{
            $navlinks[] = array('name' => get_string("student_report", "memorybank"), 'link' => '', 'type' => '');
            $navigation = build_navigation($navlinks);
        	print_header_simple('report','',$navigation,'',"<meta http-equiv='Refresh' content='30;$FULLME'>");
			print_memorybank_report2($memorybank->id, $course);
		}
		if($what==='add')
		{
			global $FULLME;

			$question  = optional_param('question',null);

			if(!empty($question))
			{
			make_question($memorybank);
			//echo($FULLME);
			redirect(strip_querystring($FULLME).'?what=add&instid=' . $instid);
			}
		}
		if($what==='edit')
		{
			global $FULLME;

			$qid  = optional_param('qid',null);
			
			if(!empty($qid))
			{

			update_questionbank($qid);
			//redirect('http://moodlehacks.com/mod/memorybank/view.php?id=20',5);
			//redirect(strip_querystring($FULLME).'?instid=1&qid='.$qid);
			}
		}
		
	}
	else //what is empty
	
	if(isset($level)) //If level is given, the user has given an item a grade, so process it
	{
		
				$question = get_record('memorybank_bank','id',$qid);
				if(empty($question))
				{
					var_dump(debug_backtrace());
					die('got level of invalid question');
					}

				if(!$schedule = get_record('memorybank_schedule','userid',$USER->id,'questionid',$qid))
				{
					die('this item is not currently scheduled.  It should not be called');
				}
				else
				{
					$currenthalflife = $schedule->halflife;
					$previoustimeanswered = $schedule->lastanswered;
					$schedule->lastanswered = time();
					$schedule->lastgrade = $level;
					// compute this based on answer $schedule->nextviewing
					//$schedule->nextviewing = time() + (-log(.9)/log(2))*$memorybank_DEFAULT_HALFLIFE;
					$schedule->viewcount++;
					$schedule->nextviewing = nextViewingDay()+rand(0,60);
					if($level > 2) {
					$schedule->nextviewing += ($level - 3) * ($schedule->viewcount-rand(0,1))* 24 * 60*60;
					//echo('next viewing in '. ($schedule->nextviewing - time())/(24*60*60));
					}
					
					update_record("memorybank_schedule", $schedule);
				}
				
		        $submission = new object();
				$submission->qid = $qid;
                $submission->userid = $USER->id;
                $submission->timequestionviewed = $lasttime;
				$submission->timeanswered = $schedule->lastanswered;
				$submission->millisecondresponse = ($submission->timeanswered-$submission->timequestionviewed) * 1000;
				$submission->grade = $level;
				$submission->viewcount = $schedule->viewcount;
				$submission->currenthalflife = $currenthalflife;
				
				//print_r('prev time '.$previoustimeanswered.' currenthalflife '.$currenthalflife. 'time '.time());
				if($submission->viewcount > 1)
				{
				$submission->recallprob = pow(.5,(time()-$previoustimeanswered)/$currenthalflife); //Note:  should prev time measured be the start time for n = 1?
				}

				insert_record("memorybank_submissions", $submission);

	}
	
	
    $strmemorybanks = get_string("modulenameplural", "memorybank");
    $strmemorybank  = get_string("modulename", "memorybank");

    $navlinks = array();
    $navlinks[] = array('name' => $strmemorybanks, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($memorybank->name), 'link' => '', 'type' => 'activityinstance');

    $navigation = build_navigation($navlinks);

    print_header_simple(format_string($memorybank->name), "", $navigation, "", "", true,
                  update_module_button($cm->id, $course->id, $strmemorybank), navmenu($course, $cm));


if($what==='add' || $what==='edit')
{
	print_edit_page($memorybank->id,$what); //This shows an editable version
}
else{
	
	print_main_page($memorybank->id,$what);
}
    print_footer($course);
?>
