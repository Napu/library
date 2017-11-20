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

function library_reservation_validation($reservation, $bookid){
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
	if(array_not_unique($id)){
		$booksid = array_unique(array_not_unique($id));
		return $booksid;	
	}else{
		return $id;
	}
	
}

function library_filtered_bookshelf($booksids){
	global $DB, $USER, $CFG, $OUTPUT;
	
	$table = new html_table();
	$rows = round(count($booksids)/4,0)+1;
	$maxcol = 4;
	if($rows<$maxcol) {
		$cols = $rows;
	}else{
		$cols= $maxcol;
	}
	$bookname = array();
	for($row=0;$row<$rows;$row++){
		for ($col=0; $col<$cols;$col++){
			$book = $DB->get_record("local_library_book",array("id"=>$booksids[$col]));
			//var_dump($book);die();
			$stock = $book->stock;
			if($stock == "0"){
				$bookname[$col]=$book->name."<br> Stock: ".$book->stock."   No quedan copias por reservar";
			}else{
				$url = new moodle_url("reserve.php",array('id'=>$book->id));
				$button = $OUTPUT->single_button($url,"Reservar");
				$bookname[$col]=$book->name."<br> Stock: ".$book->stock."   ".$button;
			}
		}
		
	}
	$table->data[]= $bookname;
	return $table;
	
}

function library_get_new_book($fromform){

	global $DB, $CFG, $USER, $OUTPUT;
	
	$book=new stdClass();
	$book ->author = $fromform->Autor;
	$book ->name = $fromform->Nombre;
	$book ->year = $fromform->publishdate;
	$book ->editorial = $fromform->Editorial;
	$book ->copies = $fromform->copias;
	$book ->tagone = $fromform->Tag;
	$book ->stock = $fromform->copias;
	
	//$validation = $DB-> get_record("local_library_book", array('author'=>$fromform->Autor,'name'=>$fromform->Nombre,'year'=>$fromform->publishdate, 'editorial'=>$fromform->Editorial,'copies'=>$fromform->copias, 'tagone'=>$fromform->Tag));
	//if($validation){
		//return false;
//	}else{	
		
		if($DB->insert_record("local_library_book", $book)){
			return true;
		}else{
			return false;
		}
	//}
	
}

function library_giveback_bookshelf(){
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
			$url = new moodle_url("givebackconfirmation.php",array('id'=>$books[$column+1]->id));
			$button = $OUTPUT->single_button($url,"Devolver");
			$bookname[$column]=$books[$column+1]->name."<br> Stock: ".$books[$column+1]->stock."   ".$button;
		}
	}
	$table->data[]=$bookname;
	return $table;

}

function library_giveback_validation($bookid){
	global $DB, $CFG, $USER, $OUTPUT;

	if($bookid!= 0){
		$validation = $DB->get_record("local_library_book", array("id"=>$bookid));
		
		if($validation->copies > $validation->stock){
			$update = new stdClass();
			$update->id = $bookid;
			$update->stock = $validation->stock +1;
			$DB->update_record("local_library_book", $update);
			echo "Devolucion exitosa";
			$url = new moodle_url("librarian.php");
			$button = $OUTPUT->single_button($url,"Volver al menu de opciones");
			echo "<br><br>".$button;
			echo $OUTPUT->footer ();
			die();
			
			
		}elseif ($validation->copies < $validation->stock || $validation->copies = $validation->stock){
			echo "No pueden haber mas copia en stock que las existentes en la UAI. Vuelva a seleccionar un libro para Devolver.";
			$url = new moodle_url("librarian.php");
			$button = $OUTPUT->single_button($url,"Volver al menu de opciones");
			echo "<br><br>".$button;
			echo $OUTPUT->footer ();
			die();
		}
	}
}