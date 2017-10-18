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
