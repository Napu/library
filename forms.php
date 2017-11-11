<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/formslib.php');


class formBuscarLibro extends moodleform {
	
	function definition() {

		global $CFG, $DB;
		$mform =& $this->_form;
		$tagarray = array();
		$tags = $DB->get_records('local_library_tag');
		$tagarray[0]=" ";
		foreach ($tags as $tag){
			$tagarray[$tag->id] = $tag->name;
		}

		$select=$mform->addElement('select', 'Tag', "Elige un tema:",$tagarray);
		$mform->addElement('text', 'Autor', "Autor:");
		$mform->setType('Autor', PARAM_TEXT);
		$mform->addElement('text', 'Nombre', "Nombre:");
		$mform->setType('Nombre', PARAM_TEXT);
		$mform->addElement('text', 'Editorial', "Editorial:");
		$mform->setType('Editorial', PARAM_TEXT);
		$this->add_action_buttons(false, "Buscar");
	}
	
}