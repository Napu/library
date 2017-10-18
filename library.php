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

//Validate there is not previous reservation
if($reservation=1 && $bookid!= 0){
	$validation = $DB->get_record("local_library", array("bookid"=>$bookid, "userid"=>$USER->id));
	if($validation != false){
		$now = strtotime(date("d-m-Y"));
		$reservation_expires = $validation -> date + 259200;
		if($reservation_expires > $now){
			echo "Ya tienes reservado este libro";
			$url = new moodle_url("library.php");
			$button = $OUTPUT->single_button($url,"Volver a elegir libros");
			echo "<br><br>".$button;
			echo $OUTPUT->footer ();
			die();
		}
		echo "Reserva Valida, por favor, dirigete a biblioteca a retirar tu libro";
		$url = new moodle_url("library.php");
		$button = $OUTPUT->single_button($url,"Volver a elegir libros");
		echo "<br><br>".$button;
		echo $OUTPUT->footer ();
		die();
	}
	
	$date = strtotime(date("d-m-Y"));
	$insert=new stdClass();
	$insert -> userid = $USER->id;
	$insert -> bookid = $bookid;
	$insert -> date = $date;
	$DB->insert_record("local_library", $insert, FALSE);
	$book = $DB->get_record("local_library_book", array("id"=>$bookid));
	$newstock = $book->stock -1;
	$update = new stdClass();
	$update->id = $bookid;
	$update->stock = $newstock;
	$DB->update_record("local_library_book", $update);
	echo "Reserva Valida, por favor, dirigete a biblioteca a retirar tu libro";
		$url = new moodle_url("library.php");
		$button = $OUTPUT->single_button($url,"Volver a elegir libros");
		echo "<br><br>".$button;
		echo $OUTPUT->footer ();
		die();
}

echo $OUTPUT->heading ( "Choose your book" );


$table = library_bookshelf();

$print_table = html_writer:: table($table);

echo $print_table;

echo $OUTPUT->footer ();
die("fin de la hoja");