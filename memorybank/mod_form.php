<?php
//$Id

/**
 * 
 * http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * The form must provide support for, at least these fields:
 *   - name: text element of 64 characters max
 *
 * Also, it's usual to use these fields:
 *   - intro: one htmlarea element to describe the activity
 *            (will be showed in the list of activities of
 *             memorybank type (index.php) and in the header 
 *             of the memorybank main page (view.php).
 *   - introformat: The format used to write the contents
 *             of the intro field. It automatically defaults 
 *             to HTML when the htmleditor is used and can be
 *             manually selected if the htmleditor is not used
 *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
 *             See lib/weblib.php Constants and the format_text()
 *             function for more info
 */

require_once ('moodleform_mod.php');

class mod_memorybank_mod_form extends moodleform_mod
{
	
	function definition()
	{
		
		global $COURSE;
		$mform =  & $this->_form;
		
		//-------------------------------------------------------------------------------
		/// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		/// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('memorybankname', 'memorybank'), array('size' => '64', ));
		$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');
		/// Adding the optional "intro" and "introformat" pair of fields
		$mform->addElement('htmleditor', 'intro', get_string('memorybankintro', 'memorybank'));
		$mform->setType('intro', PARAM_RAW);
		
		$mform->addElement('format', 'introformat', get_string('format'));
		
		//-------------------------------------------------------------------------------
		/// Adding the rest of memorybank settings, spreeading all them into this fieldset

		$mform->addElement('text', 'dailylimit', get_string('dailylimit', 'memorybank'), array('size' => '4', ));
		$mform->setDefault('dailylimit', 10);
		
		
		$options = array();
		
		$options[0] = 'No';
		$options[1] = 'Yes, Initially private';
		$options[2] = 'Yes, Initially public';
        $mform->addElement('select', 'studententries', 'Allow student entries', $options);
        $mform->setDefault('studententries', 1);

		//-------------------------------------------------------------------------------
		// add standard elements, common to all modules
		$this->standard_coursemodule_elements();
		//-------------------------------------------------------------------------------
		// add standard buttons, common to all modules
		$this->add_action_buttons();
		
	}
}
?>
