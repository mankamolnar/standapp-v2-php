<?php

function dashBirthday($pub) {
	
	//konfiguráció
	$szerver2 = "server9.mysql-host.eu";
	$user2 = "stanphu1_mailman";
	$pass2 = "mmmmmm1992:P";
	$database2 = "stanphu1_mailman";
	
	//kapcsolódás a szerverhez
	$kapcsolat = mysql_connect($szerver2, $user2, $pass2);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database2) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
	
	//szülinaposok lekérése
	$birthdays = "";
	$query = mysql_query("
	SELECT * FROM `login` 
	WHERE `pub` LIKE 'park'
	AND MONTH(`birthday`) = '".date("m")."'
	AND `email` NOT LIKE ''
	GROUP BY `email`");
	while ($result = mysql_fetch_assoc($query)) {
		$birthdays .= "<a href='index.php?page=30&fname=".$result['fname']."&lname=".$result['lname']."&email=".$result['email']."' class='notyText'>".$result['lname']." ".$result['fname']."</a><br /><br />";
	}
	
	$str = "
		<h2 class='anchor2'>E-havi születésnaposok</h2>
		<div class='notyContainer'>
			<div class='notyTop'></div>
			<div class='notyBody'>
				$birthdays
			</div>
			<div class='notyBot'></div>
		</div>
		<br />
		<hr />
	";
	
	//mysql kapcsolat zárása
	mysql_close($kapcsolat);
	
	return $str;
}

function sendInvitation() {
	
	include "conf/mysql.php";
	
	//Új elszámolás vagy nem befejezett elszámolás
	$kapcsolat = mysql_connect($szerver, $user, $pass);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
	
	//kiválasztjuk mire hívjuk meg
	if (isset($_GET['fname'])) {
		
		//italok lekérése
		$dquery = mysql_query("SELECT * FROM `drinks` WHERE `visible` = 1 AND `PID` = ".$_SESSION['pub']->ID." ORDER BY `name`;");
		$dselect = "<select name='ital'>";
		while ($dresult = mysql_fetch_assoc($dquery)) {
			$dselect .= "<option value='".$dresult['name']."'>".$dresult['name']."</option>";
		}
		$dselect .= "</select>";
		
		echo "
			<form action='index.php?page=30' method='post'>
				<input type='hidden' name='email' value='".$_GET['email']."' />
				<input type='hidden' name='fname' value='".$_GET['fname']."' />
				<input type='hidden' name='lname' value='".$_GET['lname']."' />
			
				<h2 class='anchor2'>Mire hívod meg?</h2>
				$dselect
				<br /><br />
				
				<h2 class='anchor2'>Milyen mennyiségben</h2>
				<input type='text' name='mennyiseg' />
				<br /><br />
				
				<input type='submit' value='Meghívás!' />
			</form>
		";
		
	//levél kiküldése
	} else if (isset($_POST['fname'])) {
		
		//hozzáadás adatbázishoz
		mysql_query("INSERT INTO `".$database."`.`birthdayInvitation` (`ID`, `PID`, `fname`, `lastname`, `email`, `ital`, `mennyiseg`, `felhasznalt`) VALUES (NULL, '".$_SESSION['pub']->ID."', '".$_POST['fname']."', '".$_POST['lname']."', '".$_POST['email']."', '".$_POST['ital']."', '".$_POST['mennyiseg']."', '0');");
		$code = mysql_insert_id();
		
		
		//levél kiküldése
		$to      = $_POST['email'];
		$subject = 'Boldog születésnapot kíván a '.$_SESSION['pub']->name.'! Vendégünk vagy '.$_POST['mennyiseg']." ".$_POST['ital']."-ra!";
		$headers = "From: info@standapp.hu <info@standapp.hu>\r\n". 
               "MIME-Version: 1.0" . "\r\n" . 
               "Content-type: text/html; charset=UTF-8" . "\r\n";
		$message = '
			<h1 style="text-align:center;">Boldog születésnapot kíván a '.$_SESSION['pub']->name.'!</h1>
			<h2 style="text-align:center;">Vendégünk vagy '.$_POST['mennyiseg'].' '.$_POST['ital'].'-ra!</h2>
			<h3 style="text-align:center;">A következő sorszámot kell megadd a pultnál hogy megkapd az ajándékod:<br />'.$code.'</h3>';

		mail($to, $subject, $message, $headers);
		
		echo "<b>Elküldtük a levelet!</b>";
		
	}
	
	mysql_close($kapcsolat);
}

//szülinapos ellenőrzése
function checkInvitation() {
	
	include "conf/mysql.php";

	//Új elszámolás vagy nem befejezett elszámolás
	$kapcsolat = mysql_connect($szerver, $user, $pass);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
	
	//ellenőrzés végrehajtása
	if (isset($_POST['code'])) {
		
		$query = mysql_query("SELECT * FROM  `birthdayInvitation` WHERE  `ID` =".$_POST['code']." AND `felhasznalt` = 0;");
		$result = mysql_fetch_assoc($query);
		
		//eredmény
		if (is_array($result)) {
			
			echo "
				<form action='index.php?page=31' method='post'>
				<h2 class='anchor2'>Ellenörzés eredménye:</h2>
				<b>Név:</b> ".$result['lastname']." ".$result['fname']."<br />
				<b>E-mail:</b> ".$result['email']."<br />
				<b>Ajándék:</b> ".$result['mennyiseg']." ".$result['ital']."<br /><br />
				
				<input type='hidden' name='code2' value='".$_POST['code']."' />
				<input type='submit' value='Kiadom az ajándékot!' />
				</form>
			";
			
		} else {
			echo "<h2 class='anchor2'>NINCS ILYEN KÓD VAGY MÁR HASZNÁLT!</h2>";
			
		}
	
	//Lezárás
	} else if (isset($_POST['code2'])) {
	
		mysql_query("UPDATE  `".$database."`.`birthdayInvitation` SET  `felhasznalt` =  '1' WHERE  `birthdayInvitation`.`ID` =".$_POST['code2'].";");
		
		echo "<b>Ne felejtsd el a standlapon beírni a kiadások közé az ajándékot ;) Most már visszaléphetsz a főoldalra!</b>";
	
	//FORM
	} else {
		
		echo "
			<form action='index.php?page=31' method='post'>
				<h2 class='anchor2'>Kapott kód:</h2>
				<input type='text' name='code' /><br /><br />
				<input type='submit' value='Ellenőrzés!' />
			</form>";
	}
	
	mysql_close($kapcsolat);
	
}
	
?>