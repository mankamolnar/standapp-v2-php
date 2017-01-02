<?php
	//KOCSMA KEZELŐ
	function pubHandler() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//PUBOK LISTÁJÁNAK LEKÉRDEZÉSE
		$query = mysql_query("SELECT * FROM `pub`;");
		$i = 0;
		
		//PUB LISTA NYITÓ
		echo "
			<p class='anchor2'>Kocsma módosítás</p>
			<form action='index.php?page=14' method='post' id='frm'>
			<table border='0' cellpadding='3' cellspacing='0'>
				<tr align='center'>
					<td class='td1'><b>ID</b></td>
					<td class='td1'><b>Név</b></td>
					<td class='td1'><b>Jelszó</b></td>
					<td class='td1'><b>Napi Fizetés</b></td>
					<td class='td1'><b>Van-e lottó</b></td>
					<td class='td3'></td>
				</tr>
		";
		
		//KÖZTES SOROK
		while($result = mysql_fetch_assoc($query)) {
			echo "
				<tr align='center'>
					<td class='td1'>
						<b>".$result['ID']."</b>
						<input type='hidden' name='chg$i' value='0' />
						<input type='hidden' name='id$i' value='".$result['ID']."' />
					</td>
					<td class='td1'>
						<input type='text' name='name$i' onkeypress='setuchg($i);' value='".$result['name']."' />
					</td>
					<td class='td1'>
						<input type='text' name='pass$i' onkeypress='setuchg($i);' onblur='setPass($i);' value='".$result['ppass']."' />
					</td>
					<td class='td1'>
						<input type='text' name='dfee$i' onkeypress='setuchg($i);' value='".$result['dfee']."' />
					</td>
					<td class='td1'>
						<input type='text' name='isLotto$i' onkeypress='setuchg($i);' value='".$result['isLotto']."' />
					</td>
					<td class='td3'></td>
				</tr>
			";
			$i++;
		}
		
		$i = 0;
		//záró
		echo "
				<tr align='center'>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td></td>
				</tr>
			</table>
			</form>
			<button onclick='sendPubMod();'>Módosít!</button>
			
			<hr />
			<p class='anchor2'>Új kocsma felvitele</p>
			<form action='index.php?page=15' method='post'>
			<table border='0' cellpadding='3' cellspacing='0'>
				<tr align='center'>
					<td class='td1'><b>ID</b></td>
					<td class='td1'><b>Név</b></td>
					<td class='td1'><b>Jelszó</b></td>
					<td class='td1'><b>Napi Fizu</b></td>
					<td class='td1'><b>Van-e lottó</b></td>
					<td class='td3'></td>
				</tr>
				
				<tr align='center'>
					<td class='td1'>
						<b>1.</b>
					</td>
					<td class='td1'>
						<input type='text' name='name$i' />
					</td>
					<td class='td1'>
						<input type='text' name='passz' onblur='setPass(\"z\");' />
					</td>
					<td class='td1'>
						<input type='text' name='dfee$i' />
					</td>
					<td class='td1'>
						<input type='text' name='isLotto$i' />
					</td>
					<td class='td3'></td>
				</tr>
				
				<tr align='center'>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td></td>
				</tr>
			</table>
			<p><input type='submit' value='Új kocsma felvitele!' /></p>
			</form>
		";

		//Kapcsolótábla
		$kapcs = new Kapcsolo();
		
		//mysql kapcsolat bezárása
		mysql_close($kapcsolat);
	}
	
	//Pub módosítás
	function PubModify() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		$i = 0;
		while (isset($_POST["chg$i"])) {
			
			if ($_POST["chg$i"] == 1) {
				mysql_query("UPDATE  `".$database."`.`pub` SET  `name` =  '".$_POST["name$i"]."', `ppass` =  '".$_POST["pass$i"]."', `dfee` =  '".$_POST["dfee$i"]."', `isLotto` = BIN('".$_POST["isLotto$i"]."') WHERE  `pub`.`ID` =".$_POST["id$i"].";");
			}
			
			$i++;
		}
		
		echo "<b>Sikeresen módosítottad a kocsmákat!</b><br /><br />".menu(3);
		
		//close mysql conn
		mysql_close($kapcsolat);
	}
	
	//Új Pub feltöltés
	function NewPub() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		mysql_query("INSERT INTO `".$database."`.`pub` (`ID`, `name`, `ppass`, `dfee`, `isLotto`) VALUES (NULL, '".$_POST['name0']."', '".$_POST['passz']."', '".$_POST['dfee0']."', BIN('".$_POST['isLotto0']."'));");
		
		echo "<b>Sikeresen felvitted a kocsmát!</b><br /><br />".menu(3);
		
		//mysql close
		mysql_close($kapcsolat);
	}
	
?>