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

global $DB, $USER, $CFG;

$bookid = required_param('id', PARAM_INT);

require_login();


$baseurl = new moodle_url ( '/local/library/reserve.php' );
$context = context_system::instance ();
$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_title ( "Library" );
$PAGE->set_heading ( "Virtual Library" );
$PAGE->navbar->add ( "Library", 'library.php' );
echo $OUTPUT->header ();
echo $OUTPUT->heading ( "Devolucion de libros" );

if(has_capability("local/library:Librarian",context_user::instance($USER->id))){

$book = $DB->get_record('local_library_book', array('id'=>$bookid));

echo "Quieres Devolver: <br><br><h4>".$book->name."</h4><br>
		¿Estas Seguro/a que es lo que deseas?.<br><br>";

$url_accept = new moodle_url("giveback.php",array('bookid'=>$book->id));

$url_cancel = new moodle_url("giveback.php",array('bookid'=>$book->id));

$print = "<br>";
$print .= $OUTPUT->single_button($url_cancel,"Cancelar")."			".$OUTPUT->single_button($url_accept,"Devolver");

echo $print;

echo $OUTPUT->footer ();
}else{
	echo $OUTPUT->footer ();
	die();
	
}