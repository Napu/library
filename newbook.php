<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the version and other meta-info about the plugin
*
* Setting the $plugin->version to 0 prevents the plugin from being installed.
* See https://docs.moodle.org/dev/version.php for more info.
*
* @package    newmodule
* @copyright  2016 Your Name <your@email.address>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once (dirname ( __FILE__ ) . '/../../config.php');
require_once ($CFG->dirroot.'/local/library/locallib.php');
require_once ($CFG->dirroot.'/local/library/forms.php');

global $DB, $USER, $CFG;

require_login();

$baseurl = new moodle_url ( '/local/library/librarian.php' );
$context = context_system::instance ();
$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_title ( "Library" );
$PAGE->set_heading ( "Virtual Library" );
$PAGE->navbar->add ( "Librarian", 'librarian.php' );
echo $OUTPUT->header ();
if(has_capability("local/library:Librarian",context_user::instance($USER->id))){
echo $OUTPUT->heading ( "Crea un Libro" );

$form_create = new formLibrarianNewBook ( null );
echo $form_create->display ();
if($fromform = $form_create->get_data ()){
	
	$insert = library_get_new_book($fromform);
	if($insert = true){
		echo"<h3><br><br><br>El libro ha sido creado con exito</h3>";
		
		$url = new moodle_url("librarian.php");
		echo "<br>".$OUTPUT->single_button($url,"Volver");
		
		
		echo $OUTPUT->footer();
		die();
	}else{
		echo "<h3><br><br><br>Hubo algun error en la creacion del nuevo libro, porfavor intetelo nuevamente</h3>";
		$url = new moodle_url("librarian.php");
		echo "<br>".$OUTPUT->single_button($url,"Volver");
		
		echo $OUTPUT->footer();
		die();
	}
	
}else{
	
	echo $OUTPUT->footer();
	die();
}
}else{
	echo $OUTPUT->footer();
	die();
}