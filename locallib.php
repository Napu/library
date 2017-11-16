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

/*function librarian_tabs($context, $cm, $emarking, $draft=null) {
	global $CFG, $USER;
	$usercangrade = has_capability("mod/emarking:grade", $context);
	$issupervisor = has_capability("mod/emarking:supervisegrading", $context);
	$tabs = array();
	// Print tab.
	$printtab = new tabobject('printscan', $CFG->wwwroot . "/mod/emarking/print/exam.php?id={$cm->id}", $emarking->type == EMARKING_TYPE_PRINT_ONLY ? get_string('print', 'mod_emarking') : get_string('type_print_scan', 'mod_emarking'));
	// Print summary tab.
	$printtab->subtree[] = new tabobject('myexams', $CFG->wwwroot . "/mod/emarking/print/exam.php?id={$cm->id}", get_string('exam', 'mod_emarking'));
	// Scan tab.
	$scantab = new tabobject("scan", $CFG->wwwroot . "/mod/emarking/view.php?id={$cm->id}&scan=1", get_string('exams', 'mod_emarking'));
	$scanlist = new tabobject("scanlist", $CFG->wwwroot . "/mod/emarking/view.php?id={$cm->id}&scan=1", get_string('view'));
	$uploadanswers = new tabobject("uploadanswers", $CFG->wwwroot . "/mod/emarking/print/uploadanswers.php?id={$cm->id}", get_string('uploadanswers', 'mod_emarking'));
	$orphanpages = new tabobject('orphanpages', $CFG->wwwroot . "/mod/emarking/print/orphanpages.php?id={$cm->id}", get_string('orphanpages', 'mod_emarking'));
	$scantab->subtree[] = $scanlist;
	if ($usercangrade && $issupervisor && $emarking->type != EMARKING_TYPE_PRINT_ONLY
	&& $emarking->uploadtype == EMARKING_UPLOAD_QR) {
		$printtab->subtree[] = $uploadanswers;
		$printtab->subtree[] = $orphanpages;
	}
	// Grade tab.
	$markingtab = new tabobject("grade", $CFG->wwwroot . "/mod/emarking/view.php?id={$cm->id}", get_string('onscreenmarking', 'mod_emarking'));
	$markingtab->subtree[] = new tabobject("mark", $CFG->wwwroot . "/mod/emarking/view.php?id={$cm->id}", get_string("marking", 'mod_emarking'));
	$regradestab = new tabobject("regrades", $CFG->wwwroot . "/mod/emarking/marking/regraderequests.php?id={$cm->id}", get_string("regrades", 'mod_emarking'));
	if (!$usercangrade) {
		if ($emarking->peervisibility) {
			$markingtab->subtree[] = new tabobject("ranking", $CFG->wwwroot . "/mod/emarking/reports/ranking.php?id={$cm->id}", get_string("ranking", 'mod_emarking'));
			$markingtab->subtree[] = new tabobject("viewpeers", $CFG->wwwroot . "/mod/emarking/reports/viewpeers.php?id={$cm->id}", get_string("reviewpeersfeedback", 'mod_emarking'));
		}
		if ($emarking->type == EMARKING_TYPE_ON_SCREEN_MARKING && $draft != null && $draft->status >= EMARKING_STATUS_PUBLISHED) {
			$markingtab->subtree[] = $regradestab;
		}
	} else {
		if (has_capability('mod/emarking:regrade', $context) && $emarking->type == EMARKING_TYPE_ON_SCREEN_MARKING) {
			$markingtab->subtree[] = $regradestab;
		}
	}
	// Settings tab.
	$settingstab = new tabobject("settings", $CFG->wwwroot . "/mod/emarking/marking/settings.php?id={$cm->id}", get_string('settings'));
	// Settings for marking.
	if ($emarking->type == EMARKING_TYPE_ON_SCREEN_MARKING) {
		$settingstab->subtree[] = new tabobject("osmsettings", $CFG->wwwroot . "/mod/emarking/marking/settings.php?id={$cm->id}", get_string("marking", 'mod_emarking'));
		$settingstab->subtree[] = new tabobject("comment", $CFG->wwwroot . "/mod/emarking/marking/predefinedcomments.php?id={$cm->id}&action=list", get_string("predefinedcomments", 'mod_emarking'));
		if (has_capability('mod/emarking:assignmarkers', $context)) {
			$settingstab->subtree[] = new tabobject("markers", $CFG->wwwroot . "/mod/emarking/marking/markers.php?id={$cm->id}", get_string("markerspercriteria", 'mod_emarking'));
			$settingstab->subtree[] = new tabobject("pages", $CFG->wwwroot . "/mod/emarking/marking/pages.php?id={$cm->id}", core_text::strtotitle(get_string("pagespercriteria", 'mod_emarking')));
			$settingstab->subtree[] = new tabobject("outcomes", $CFG->wwwroot . "/mod/emarking/marking/outcomes.php?id={$cm->id}", core_text::strtotitle(get_string("outcomes", "grades")));
			$settingstab->subtree[] = new tabobject("importrubric", $CFG->wwwroot . "/mod/emarking/marking/importrubric.php?id={$cm->id}&action=list", get_string("importrubric", 'mod_emarking'));
			$settingstab->subtree[] = new tabobject("export", $CFG->wwwroot . "/mod/emarking/marking/export.php?id={$cm->id}", core_text::strtotitle(get_string("export", "mod_data")));
		}
	}
	// Grade report tab.
	$gradereporttab = new tabobject("gradereport", $CFG->wwwroot . "/mod/emarking/reports/feedback.php?id={$cm->id}", get_string("reports", "mod_emarking"));
	$gradereporttab->subtree[] = new tabobject("feedback", $CFG->wwwroot . "/mod/emarking/reports/feedback.php?id={$cm->id}", get_string("feedback", "mod_emarking"));
	$gradereporttab->subtree[] = new tabobject("report", $CFG->wwwroot . "/mod/emarking/reports/grades.php?id={$cm->id}", get_string("grades", "grades"));
	$gradereporttab->subtree[] = new tabobject("markingreport", $CFG->wwwroot . "/mod/emarking/reports/marking.php?id={$cm->id}", get_string("marking", 'mod_emarking'));
	$gradereporttab->subtree[] = new tabobject("ranking", $CFG->wwwroot . "/mod/emarking/reports/ranking.php?id={$cm->id}", get_string("ranking", 'mod_emarking'));
	if ($emarking->justiceperception > EMARKING_JUSTICE_DISABLED) {
		$gradereporttab->subtree[] = new tabobject("justicereport", $CFG->wwwroot . "/mod/emarking/reports/justice.php?id={$cm->id}", get_string("justice", 'mod_emarking'));
	}
	$gradereporttab->subtree[] = new tabobject("outcomesreport", $CFG->wwwroot . "/mod/emarking/reports/outcomes.php?id={$cm->id}", get_string("outcomes", "grades"));
	// Tabs sequence.
	if ($usercangrade) {
		// Print tab goes always except for markers training.
		if ($emarking->type == EMARKING_TYPE_PRINT_ONLY || $emarking->type == EMARKING_TYPE_PRINT_SCAN || $emarking->type == EMARKING_TYPE_ON_SCREEN_MARKING || $emarking->type == EMARKING_TYPE_PEER_REVIEW) {
			if (has_capability('mod/emarking:addinstance', $context)) {
				if (count($printtab->subtree) == 1) {
					$tabs[] = $printtab->subtree[0];
				} else {
					$tabs[] = $printtab;
				}
			}
		}
		// Scan or enablescan tab.
		if ($emarking->type == EMARKING_TYPE_PRINT_SCAN) {
			$tabs[] = $scantab;
		}
		// OSM tabs, either marking, reports and settings or enable osm.
		if ($emarking->type == EMARKING_TYPE_ON_SCREEN_MARKING || $emarking->type == EMARKING_TYPE_PEER_REVIEW) {
			$tabs[] = $markingtab;
			$tabs[] = $gradereporttab;
			if ($issupervisor) {
				$tabs[] = $settingstab;
			}
		}
	} else
	if ($emarking->type == EMARKING_TYPE_PRINT_SCAN) {
		// This case is for students (user can not grade).
		$tabs = $scantab->subtree;
	} else
	if ($emarking->type == EMARKING_TYPE_PRINT_ONLY) {
		// This case is for students (user can not grade).
		$tabs = array();
	} else {
		// This case is for students (user can not grade).
		$tabs = $markingtab->subtree;
	}
	return $tabs;
}*/