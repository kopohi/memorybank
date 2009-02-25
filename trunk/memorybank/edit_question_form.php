<?php
/**
 * This page defines the elements displayed when creating and editing questions. 
 */

require_once ($CFG->libdir . '/formslib.php');

class edit_question_form extends moodleform{
	function definition(){

		$mform =  & $this->_form; //Is the & symbol for error supression needed?  Perhaps it is when $this->_form is not yet defined
		
		$mform->addElement('select', 'category', get_string('category'), category_menu());
		
		$options = array('rows' => 7);
		$mform->addElement('htmleditor', 'question', get_string('question'), $options);
		$mform->setType('question', PARAM_RAW);  //Perhaps not filtering this is when multi-media is showing extra stuff.  Check into using something other than PARAM_RAW
		
		$mform->addElement('htmleditor', 'answer', get_string('answer'), $options);
		$mform->setType('answer', PARAM_RAW);
		
		$mform->addElement('text', 'reference', 'Reference', array('size' => '70', ));
		
		unset($options);
		$options[0] = get_string("zerodef", "memorybank");
		$options[1] = get_string("onedef", "memorybank");
		$options[2] = get_string("twodef", "memorybank");
		$options[3] = get_string("threedef", "memorybank");
		$options[4] = get_string("fourdef", "memorybank");
		$options[5] = get_string("fivedef", "memorybank");
		$mform->addElement('select', 'initialgrade', get_string("initialgrade", "memorybank"), $options);
		$mform->setDefault('initialgrade', 4);
		
		$mform->addElement('checkbox', 'visible', get_string('visible'));
		$mform->setDefault('visible', true);
		
		$currentyear = date('Y');
		$options = array
		(
			'startyear' => $currentyear, //We should make this the current year and the next year
			'stopyear' => $currentyear + 1,
			'timezone' => 99,
			'applydst' => true,
			'optional' => false
		);
		$mform->addElement('date_time_selector', 'initviewtime', get_string("nextviewing", "memorybank"), $options);
		$mform->setDefault('initviewtime',nextViewingDay()-24*60*60);
		//$mform->disabledIf('visible', 'initviewtime[off]', 'checked');  //Check if this is working
		
		$instid = required_param('instid');
		$mform->addElement('hidden', 'instid', $instid);
		$mform->addElement('hidden', 'qid', null);
		$mform->addElement('hidden', 'what', 'add');
		$this->add_action_buttons();
		
		$what = optional_param('what',null);
		if($what === 'edit'){
			
			$qid = required_param('qid');

			$question = get_record('memorybank_bank', 'id', $qid);
			$mform->setDefault('initialgrade', $question->initialgrade);
			$mform->setDefault('question', $question->question);
			$mform->setDefault('answer', $question->answer);
			$mform->setDefault('reference', $question->reference);
			$mform->setDefault('initviewtime', $question->initviewtime);
			$mform->setDefault('visible', $question->visible);
			$mform->setDefault('what', 'edit');
			$mform->setDefault('qid', $qid);
			$mform->setDefault('category', $question->modid);
		}
	}
}
?>
