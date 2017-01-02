<?php
	
	//MINDENHOVA
	echo "<script src='plugins/jquery-1.9.1.js?".time()."' type='text/javascript'></script>";
	
	//JS BETÖLTŐ HA NINCS SESSION ÉS PAGE SINCS (BEJELENTKEZÉS ELŐTT)
	if (!isset($_SESSION['ID']) && !isset($_GET['page'])) {
		
		echo "<script src='js/checkJS.js?".time()."' type='text/javascript'></script>";
		
	}
	
	//BEJELENTKEZÉS UTÁNI FŐOLDAL
	if (is_numeric($_SESSION['pub']->ID) && !isset($_GET['page'])) {
		echo "
			<script type='text/javascript' src='plugins/chart-2/Chart.js?".time()."'></script>
			<link href='plugins/sunny/jquery-ui-1.10.1.custom.css?".time()."' rel='stylesheet' />
			<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
			<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
		";
	}
	
	//elszám modosítás
	if ($_GET['page'] == 3) {
		
		echo "
			<script src='js/standModul.js?".time()."' type='text/javascript'></script>
			<script src='plugins/jquery.cookie.js?".time()."' type='text/javascript'></script>
		";
		
	//itallap modosítás
	} else if ($_GET['page'] == 6) {
		
		echo "
			<script src='js/itallapModul.js?".time()."' type='text/javascript'></script>
		";
	
	//Új standlap == JQUERY && DATEPICKER
	} else if ($_GET['page'] == 1) {
		
		echo "
			<link href='plugins/sunny/jquery-ui-1.10.1.custom.css?".time()."' rel='stylesheet' />
			<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
			<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
		";
	
	//stand kereső
	} else if ($_GET['page'] == 4 ) {
	
		echo "
			<link href='plugins/sunny/jquery-ui-1.10.1.custom.css' rel='stylesheet' />
			<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
			<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
			
			<script src='js/keresoModul.js?".time()."' type='text/javascript'></script>
		";
	
	//Felhasználó kezelő && kocsma kezelő && személyes beállíások || JQUERY && MD5
	} else if ($_GET['page'] == 9 || $_GET['page'] == 13 || $_GET['page'] == 19) {
		
		echo "
			<link href='plugins/sunny/jquery-ui-1.10.1.custom.css' rel='stylesheet' />
			<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
			<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
			
			<script src='js/md5.js?".time()."' type='text/javascript'></script>
			<script src='js/felhaszKocsModul.js?".time()."' type='text/javascript'></script>
		";
		
	//Statisztika
	} else if ($_GET['page'] == 16) {
		
		if (isset($_POST['type'])) {
			
			echo "
				<script type='text/javascript' src='plugins/chart/jquery.min.js?".time()."'></script>
				<script type='text/javascript' src='plugins/chart/jquery.jqplot.min.js?".time()."'></script>
				<script type='text/javascript' src='js/statisztika.js?".time()."'></script>
				<link rel='stylesheet' type='text/css' href='plugins/chart/jquery.jqplot.css' />
				<script type='text/javascript' src='plugins/chart/plugins/jqplot.barRenderer.min.js?".time()."'></script>
				<script type='text/javascript' src='plugins/chart/plugins/jqplot.categoryAxisRenderer.min.js?".time()."'></script>
				<script type='text/javascript' src='plugins/chart/plugins/jqplot.pointLabels.min.js?".time()."'></script>
				<script type='text/javascript' src='plugins/chart/plugins/jqplot.canvasTextRenderer.min.js?".time()."'></script>
				
				<script type='text/javascript' src='plugins/chart-2/Chart.js?".time()."'></script>
			";
			
		} else {

			echo "
				<link href='plugins/sunny/jquery-ui-1.10.1.custom.css' rel='stylesheet' />
				<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
				<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
				<script type='text/javascript' src='js/statisztika.js?".time()."'></script>
			";
		
		}
		
	//Fizetés számoló
	} else if($_GET['page'] == 20) {
		echo "
			<link href='plugins/sunny/jquery-ui-1.10.1.custom.css' rel='stylesheet' />
			<script src='plugins/jquery-ui-1.10.1.custom.js?".time()."' type='text/javascript'></script>
			<script src='plugins/hu/jquery.ui.datepicker-hu.js?".time()."' type='text/javascript'></script>
		";
		
	//akciós termék
	} else if ($_GET['page'] == 22) {
		
		echo "<script src='js/itallapModul.js?".time()."' type='text/javascript'></script>";
		
	} else if (!isset($_GET['page']) && $_SESSION['plogined'] == 1) {
		echo "<script src='js/dashboard.js?".time()."' type='text/javascript'></script>";
		
	//kocsma beállítások
	} else if ($_GET['page'] == 28) {
		
		echo "<script src='js/kocsmaBeallitasok.js?".time()."' type='text/javascript'></script>";
		
	//sorsjegy beállítások
	} else if ($_GET['page'] == 29) {
		
		echo "<script src='js/sorsjegy.js?".time()."' type='text/javascript'></script>";
		
	}
	
?>