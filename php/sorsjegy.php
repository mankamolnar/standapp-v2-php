<?php
	
	function sorsjegyBeallitasok() {
		
		include("conf/mysql.php");
		
		//sorsjegy program adatai
		$user2 = "stanphu1_proba";
		$pass2 = "mmmmmm1992:P";
		$database2 = "stanphu1_sorsjegy";
		
		//SOSRJEGY MYSQL CONN
		$kapcsolat = mysql_connect($szerver, $user2, $pass2);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database2) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//nyeremények lekérése
		$query = mysql_query("SELECT * FROM `nyeremenyek`;");
		$nyeremenyek = array();
		while($result = mysql_fetch_assoc($query)) {
			$nyeremenyek[$result['ID']] = $result;
		}
				//select legyártása a nyeremények arrayből
		$select = "<select name='newNyeremeny'>";
		foreach ($nyeremenyek as $NYID => $values) {
			$select .= "<option value='".$NYID."'>".$values['nev']."</option>";
		}
		$select .= "</select>";
		
		mysql_close($kapcsolat);
		
		//STANDAPP MYSQL
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
				//beállítások lekérése
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = '".$_SESSION['pub']->ID."';");
		
		//beállítások betöltése arraybe
		$pubOptions = array();
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result['value'];
		}
		
		//italok lekérése
		$query = mysql_query("SELECT * FROM `drinks` WHERE `visible` = 1 AND `PID` = '".$_SESSION['pub']->ID."';");
		$italok = array();
		while ($result = mysql_fetch_assoc($query)) {
			$italok[$result['ID']] = $result;
		}
		
		//select készítése italok arrayből
		$italselect = "<select name='italselect'>";
		foreach ($italok as $did => $values) {
			$italselect .= "<option value='$did'>".$values['name']."</option>";
		}
		$italselect .= "</select>";
		
		//nyeremenyek lekerese
		$query = mysql_query("SELECT * FROM `sorsjegyNyeremenyek` WHERE `sorsjegyNyeremenyek`.`pid` = '".$_SESSION['pub']->ID."';");
		$aktivnyeremenyek = array();
		while ($result = mysql_fetch_assoc($query)) {
			$aktivnyeremenyek[] = $result;
		}		
		mysql_close($kapcsolat);
		
		//inputok a jquerynek
		echo "<input type='hidden' id='pubid' value='".$_SESSION['pub']->ID."' />";
		
		//FORM MEGJELENÍTÉSE
		echo "
			<span class='anchor2'>Sorsjegy beállításai</span><br />
			<b>Kocsma ID-je a SORSJEGY adatbázisban: </b><br />
			<input type='text' name='sorsjegyPubId' value='".$pubOptions["sorsjegyPubId"]."' /> <input type='submit' value='Mentés!' onclick='sorsjegypidment();' /><br/><br />
		";
		
		//csak akkor jöjjön be a többi form ha már van deklarálva sorsjegyPubId
		if (isset($pubOptions["sorsjegyPubId"])) {
			
			//jutalék
			echo "<b>Jutalék:</b><br /><input type='text' name='jutalek' value='".$pubOptions['jutalek']."' maxlength='4' />% <input type='submit' value='Mentés!' onclick='jutalekment();' /><br /><br />";
			
			//AKTÍV NYEREMÉNYEK
			echo "<span class='anchor2'>Aktív nyeremények</span><br /><b>Sorsjegy nyeremény - Standlap megfelelője - Ennyi fogyasztást vonjon le</b><br />";
			
			//nyeremények listázása
			for ($i = 0; $i < count($aktivnyeremenyek); $i++) {
				echo $nyeremenyek[$aktivnyeremenyek[$i]['sid']]["nev"]." - ".$italok[$aktivnyeremenyek[$i]["did"]]["name"]." - ".$aktivnyeremenyek[$i]["fogy"]." <input type='submit' value='Törlés!' onclick='nyeremenytorol(".$aktivnyeremenyek[$i]["ID"].");' /><br />";
			}
			
			//új nyeremény
			echo "
				<br /><span class='anchor2'>Új aktív nyeremény</span><br />
				$select $italselect <input type='text' name='fogy' />
				<input type='submit' value='Mentés!' onclick='addnyeremeny();' />
			";
			
		}
	}
?>