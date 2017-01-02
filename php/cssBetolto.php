<?php
			
	//HA VAN BROWSER
	if (isset($_GET['bro'])) {
		$_SESSION['browser'] = $_GET['bro'];
	}
	
	//GET BROWSER
	if ($_SESSION['browser'] == "msie") {
		echo "<link rel='stylesheet' type='text/css' href='css/font_msie.css?".time()."' />";
		echo "<link rel='stylesheet' type='text/css' href='css/standards_msie.css?".time()."' />";
	} else {
		echo "<link rel='stylesheet' type='text/css' href='css/font_other.css?".time()."' />";
	}
	
	//mindig
	echo "<link rel='stylesheet' type='text/css' href='css/standards.css?".time()."' />";
	
	//főoldal
	if (!isset($_GET['page'])) {
		echo "<link rel='stylesheet' type='text/css' href='css/standlap.css?".time()."' />";
	}
	
	//elszám modosítás
	if ($_GET['page'] == 3) {
		
		echo "<link rel='stylesheet' type='text/css' href='css/standlap.css?".time()."' /><link rel='stylesheet' type='text/css' href='css/others.css?".time()."' />";
		
	//itallap modosítás
	} else if ($_GET['page'] == 6) {
		
		echo "
			<link rel='stylesheet' type='text/css' href='css/standlap.css?".time()."' />
			<link rel='stylesheet' type='text/css' href='css/itallap.css?".time()."' />
			<link rel='stylesheet' type='text/css' href='css/others.css?".time()."' />
		";
	
	//Elszámolás keresés
	} else if($_GET['page'] == 4) {
		
		echo "<link rel='stylesheet' type='text/css' href='css/standSearch.css?".time()."' /><link rel='stylesheet' type='text/css' href='css/others.css?".time()."' />";
		
	//Statiszzika megjelenítés
	} else if ($_GET['page'] == 16) {
		
		echo "<link rel='stylesheet' type='text/css' href='css/statisztika.css?".time()."' />";
	
	//Stand feltöltésekor stand css betöltése a dashboard miatt
	} else if ($_GET['page'] == 5) {
	
		echo "<link rel='stylesheet' type='text/css' href='css/standlap.css?".time()."' />";
		
	//felhasznalo kezelő stylesheetje
	} else if ($_GET['page'] == 9 || $_GET['page'] == 19) {
		
		echo "<link rel='stylesheet' type='text/css' href='css/kezelok.css?".time()."' />";
		
	//fix kiadás beállítás és kocsma beállítás
	} else if ($_GET['page'] == 27 || $_GET['page'] == 28) {
		echo "
			<link rel='stylesheet' type='text/css' href='css/standlap.css?".time()."' />
			<link rel='stylesheet' type='text/css' href='css/itallap.css?".time()."' />
			<link rel='stylesheet' type='text/css' href='css/kocsmaBeallitasok.css?".time()."' />
		";
	}
	
?>