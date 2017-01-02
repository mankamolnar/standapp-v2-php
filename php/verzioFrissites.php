<?php
	
	include "conf/mysql.php";
			
	//Új elszámolás vagy nem befejezett elszámolás
	$kapcsolat = mysql_connect($szerver, $user, $pass);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
	
	//verzio lekérése
	$query = mysql_query("SELECT * FROM `verzio` ORDER BY `ID` DESC;");
	$verzio = mysql_fetch_assoc($query);
	
	//user látta -e
	$query = mysql_query("SELECT * FROM `verzio2user` WHERE `vid` = ".$verzio['ID']." AND `uid` = ".$_SESSION['ID'].";");
	$latott = mysql_fetch_assoc($query);
	
	//ha nem látta még megjeleníti és
	$vs = "update_".$_SESSION['tether'];
	if (!$latott && $verzio[$vs] != NULL) {
		echo "
			<div class='notyContainer'>
				<div class='notyTop'></div>
				<div class='notyBody'>
					<span class='notyVersion'>".$verzio['verzio']." frissítés:</span> <span class='notyText'>".$verzio[$vs]."</span>
				</div>
				<div class='notyBot'></div>
			</div>
			<br /><br />
		";
		mysql_query("INSERT INTO `".$database."`.`verzio2user` (`ID`, `uid`, `vid`) VALUES (NULL, '".$_SESSION['ID']."', '".$verzio['ID']."');");
	}
	
	//lekéri az adott felhasználóhoz látta -e már 
	mysql_close($kapcsolat);
?>