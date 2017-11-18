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

echo $OUTPUT->heading ( "Elige una Opcion" );

$table = new html_table();

$buttons = [];

$urlNewBook = new moodle_url("newbook.php");
$buttonNewBook = $OUTPUT->single_button($urlNewBook,"Crear nuevo Libro");
$buttons[1] = $buttonNewBook;

$urlGiveBackBook = new moodle_url("giveback.php");
$buttonGiveBackBook = $OUTPUT->single_button($urlGiveBackBook,"Devolver Libro");
$buttons[2] = $buttonGiveBackBook;
$table->data[]=$buttons;
$print_table = html_writer:: table($table);

echo $print_table;


echo $OUTPUT-> footer();