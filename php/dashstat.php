<?php

//Dashstats
function dashstat($kapcsolat) {
	include "conf/mysql.php";
	
	//kocsma beállításainak betöltése
	$pubOptions = array();
	$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID.";");
	while ($result = mysql_fetch_assoc($query)) {
		$pubOptions[$result['option']] = $result['value'];
	}
	
	/* JOBB OLDALI MODUL : ALKALMAZOTTAK ÖSSZEHASONLÍTÁSA */
	//IDŐSZAK
	$j = 0;
	$start = date('Y-m-01');
	$end = date('Y-m-t');
	$thismonth = date('Y-m');
	
	//ÚJ FORGALOM SZÁMOLÁS
	if (isset($pubOptions['ujforgSzamol']) && $pubOptions['ujforgSzamol'] == 1) {
		
		$query = mysql_query("
			
			SELECT FLOOR(SUM(`forgalom`) / SUM(`wtime`)) as `forgalom`, `user`.`uname` as 'name'
			FROM `standok` 
			LEFT JOIN `user`
			ON `user`.`ID` = `standok`.`UID`
			WHERE `standok`.`date` 
			BETWEEN '$start' AND '$end'
			AND `standok`.`PID` = ".$_SESSION['pub']->ID."
			GROUP BY `standok`.`UID`
		
		");
		
	//RÉGI FORGALOM SZÁMOLÁS
	} else {
		
		$query = mysql_query("
			SELECT `standok`.`UID`,

			(SELECT `user`.`uname` FROM `user` WHERE `user`.`ID` = `standok`.`UID`) as 'name',

			FLOOR(SUM((SELECT SUM(`stand`.`fogyas` * `stand`.`price`) FROM `stand` WHERE `stand`.`SID` = `standok`.`ID` )) / sum(`standok`.`wtime`)) as 'forgalom' 

			FROM `standok`
			WHERE `standok`.`PID` = ".$_SESSION['pub']->ID." AND 
			`standok`.`date` BETWEEN '$start' AND '$end'
			GROUP BY `standok`.`UID`;
		");
		
	}
	
	//KAPOT STATISZTIKAI ADATOK TÖMBBE MENTÉSE
	$users = array();
	$usersdata = "";
	$i = 0;
	while ($result = mysql_fetch_assoc($query)) {
		
		$usersdata .= "<input type='hidden' id='user$i' name='user$i' value='".$result['name'].";".$result['forgalom']."'>";
		
		$i++;
	}
	
	
	/* BAL OLDALI MODUL : ELMÚLT 1 ÉV */
	$idei = date("Y");
	$tavalyi = date("Y")-1;
	//--------------------------- >
	$ideistart = date("Y-01-01");
	$tavalyistart = $tavalyi."-01-01";
	//--------------------------- >
	$ideiend = date("Y-12-31");
	$tavalyiend = $tavalyi."-12-31";
	//--------------------------- >
	
	//szükséges változók
	$ideirecords = array();
	$tavalyirecords = array();
	
	//ÚJ SZÁMOLÓS RENDSZER
	if (isset($pubOptions['ujforgSzamol']) && $pubOptions['ujforgSzamol'] == 1) {
		
		$query = mysql_query("
			SELECT SUM(`forgalom`) as `_final`, `date` 
			FROM `standok` 
			WHERE `standok`.`date` BETWEEN '$ideistart' AND '$ideiend'
			GROUP BY YEAR(`date`), MONTH(`date`)
			ORDER BY `standok`.`date` ASC
		");
		
		//adatok tömbbe
		while ($result = mysql_fetch_assoc($query)) {
			$ideirecords[] = $result;
			
		}
		
		$query = mysql_query("
			SELECT SUM(`forgalom`) as `_final`, `date` 
			FROM `standok` 
			WHERE `standok`.`date` BETWEEN '$tavalyistart' AND '$tavalyiend'
			GROUP BY YEAR(`date`), MONTH(`date`)
			ORDER BY `standok`.`date` ASC
		");
		
		//adatok tömbbe
		while ($result = mysql_fetch_assoc($query)) {
			$tavalyirecords[] = $result;
			
		}
		
	//RÉGI SZÁMOLÓ RENDSZER
	} else {
		
		$query = mysql_query("
			SELECT ROUND(sum(`stand`.`fogyas`*`stand`.`price`)) as `_final`, `standok`.*
			FROM  `stand`

			LEFT JOIN `standok`
			ON `stand`.`SID` = `standok`.`ID`

			WHERE  `PID` =".$_SESSION['pub']->ID."
			AND `standok`.`date` BETWEEN '".$ideistart." 00:00:00' AND '".$ideiend." 00:00:00'

			GROUP BY YEAR(`standok`.`date`), MONTH(`standok`.`date`)
			ORDER BY `standok`.`date` ASC
		");
		
		//adatok tömbbe
		while ($result = mysql_fetch_assoc($query)) {
			$ideirecords[] = $result;	
		}
		
		$query = mysql_query("
			SELECT ROUND(sum(`stand`.`fogyas`*`stand`.`price`)) as `_final`, `standok`.*
			FROM  `stand`

			LEFT JOIN `standok`
			ON `stand`.`SID` = `standok`.`ID`

			WHERE  `PID` =".$_SESSION['pub']->ID."
			AND `standok`.`date` BETWEEN '".$tavalyistart." 00:00:00' AND '".$tavalyiend." 00:00:00'

			GROUP BY YEAR(`standok`.`date`), MONTH(`standok`.`date`)
			ORDER BY `standok`.`date` ASC
		");
		//--------------------------- >
		
		//adatok tömbbe
		while ($result = mysql_fetch_assoc($query)) {
			$tavalyirecords[] = $result;
			
		}
	}
	
	//tömbben a date átírása
	$datestr = "";
	$finalstr = "";
	for ($i = 0;$i < count($ideirecords); $i++) {
		$finalstr .= $ideirecords[$i]["_final"].";";
	}
	for ($i = 1; $i < 13; $i++) {
		if ($i < 10) {
			$datestr .= "0".$i.";";
		} else {
			$datestr .= $i.";";
		}
	}
	
	//tömbben a date átírása
	$datestr2 = "";
	$finalstr2 = "";
	for ($i = 0;$i < count($tavalyirecords); $i++) {
		$finalstr2 .= $tavalyirecords[$i]["_final"].";";
	}
	echo "<input type='hidden' id='dates' value='$datestr' />";
	echo "<input type='hidden' id='values1' value='$finalstr' />";
	echo "<input type='hidden' id='values2' value='$finalstr2' />";
				
	//canvas
	$leftcanvas = '
		<div class="leftchartlabel">
			<h2 class="anchor2">
				
				<a href="#" style="cursor:text; color:#5da5da;"><span id="tavalylabel1" style="color:#000;">'.$tavalyi.'</span></a>
				VS 
				<a href="#" style="cursor:text; color:#faa43a;"><span id="ideilabel1" style="color:#000;">'.$idei.'</span></a>
				
				<img src="img/dashSetting.png" class="dashbutton" onclick="statOneSettings();" />
			</h2>
		</div>
		<canvas id="chart-left" width="400" height="300"/>
	';
	$rightcanvas = '
		<div class="rightchartlabel">
			<h2 class="anchor2">Pultosok átlagforgalma <img src="img/dashSetting.png" class="dashbutton" onclick="statTwoSettings();" /></h2>
		</div>
		<canvas id="chart-right" width="400" height="300"/>
	';
	
	// TÁROLÓ DOBOZOK
	$str .= "
		<div class='statContainer'>
			<div class='leftCanvasC'>$leftcanvas</div>
			<div class='rightCanvasC'>$usersdata $rightcanvas</div>
		</div>
	";
	
	//HA VAN SORSJEGY
	/* BAL ALSÓ: sorsjegy fogyás */
	if (isset($pubOptions['sorsjegy']) && $pubOptions['sorsjegy'] == 1) {
		
		//KEZDŐ LEKÉRÉSEK JOBB ALSÓ KIADÁSOS STATNAK
		$query = mysql_query("
			SELECT `sorsjegyNyeremenyek`.`sid`, 
			`drinks`.`name`,
			ROUND(`sorsjegyNyeremenyek`.`fogy` * `drinks`.`price`) as 'koltseg'

			FROM `sorsjegyNyeremenyek` 

			LEFT JOIN `drinks`
			ON `drinks`.`ID` = `sorsjegyNyeremenyek`.`did`

			WHERE `sorsjegyNyeremenyek`.`pid` = ".$_SESSION['pub']->ID.";
		");
		$sorsjegyNyKolts = array();
		while ($result = mysql_fetch_assoc($query)) {
			$sorsjegyNyKolts[$result['sid']]['price'] = $result['koltseg'];
			$sorsjegyNyKolts[$result['sid']]['name'] = $result['name'];
		}
		
		//sorsjegy program adatai
		$user2 = "stanphu1_proba";
		$pass2 = "mmmmmm1992:P";
		$database2 = "stanphu1_sorsjegy";
		
		//SOSRJEGY MYSQL CONN
		mysql_close($kapcsolat);
		
		$kapcsolat = mysql_connect($szerver, $user2, $pass2);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database2) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//Lekéri a sorsjegyek fogyását
		$sdates = "";
		$svalues = "";
		$query = mysql_query("
			SELECT COUNT(`sorsjegyek`.`jegy`) as 'db',
			`sorsjegyek`.`felhasznalt`
			FROM `sorsjegyek` 


			WHERE `sorsjegyek`.`hasznalt` = ".$pubOptions['sorsjegyPubId']."
			GROUP BY YEAR(`sorsjegyek`.`felhasznalt`), MONTH(`sorsjegyek`.`felhasznalt`);
		");
		while ($result = mysql_fetch_assoc($query)) {
			$sdates .= date("Y-m", strtotime($result['felhasznalt'])).";";
			$svalues .= $result['db'].";";
		}
		
		//Lekéri nyeremények fogyását hónapokra bontva
		$sorsjegyNyeremeny = array();
		$query = mysql_query("
			SELECT 
			`sorsjegyek`.`nyeremeny`, 
			`sorsjegyek`.`felhasznalt`, 
			COUNT(`sorsjegyek`.`ID`) as 'db'

			FROM `sorsjegyek`

			WHERE `sorsjegyek`.`hasznalt` = ".$pubOptions['sorsjegyPubId']."
			AND `sorsjegyek`.`nyeremeny` != 0


			GROUP BY `sorsjegyek`.`nyeremeny`, YEAR(`sorsjegyek`.`felhasznalt`), MONTH(`sorsjegyek`.`felhasznalt`)

			HAVING `db` > 1
			ORDER BY `sorsjegyek`.`nyeremeny`, `sorsjegyek`.`felhasznalt` ASC; 
		");
		$s = 0;
		$labels = "";
		$spvalues = "";
		while ($result = mysql_fetch_assoc($query)) {
			
			if (!isset($sorsjegyNyeremeny[date("Y-m", strtotime($result['felhasznalt']))]['koltseg'])) {
				$sorsjegyNyeremeny[date("Y-m", strtotime($result['felhasznalt']))]['koltseg'] = 0;
			}
			$sorsjegyNyeremeny[date("Y-m", strtotime($result['felhasznalt']))]['nyeremeny'] = $result['nyeremeny'];
			$sorsjegyNyeremeny[date("Y-m", strtotime($result['felhasznalt']))]['koltseg'] = $sorsjegyNyeremeny[date("Y-m", strtotime($result['felhasznalt']))]['koltseg'] + ($result['db'] * $sorsjegyNyKolts[$result['nyeremeny']]['price']);
			
			
			if (strpos($labels, date("Y-m", strtotime($result['felhasznalt']))) === FALSE) {
				$labels .= date("Y-m", strtotime($result['felhasznalt'])).";";
			}
		}
		
		foreach ($sorsjegyNyeremeny as $key => $value) {
			$spvalues .= $value['koltseg'].";";
		}
		
		
		
		//Kiírja az adatokat js-nek (oldalspecfug.js)
		$str .= "<input type='hidden' id='sdates' value='$sdates' />";
		$str .= "<input type='hidden' id='svalues' value='$svalues' />";
		
		$str .= "<input type='hidden' id='splabels' value='$labels' />";
		$str .= "<input type='hidden' id='spvalues' value='$spvalues' />";
		
		$sleftcanvas = '
			<div class="leftchartlabel">
				<h2 class="anchor2">Sorsjegyek fogyása</h2>
			</div>
			<canvas id="sorsjegy-canvas" width="400" height="300"/>
		';
		
		$srightcanvas = '
			<div class="leftchartlabel">
				<h2 class="anchor2">Nyeremény kiadások</h2>
			</div>
			<canvas id="sorsjegyny-canvas" width="400" height="300"/>
		';
		
		$str .= "
			<p></p>
			<p></p>
			<p></p>
			<div class='statContainer'>
				<div class='leftCanvasC'>$sleftcanvas</div>
				<div class='rightCanvasC'>$srightcanvas</div>
			</div>
		";
	}
	$str .= "<hr></hr>";
	

	//EREDETI KAPCSOLAT VISSZAÁLLÍTÁSA
	mysql_close($kapcsolat);
	
	return $str;
}
	
?>