<?php

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio

//table showing a table of 4x4 where books are shown
function library_bookShelf(){
	global $DB, $USER, $CFG, $OUTPUT;
	
	$table = new html_table();
	
	$books = $DB->get_records('local_library_book');
	
	$columns= count($books);
	
	if($columns<4){
		$rows=1;
	}else{
		$rows =round($columns/4);
	} 
	//going throw rows
	for($row = 0;$row<$rows;$row ++){
		for($column=0;$column<$columns;$column++){
			$stock = $books[$column+1]->stock;
			if($stock == "0"){
				$bookname[$column]=$books[$column+1]->name."<br> Stock: ".$books[$column+1]->stock."   No quedan copias por reservar";
			}else{
				$url = new moodle_url("reserve.php",array('id'=>$books[$column+1]->id));
				$button = $OUTPUT->single_button($url,"Reservar");
				$bookname[$column]=$books[$column+1]->name."<br> Stock: ".$books[$column+1]->stock."   ".$button;
			}
		}
	}
	$table->data[]=$bookname;
	//var_dump($table->data);die();
	return $table;
}

function library_validation($reservation, $bookid){
	global $DB, $CFG, $USER, $OUTPUT;

	if($reservation=1 && $bookid!= 0){
		$validation = $DB->get_record("local_library", array("bookid"=>$bookid, "userid"=>$USER->id));
		if($validation != false){
			$now = strtotime(date("d-m-Y"));
			$reservation_expires = $validation -> date + 259200;
			if($reservation_expires > $now){
				$condition= "Ya tienes reservado este libro";
				$url = new moodle_url("library.php");
				$button = $OUTPUT->single_button($url,"Volver a elegir libros");
				$condition .= "<br><br>".$button;
				$condition.= $OUTPUT->footer ();
				
				return $condition;
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
}

function array_not_unique($raw_array) {
	$dupes = array();
	natcasesort($raw_array);
	reset($raw_array);

	$old_key   = NULL;
	$old_value = NULL;
	foreach ($raw_array as $key => $value) {
		if ($value === NULL) { continue; }
		if (strcasecmp($old_value, $value) === 0) {
			$dupes[$old_key] = $old_value;
			$dupes[$key]     = $value;
		}
		$old_value = $value;
		$old_key   = $key;
	}
	return $dupes;
}

function library_get_books_fromform($fromform){
	global $DB, $CFG, $USER, $OUTPUT;
	
	$id=array();
	
	if($fromform->Autor==""){
		$sqlWhereAuthor = NULL;
	}else{
		$author_sql = "SELECT id FROM {local_library_book} WHERE author LIKE '%".$fromform->Autor."%'";
		$authorids = $DB->get_records_sql($author_sql);
		foreach($authorids as $authorid){
			$id[]=$authorid->id;
		}
	}
	
	if ($fromform->Tag ==""){
		$sqlWhereTag = NULL;
	}else{
		$tag_sql = "SELECT id FROM {local_library_book} WHERE tagone = ".$fromform->Tag;
		$tagsids = $DB->get_records_sql($tag_sql);
		foreach($tagsids as $tagsid){
			$id[]=$tagsid->id;
		}
	}
	
	if ($fromform->Nombre ==""){
		$sqlWhereName = NULL;
	}else{
		$name_sql = "SELECT id FROM {local_library_book} WHERE name LIKE '%".$fromform->Nombre."%'";
		$nameids = $DB->get_records_sql($name_sql);
		foreach($nameids as $nameid){
			$id[]=$nameid->id;
		}
	}
	
	if ($fromform->Editorial ==""){
		$sqlWhereEditorial = NULL;
	}else{
		$editorial_sql = "SELECT id FROM {local_library_book} WHERE editorial LIKE '%".$fromform->Editorial."%'";
		$editorialids = $DB->get_records_sql($editorial_sql);
		foreach($editorialids as $editorialid){
			$id[]=$editorialid->id;
		}
	}
	$booksid = array_unique(array_not_unique($id));
	return $booksid;
}