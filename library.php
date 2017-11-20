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

$bookid = optional_param("bookid",0, PARAM_INT);
$reservation = optional_param("reserva", false, PARAM_BOOL);

require_login();

$baseurl = new moodle_url ( '/local/library/library.php' );
$context = context_system::instance ();
$PAGE->set_context ( $context );
$PAGE->set_url ( $baseurl );
$PAGE->set_pagelayout ( 'standard' );
$PAGE->set_title ( "Library" );
$PAGE->set_heading ( "Virtual Library" );
$PAGE->navbar->add ( "Library", 'library.php' );
echo $OUTPUT->header ();

$form_buscar = new formBuscarLibro ( null );
echo $form_buscar->display ();
if($fromform = $form_buscar->get_data ()){
	echo $OUTPUT->heading ( "Resultados de la Busqueda" );
	//Get books ids matching the form inputs 
	$booksid = library_get_books_fromform($fromform);
	//Var_dump($booksid);die();
	$shelf = library_filtered_bookshelf($booksid);
	$print_table = html_writer:: table($shelf);
	echo $print_table;
	
	echo $OUTPUT->footer ();
	die();
}else{
	//Validate there is not previous reservation
	echo library_reservation_validation($reservation, $bookid);

	echo $OUTPUT->heading ( "Choose your book" );


	$table = library_bookshelf();

	$print_table = html_writer:: table($table);

	echo $print_table;
	echo $OUTPUT->footer ();
	die();
}
