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

class formLibrarianNewBook extends moodleform {
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
		$mform->addElement('text', 'publishdate', "Fecha de publicacion:");
		$mform->setType('publishdate', PARAM_TEXT);
		$mform->addElement('text', 'Editorial', "Editorial:");
		$mform->setType('Editorial', PARAM_TEXT);
		$mform->addElement('text', 'copias', "Numero de copias:");
		$mform->setType('copias', PARAM_TEXT);
		
		$mform -> addRule('Tag', 'Campo Obligatorio', 'required', false, false);
		$mform -> addRule('Autor', 'Campo Obligatorio', 'required', false, false);
		$mform -> addRule('Nombre', 'Campo Obligatorio', 'required', false, false);
		$mform -> addRule('publishdate', 'Campo Obligatorio', 'required', false, false);
		$mform -> addRule('Editorial', 'Campo Obligatorio', 'required', false, false);
		$mform -> addRule('copias', 'Campo Obligatorio', 'required', false, false);
		$this->add_action_buttons(false, "Ingresar");
	}
	
}