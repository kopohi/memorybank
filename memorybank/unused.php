<?php
	        $sql = "SELECT q.*, a.*".
               "  FROM {$CFG->prefix}question q, {$CFG->prefix}question_answers a".
			   " WHERE q.category = {$mycategory} AND q.id = a.question AND fraction = 1 ORDER BY a.id LIMIT 1";
        if (!$thequestions = get_records_sql($sql)) {
            echo('Questions missing');
        }
		
		
		
			if(!empty($lasttime))
	{
		        $entry = new object();
                $entry->memorybankid = $memorybank->id;
                $entry->userid = $USER->id;
                $entry->timeclicked = $lasttime;
				$entry->visible = true;
                insert_record("memorybank_submissions", $entry);
				add_to_log($course->id, "memorybank", "add", "view.php?id=$cm->id",'U: '.$lasttime);
	}
	else
{
	add_to_log($course->id, "memorybank", "view", "view.php?id=$cm->id", "$memorybank->id");    
}
?>

	switch($action)
	{
		
		case 'a':
			//echo ('a');
			break;
		case 'b':
			//echo ('b');
			break;
	}
	
		global $CFG;
	        $sql = "SELECT q.*".
               "  FROM {$CFG->prefix}question q".
			   " WHERE q.category = {$mycategory} AND q.qtype = 'shortanswer' AND q.hidden = 0 ORDER BY q.id";
        if (!$questions = get_records_sql($sql)) {
            echo('Questions missing');
        }


	        $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
			
	foreach ($questions as $question) {

		$thequestion = format_text($question->questiontext,$question->questiontextformat,$formatoptions);
		$thereference = format_text($question->generalfeedback,FORMAT_MOODLE,$formatoptions);
		
		if(!empty($question->image)){
		//$image = get_question_image($question);
		}
		$answer=get_record('question_answers','question',$question->id,'fraction',1);
		$theanswer = format_text($answer->answer,FORMAT_MOODLE,$formatoptions);
	}
	$catlock = optional_param('catlock');
	$catmenu = choose_from_menu(array('a', 'b'), 'the cats','','All categories','','0',true,!empty($catlock));
	
	
	
	function print_main_page($instid,$action='a', $mycategory = 4)
{
	global $CFG;
	        $sql = "SELECT q.*".
               "  FROM {$CFG->prefix}question q".
			   " WHERE q.category = {$mycategory} AND q.qtype = 'shortanswer' AND q.hidden = 0 ORDER BY q.id";
        if (!$questions = get_records_sql($sql)) {
            echo('Questions missing');
        }


	        $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
			
	foreach ($questions as $question) {

		$thequestion = format_text($question->questiontext,$question->questiontextformat,$formatoptions);
		$thereference = format_text($question->generalfeedback,FORMAT_MOODLE,$formatoptions);
		
		if(!empty($question->image)){
		//$image = get_question_image($question);
		}
		$answer=get_record('question_answers','question',$question->id,'fraction',1);
		$theanswer = format_text($answer->answer,FORMAT_MOODLE,$formatoptions);
	}
	$catlock = optional_param('catlock');
	$catmenu = choose_from_menu(array('a', 'b'), 'the cats','','All categories','','0',true,!empty($catlock));
	include('mainform.html');
	}
	?>
	
	<?php if (!empty($image)) { ?>

  <img class="qimage" src="<?php echo $image; ?>" alt="" />
<?php } ?>

		
		$strfeedback = 'string feedback';
		
		$feedback = "'', FGBACKGROUND, '".$CFG->wwwroot."/file.php/1/3monthcal.png',WIDTH,120,CAPTION,'3 month calendar                                                                ---',HEIGHT,137";
		$feedback = "'This changes the width to 540 pixels',CAPTION, 'Hi. There!!', WIDTH, 540";
		
		//echo $feedback; 
                       $overlib = "return overlib($feedback);";
                        $studentshtml = '<span onmouseover="'.s($overlib).'" onmouseout="return nd();">';
						
						echo $studentshtml."HELLO".'</span>';
		
		//echo('<pre>');print_r($mform->_attributes);echo('</pre>');