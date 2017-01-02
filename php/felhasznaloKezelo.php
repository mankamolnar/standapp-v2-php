<?php
	
	//FELHASZNÁLÓ KEZELÉS
	function UserSet() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//Get users
		$query = mysql_query("
			SELECT `user`.*
			FROM  `UsPuKapcs` 
			RIGHT JOIN `UsPuKapcs` as `rand` ON `UsPuKapcs`.`PID` = `rand`.`PID`
			LEFT JOIN `user` ON `rand`.`UID` = `user`.`ID`
			WHERE  `UsPuKapcs`.`UID` = ".$_SESSION['ID']."
			AND `user`.`tether` <= ".$_SESSION['tether']."
			GROUP BY `rand`.`UID`;
		");
		
		//write their properties into hidden fields
		$i = 0;
		$users = array();
		while ($result = mysql_fetch_assoc($query)) {
			
			$users[$i] = $result['uname'];
			
			echo "
				<input type='hidden' name='id[$i]' value='".$result['ID']."' />
				<input type='hidden' name='uname[$i]' value='".$result['uname']."' />
				<input type='hidden' name='fullname[$i]' value='".$result['fullname']."' />
				<input type='hidden' name='lakcim[$i]' value='".$result['lakcim']."' />
				<input type='hidden' name='tartozkodasi[$i]' value='".$result['tartozkodasi']."' />
				<input type='hidden' name='szulhely[$i]' value='".$result['szulhely']."' />
				<input type='hidden' name='szulnap[$i]' value='".$result['szulnap']."' />
				<input type='hidden' name='vegzettseg[$i]' value='".$result['vegzettseg']."' />
				<input type='hidden' name='anyjan[$i]' value='".$result['anyjan']."' />
				<input type='hidden' name='maganNyugdij[$i]' value='".$result['maganNyugdij']."' />
				<input type='hidden' name='lakcimKsz[$i]' value='".$result['lakcimKsz']."' />
				<input type='hidden' name='szig[$i]' value='".$result['szig']."' />
				<input type='hidden' name='adosz[$i]' value='".$result['adosz']."' />
				<input type='hidden' name='taj[$i]' value='".$result['taj']."' />
				<input type='hidden' name='pass[$i]' value='".$result['pass']."' />
				<input type='hidden' name='phone[$i]' value='".$result['phone']."' />
				<input type='hidden' name='tether[$i]' value='".$result['tether']."' />
			";
			
			$i++;
		}
		
		//felhasznalo kezelo új
		echo "
			<div id='kezeloContainer'>
				<div id='baloldal'>
				
		";
		
		for ($i = 0; $i < count($users); $i++) {
			echo "<span class='kezeloA' id='i".$i."'>".$users[$i]."</span><br />";
		}
		echo "<span class='kezeloA' id='inew'>új felvitele</span><br />";
		
		echo "
				</div>
				
				<div id='jobboldal'>
					
					<form action='index.php?page=11' method='post' id='felhForm'>
					
						<p class='anchor2' id='felhTitle'>Új felhasználó</p>
						<input type='hidden' name='id' value='' />
						<br />
					
						<p class='anchor2'>felhasználónév</p>
						<input type='text' name='uname' value='' />
						
						<p class='anchor2'>teljes név</p>
						<input type='text' name='fullname' value='' />
						
						<p class='anchor2'>lakcím</p>
						<input type='text' name='lakcim' value='' />
						
						<p class='anchor2'>tartozkodási hely</p>
						<input type='text' name='tartozkodasi' value='' />
						
						<p class='anchor2'>születési hely</p>
						<input type='text' name='szulhely' value='' />
						
						<p class='anchor2'>Születési idö</p>
						<input type='text' name='szulnap' value='' />
						
						<p class='anchor2'>Végzettség</p>
						<input type='text' name='vegzettseg' value='' />
						
						<p class='anchor2'>Anyja neve</p>
						<input type='text' name='anyjan' value='' />
					
						<p class='anchor2'>Van-e magánnyugdíj pénztára</p>
						<select name='maganNyugdij'>
							<option value='0'>Nincs</option>
							<option value='1'>Van</option>
						</select>
						
						<p class='anchor2'>lakcím kártya szám</p>
						<input type='text' name='lakcimKsz' value='' />
					
						<p class='anchor2'>személyi igazolvány száma</p>
						<input type='text' name='szig' value='' />
					
						<p class='anchor2'>adószám</p>
						<input type='text' name='adosz' value='' />
					
						<p class='anchor2'>Taj szám</p>
						<input type='text' name='taj' value='' />
					
						<p class='anchor2'>Jelszó</p>
						<input type='text' name='pass' value='' onblur='setPass();' />
					
						<p class='anchor2'>telefonszám</p>
						<input type='text' name='phone' value='' />
					
						<p class='anchor2'>jogkör</p>
						<select name='tether'>
							<option value='0'>Alkalmazott</option>
							<option value='1'>Üzletvezető</option>
							<option value='2'>Főnök</option>
							<option value='3'>Rendszergazda</option>
						</select><br /><br />
						
						<input type='submit' value='Mentés!' />
					</form>
					
				</div>
				
			</div>
		";
		//Kapcsolótábla
		$kapcs = new Kapcsolo();
		//KAPCSOLAT BONTÁS
		mysql_close($kapcsolat);
	}
	
	//FELHASZNÁLÓ MÓDOSÍTÁS
	function UserModify() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//Check tether. users cant modify to bigger authority
		if ($_POST['tether'] <= $_SESSION['tether']) {
			if(!empty($_POST['pass'])) {
				$query = "
					UPDATE  `".$database."`.`user` SET  
					`uname` =  '".$_POST['uname']."',
					`fullname` =  '".$_POST['fullname']."',
					`lakcim` =  '".$_POST['lakcim']."',
					`tartozkodasi` =  '".$_POST['tartozkodasi']."',
					`szulhely` =  '".$_POST['szulhely']."',
					`szulnap` =  '".$_POST['szulnap']."',
					`vegzettseg` =  '".$_POST['vegzettseg']."',
					`anyjan` =  '".$_POST['anyjan']."',
					`maganNyugdij` = BIN('".$_POST['maganNyugdij']."') ,
					`lakcimKsz` =  '".$_POST['lakcimKsz']."',
					`szig` =  '".$_POST['szig']."',
					`adosz` =  '".$_POST['adosz']."',
					`taj` =  '".$_POST['taj']."',
					`pass` =  '".$_POST['pass']."',
					`phone` =  '".$_POST['phone']."',
					`tether` = '".$_POST['tether']."'
					WHERE  `user`.`ID` =".$_POST['id'].";
				";
			}
			else {
				$query = "
					UPDATE  `".$database."`.`user` SET  
					`uname` =  '".$_POST['uname']."',
					`fullname` =  '".$_POST['fullname']."',
					`lakcim` =  '".$_POST['lakcim']."',
					`tartozkodasi` =  '".$_POST['tartozkodasi']."',
					`szulhely` =  '".$_POST['szulhely']."',
					`szulnap` =  '".$_POST['szulnap']."',
					`vegzettseg` =  '".$_POST['vegzettseg']."',
					`anyjan` =  '".$_POST['anyjan']."',
					`maganNyugdij` = BIN('".$_POST['maganNyugdij']."') ,
					`lakcimKsz` =  '".$_POST['lakcimKsz']."',
					`szig` =  '".$_POST['szig']."',
					`adosz` =  '".$_POST['adosz']."',
					`taj` =  '".$_POST['taj']."',
					`phone` =  '".$_POST['phone']."',
					`tether` = '".$_POST['tether']."'
					WHERE  `user`.`ID` =".$_POST['id'].";
				";
			}		
		
			mysql_query($query);
			
		} else {
		
			echo "<b>A felhasználód jogkörénél magasabb jogkörű felhasználót nem áll módodban létrehozni!</b>";
			exit;
			
		}
		
		//KAPCSOLAT BONTÁSA
		mysql_close($kapcsolat);
		
		//megnézzük, honnan érkezett a kérelem, felhasznál szerkesztőből vagy saját adatok szerkesztéséből
		parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $getParams);
		$from = $getParams['page'];
		if($from == 19) { //személyes beállításokból jött
			header("Location: index.php?page=19");
		}
		else { //felhasználó szerkesztőből/máshonnan jött
			header("Location: index.php?page=9");
		}
		
	}
	
	//ÚJ FELHASZNÁLÓ
	function NewUser() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//FELTÖLT
		mysql_query("INSERT INTO `".$database."`.`user` (`ID`, `uname`, `fullname`, `lakcim`, `tartozkodasi`, `szulhely`, `szulnap`, `vegzettseg`, `anyjan`, `maganNyugdij`, `lakcimKsz`, `szig`, `adosz`, `taj`, `pass`, `phone`, `tether`) VALUES (NULL, '".mysql_real_escape_string($_POST['uname'])."', '".mysql_real_escape_string($_POST['fullname'])."', '".mysql_real_escape_string($_POST['lakcim'])."', '".mysql_real_escape_string($_POST['tartozkodasi'])."', '".mysql_real_escape_string($_POST['szulhely'])."', '".mysql_real_escape_string($_POST['szulnap'])."', '".mysql_real_escape_string($_POST['vegzettseg'])."', '".mysql_real_escape_string($_POST['anyjan'])."', BIN('".mysql_real_escape_string($_POST['maganNyugdij'])."'), '".mysql_real_escape_string($_POST['lakcimKsz'])."', '".mysql_real_escape_string($_POST['szig'])."', '".mysql_real_escape_string($_POST['adosz'])."', '".mysql_real_escape_string($_POST['taj'])."', '".mysql_real_escape_string($_POST['pass'])."', '".mysql_real_escape_string($_POST['phone'])."', '".$_POST['tether']."');");
		$id = mysql_insert_id();
		
		//KApCSOLÓTÁBLA :D
		if ($id != 0) {
			mysql_query("INSERT INTO `".$database."`.`UsPuKapcs` (`ID`, `PID`, `UID`) VALUES (NULL, '".$_SESSION['pub']->ID."', '".$id."');");
		}
		
		//Mysql kapcsolat bontása
		mysql_close($kapcsolat);
		
		header("Location: index.php?page=9");
	}
	
?>