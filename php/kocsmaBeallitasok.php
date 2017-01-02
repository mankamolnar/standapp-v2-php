<?php
	
	//beállítások ellenőrzése, nem létező beállítások létrehozása 0ra
	function getOption($pubOptions, $checkMe) {
		
		if (!isset($pubOptions[$checkMe])) {
			$pubOptions[$checkMe] = 0;
		}
		
		return $pubOptions[$checkMe];
	}
	
	//kocsma beállítások megjelítése
	function kocsmaBeallitasok() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//Üzenet megjelenítése ha van
		if (isset($_GET['message'])) {
			echo "<b>".$_GET['message']."</b><br /><br />";
		}
		
		//akkor jelenjen meg ha nagyobb mint üzletvezető felhasználó
		if ($_SESSION['tether'] > 1) {
		
			//beállítások lekérése
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = '".$_SESSION['pub']->ID."';");
			
			//beállítások betöltése arraybe
			$pubOptions = array();
			while ($result = mysql_fetch_assoc($query)) {
				$pubOptions[$result['option']] = $result['value'];
			}
			
			//lottó modul lekérése és betétele pubOptionsba
			$query = mysql_query("SELECT * FROM `pub` WHERE `pub`.`ID` = '".$_SESSION['pub']->ID."';");
			$result = mysql_fetch_assoc($query);
			$pubOptions['isLotto'] = $result['isLotto'];
			
			echo "
				<table cellspacing='0'>
				
					<tr class='osszesitoSor'>
						<td class='td1' colspan='2'>
							<b>Kocsma beállítások</b>
							<input type='hidden' id='pubid' value='".$_SESSION['pub']->ID."' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>					
						<td class='tdSettingName'>
							Lottó terminál stand (a módosítás érvénybe lépéséhez ki-be jelentkezés szükséges)
							<input type='hidden' id='isLotto' value='".getOption($pubOptions, "isLotto")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='lottoSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Módosítható sorok
							<input type='hidden' id='modosithatoStand' value='".getOption($pubOptions, "modosithatoStand")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='modSorSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Üzenőfal
							<input type='hidden' id='chatbox' value='".getOption($pubOptions, "chatbox")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='chatboxSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Nyitó automatikus másolása maradványhoz
							<input type='hidden' id='nyitoToMaradvany' value='".getOption($pubOptions, "nyitoToMaradvany")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='NyitoToMaradvanySw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Sorsjegy támogatás
							<input type='hidden' id='sorsjegy' value='".getOption($pubOptions, "sorsjegy")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='sorsjegySw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Új bevétel kiadás modul
							<input type='hidden' id='ujkiadas' value='".getOption($pubOptions, "ujKiadas")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='ujkiadasSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Saját fogyasztás modul
							<input type='hidden' id='sajatfogyasztas' value='".getOption($pubOptions, "sajatFogyasztas")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='sajatfogyasztasSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Stand Ellenőrzés Checkbox
							<input type='hidden' id='standellenorzes' value='".getOption($pubOptions, "standEllenorzes")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='standellenorzesSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Étel fogyás
							<input type='hidden' id='etelfogyasztas' value='".getOption($pubOptions, "etelFogyasztas")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='etelfogyasztasSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Plusz raktár modul
							<input type='hidden' id='pluszraktar' value='".getOption($pubOptions, "pluszRaktar")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='pluszraktarSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Bankkártya modul
							<input type='hidden' id='bankkartya' value='".getOption($pubOptions, "bankkartya")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='bankkartyaSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Egyéb forgalom modul
							<input type='hidden' id='egyebforgalom' value='".getOption($pubOptions, "egyebForgalom")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='egyebforgalomSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							Jutalék
							<input type='hidden' id='jutalek' value='".getOption($pubOptions, "jutalek")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='jutalekSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr class='sor1'>
						<td class='tdSettingName'>
							LikeMyWifi
							<input type='hidden' id='likemywifi' value='".getOption($pubOptions, "likemywifi")."' />
						</td>
						
						<td class='td1'>
							<div class='switch'>
								<div class='switchBt' id='likemywifiSw'></div>
							</div>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr>
						<td class='td2'>
							
						</td>
						
						<td class='td2'>
							
						</td>
						
						<td>
						
						</td>
					<tr>
				</table>
				<hr />
			";
			
		}
		
		//fix kiadás lista
		fixKiadasList($kapcsolat);
		
		//saját fogyasztás modul
		if (getOption($pubOptions, "sajatFogyasztas") == 1) {
			sajatFogyasztasList($kapcsolat);
		}
		
		//saját fogyasztás modul
		if (getOption($pubOptions, "egyebForgalom") == 1) {
			egyebForgalomList($kapcsolat);
		}
		
		//plusz raktar modul
		if (getOption($pubOptions, "pluszRaktar") == 1) {
			pluszRaktarList($kapcsolat);
		}
		
		//likemywifi
		if (getOption($pubOptions, "likemywifi") == 1) {
			likeMyWifiSet($kapcsolat);
		}
		
		mysql_close($kapcsolat);
		
	}
	
	//Fix kiadások beállítása
	function fixKiadasList($kapcsolat) {
		
		//létező fix kiadások lekérése
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = '".$_SESSION['pub']->ID."' AND `pubOptions`.`option` LIKE 'fixKiadas';");
		
		echo "
			<table cellspacing='0'>
		
				<tr class='osszesitoSor'>					
					<td class='td1' colspan='2'>
						<b>Fix kiadások beállítása</b>
					</td>
					
					<td class='td3'>
						<form action='index.php?page=27' method='post' id='fixSorFrm1'>
							<input type='hidden' name='action' value='no' />
							<input type='hidden' name='fixKiadPid' value='".$_SESSION['pub']->ID."' />
							<input type='hidden' name='fixKiadId' value='' />
							<input type='hidden' name='fixKiadValue' value='' />
						</form>
					</td>
				</tr>
		";
		
		//kilistázás
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//sor megjelenítése
			echo "
				<tr class='sor0'>					
					<td class='tdSettingName'>
						<input type='hidden' id='fixKiadId$i' value='".$result['ID']."' />
						<input type='text' class='inputTextSmall' id='fixKiadNev$i' value='".$result['value']."' />
					</td>
					
					<td class='td1'>
						<img src='img/close.png' class='fixKiadasButton' alt ='Törlés' onclick='kiadTorles($i);' /> &nbsp; <img src='img/itallapFloppy.png' class='fixKiadasButton' alt='Mentés' onclick='kiadMentes($i);' />
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
			";
			
			$i++;
		}
		
		//táblázat lezárása
		echo "
				<tr class='sor0'>					
					<form action='index.php?page=27' id='kiadFrm' method='post'>
						
						<td class='tdSettingName'>
							<input type='hidden' name='action' value='ujsor' />
							<input type='hidden' name='ujsorpid' value='".$_SESSION['pub']->ID."' />
							<input type='text' class='inputTextSmall' name='ujFixKiadNeve' value='".$result['value']."' />
						</td>
						
						<td class='td1'>
							<img src='img/add.png' class='fixKiadasButton' alt ='Új felvitele!' onclick='$(\"#kiadFrm\").submit();' />
						</td>
						
						<td class='td3'>
							
						</td>
						
					</form>
				</tr>
				
				<tr>
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				<tr>
			</table>
		";
	}
	
	//saját fogyasztás fogyasztóinak listázása
	function sajatFogyasztasList($kapcsolat) {
		
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `option` LIKE 'sajatFogyasztasNev' AND `PID` = ".$_SESSION['pub']->ID.";");
		
		echo "
			<hr />
			<table cellspacing='0'>
		
				<tr class='osszesitoSor'>					
					<td class='td1' colspan='2'>
						<b>Saját fogyasztás fogyasztóinak kezelése</b>
					</td>
					
					<td class='td3'>
						<form action='index.php?page=27' method='post' id='sajatfogyaFrm'>
							<input type='hidden' name='action' value='no' />
							<input type='hidden' name='sajatpid' value='".$_SESSION['pub']->ID."' />
							<input type='hidden' name='sajatkiadid' value='' />
							<input type='hidden' name='sajatkiadvalue' value='' />
						</form>
					</td>
				</tr>
		";
		
		//kilistázás
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//sor megjelenítése
			echo "
				<tr class='sor0'>					
					<td class='tdSettingName'>
						<input type='hidden' id='sajatfogyid$i' value='".$result['ID']."' />
						<input type='text' class='inputTextSmall' id='sajatfogyvalue$i' value='".$result['value']."' />
					</td>
					
					<td class='td1'>
						<img src='img/close.png' class='fixKiadasButton' alt ='Törlés' onclick='sajatfogytorol($i);' /> &nbsp; <img src='img/itallapFloppy.png' class='fixKiadasButton' alt='Mentés' onclick='sajatfogymentes($i);' />
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
			";
			
			$i++;
		}
		
		//táblázat lezárása
		echo "
				<tr class='sor0'>					
					<form action='index.php?page=27' id='sajatfogyFrm' method='post'>
						
						<td class='tdSettingName'>
							<input type='hidden' name='action' value='ujsajatfogy' />
							<input type='hidden' name='ujsorpid' value='".$_SESSION['pub']->ID."' />
							<input type='text' class='inputTextSmall' name='ujsajatfogyNeve' value='".$result['value']."' />
						</td>
						
						<td class='td1'>
							<img src='img/add.png' class='fixKiadasButton' alt ='Új felvitele!' onclick='$(\"#sajatfogyFrm\").submit();' />
						</td>
						
						<td class='td3'>
							
						</td>
						
					</form>
				</tr>
				
				<tr>
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				<tr>
			</table>
		";
		
	}
	
	//egyéb forgalmak lekezelése
	function egyebForgalomList($kapcsolat) {
		
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `option` LIKE 'egyebForgalomNev' AND `PID` = ".$_SESSION['pub']->ID.";");
		
		echo "
			<hr />
			<table cellspacing='0'>
		
				<tr class='osszesitoSor'>					
					<td class='td1' colspan='2'>
						<b>Egyéb forgalom modul</b>
					</td>
					
					<td class='td3'>
						<form action='index.php?page=27' method='post' id='egyebforgFrm'>
							<input type='hidden' name='action' value='no' />
							<input type='hidden' name='egyebforgpid' value='".$_SESSION['pub']->ID."' />
							<input type='hidden' name='egyebforgid' value='' />
							<input type='hidden' name='egyebforgvalue' value='' />
						</form>
					</td>
				</tr>
		";
		
		//kilistázás
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//sor megjelenítése
			echo "
				<tr class='sor0'>					
					<td class='tdSettingName'>
						<input type='hidden' id='egyebforgid$i' value='".$result['ID']."' />
						<input type='text' class='inputTextSmall' id='egyebforgvalue$i' value='".$result['value']."' />
					</td>
					
					<td class='td1'>
						<img src='img/close.png' class='fixKiadasButton' alt ='Törlés' onclick='egyebforgtorol($i);' /> &nbsp; <img src='img/itallapFloppy.png' class='fixKiadasButton' alt='Mentés' onclick='egyebforgmentes($i);' />
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
			";
			
			$i++;
		}
		
		//táblázat lezárása
		echo "
				<tr class='sor0'>					
					<form action='index.php?page=27' id='newegyebforgFrm' method='post'>
						
						<td class='tdSettingName'>
							<input type='hidden' name='action' value='ujegyebforg' />
							<input type='hidden' name='ujegyebforgpid' value='".$_SESSION['pub']->ID."' />
							<input type='text' class='inputTextSmall' name='ujegyebforgNeve' value='".$result['value']."' />
						</td>
						
						<td class='td1'>
							<img src='img/add.png' class='fixKiadasButton' alt ='Új felvitele!' onclick='$(\"#newegyebforgFrm\").submit();' />
						</td>
						
						<td class='td3'>
							
						</td>
						
					</form>
				</tr>
				
				<tr>
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				<tr>
			</table>
		";
		
	}
	
	//saját fogyasztás fogyasztóinak listázása
	function pluszRaktarList($kapcsolat) {
		
		$query = mysql_query("SELECT * FROM `pluszRaktar` WHERE `PID` = ".$_SESSION['pub']->ID.";");
		
		echo "
			<hr />
			<table cellspacing='0'>
		
				<tr class='osszesitoSor'>					
					<td class='td1' colspan='2'>
						<b>Plusz raktárok</b>
					</td>
					
					<td class='td3'>
						<form action='index.php?page=27' method='post' id='praFrm'>
							<input type='hidden' name='action' value='no' />
							<input type='hidden' name='ppid' value='".$_SESSION['pub']->ID."' />
							<input type='hidden' name='prid' value='' />
							<input type='hidden' name='prname' value='' />
						</form>
					</td>
				</tr>
		";
		
		//kilistázás
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//sor megjelenítése
			echo "
				<tr class='sor0'>					
					<td class='tdSettingName'>
						<input type='hidden' id='prid$i' value='".$result['ID']."' />
						<input type='text' class='inputTextSmall' id='prvalue$i' value='".$result['nev']."' />
					</td>
					
					<td class='td1'>
						<img src='img/itallapFloppy.png' class='fixKiadasButton' alt='Mentés' onclick='prmentes($i);' />
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
			";
			
			$i++;
		}
		
		//táblázat lezárása
		echo "
				<tr class='sor0'>					
					<form action='index.php?page=27' id='prFrm' method='post'>
						
						<td class='tdSettingName'>
							<input type='hidden' name='action' value='ujprid' />
							<input type='hidden' name='ujprpid' value='".$_SESSION['pub']->ID."' />
							<input type='text' class='inputTextSmall' name='ujprnev' />
						</td>
						
						<td class='td1'>
							<img src='img/add.png' class='fixKiadasButton' alt ='Új felvitele!' onclick='$(\"#prFrm\").submit();' />
						</td>
						
						<td class='td3'>
							
						</td>
						
					</form>
				</tr>
				
				<tr>
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				<tr>
			</table>
		";
		
	}
	
	//likemywifi azonosító beállítása
	function likeMyWifiSet($kapcsolat) {
		
		//Lekérjük van-e azonosító
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `PID` = ".$_SESSION['pub']->ID." AND `option` LIKE 'likemywifiID';");
		$result = mysql_fetch_assoc($query);
		
		//Változó beállítás a táblázatba
		if (isset($result['value'])) {
			$value = $result['value'];
			$id = $result['ID'];
			
		} else {
			$value = "";
			$id = "0";
			
		}
		
		//Táblázat
		echo "
			<hr />
			<table cellspacing='0'>
		
				<tr class='osszesitoSor'>					
					<td class='td1'>
						<b>LikeMyWifi Azonosító</b>
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
		
				<tr class='sor0'>					
					<td class='tdSettingName'>
						<form action='index.php?page=27' id='lmwFrm' method='post'>
							<input type='hidden' name='action' value='lmwset' />
							<input type='hidden' name='lmwid' value='".$id."' />
							<input type='text' class='inputTextSmall' name='lmwvalue' value='".$value."' />
							<input type='submit' value='Felvitel!' />
						</form>
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
				
				<tr>
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				</tr>
				
			</table>
		";
		
	}

	//új sor, sor frissítés sor törlés
	function dataActions() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		if ($_POST['action'] == "ujsor") {
			mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_POST['ujsorpid']."', 'fixKiadas', '".$_POST['ujFixKiadNeve']."');");
			header("Location: index.php?page=28&message=Sikeresen%20hozzá%20lett%20adva%20az%20új%20sor!");
			
		} else if ($_POST['action'] == "sorTorles") {
			mysql_query("DELETE FROM `".$database."`.`pubOptions` WHERE `pubOptions`.`ID` = ".$_POST['fixKiadId'].";");
			header("Location: index.php?page=28&message=Sikeresen%20törölve%20lett!");
			
		} else if ($_POST['action'] == "sorFrissites") {
			mysql_query("UPDATE `".$database."`.`pubOptions` SET `value` = '".$_POST['fixKiadValue']."' WHERE `pubOptions`.`ID` = ".$_POST['fixKiadId'].";");
			header("Location: index.php?page=28&message=Sikeresen%20módosítva%20lett!");
			
		} else if ($_POST['action'] == "sajatfogyT") {
			mysql_query("DELETE FROM `".$database."`.`pubOptions` WHERE `pubOptions`.`ID` = ".$_POST['sajatkiadid'].";");
			header("Location: index.php?page=28&message=Sikeresen%20törölve%20lett!");
			
		} else if ($_POST['action'] == "sajatfogyF") {
			mysql_query("UPDATE `".$database."`.`pubOptions` SET `value` = '".$_POST['sajatkiadvalue']."' WHERE `pubOptions`.`ID` = ".$_POST['sajatkiadid'].";");
			header("Location: index.php?page=28&message=Sikeresen%20módosítva%20lett!");
			
		} else if ($_POST['action'] == "prfriss") {
			mysql_query("UPDATE `".$database."`.`pluszRaktar` SET `nev` = '".$_POST['prname']."' WHERE `pluszRaktar`.`ID` = ".$_POST['prid'].";");
			header("Location: index.php?page=28&message=Sikeresen%20módosítva%20lett!");
			
		} else if ($_POST['action'] == "ujsajatfogy") {
			mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_POST['ujsorpid']."', 'sajatFogyasztasNev', '".$_POST['ujsajatfogyNeve']."');");
			header("Location: index.php?page=28&message=Sikeresen%20hozzá%20lett%20adva%20az%20új%20sor!");
			
		} else if ($_POST['action'] == "ujprid") {
			mysql_query("INSERT INTO `".$database."`.`pluszRaktar` (`ID`, `PID`, `nev`) VALUES (NULL, '".$_POST['ujprpid']."', '".$_POST['ujprnev']."');");
			header("Location: index.php?page=28&message=Sikeresen%20hozzá%20lett%20adva%20az%20új%20sor!");
			
		} else if ($_POST['action'] == "lmwset") {
			
			if ($_POST['lmwid'] == 0) {
				mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_SESSION['pub']->ID."', 'likemywifiID', '".$_POST['lmwvalue']."');");
				
			} else {
				mysql_query("UPDATE `".$database."`.`pubOptions` SET `value` = '".$_POST['lmwvalue']."' WHERE `pubOptions`.`ID` = ".$_POST['lmwid'].";");
				
			}
			header("Location: index.php?page=28&message=Sikeresen%20beállítva!");
			
		} else if ($_POST['action'] == "ujegyebforg") {
			mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_POST['ujegyebforgpid']."', 'egyebForgalomNev', '".$_POST['ujegyebforgNeve']."');");
			header("Location: index.php?page=28&message=Sikeresen%20hozzá%20lett%20adva%20az%20új%20sor!");
			
		} else if ($_POST['action'] == "egyebforgdelete") {
			mysql_query("DELETE FROM `".$database."`.`pubOptions` WHERE `pubOptions`.`ID` = ".$_POST['egyebforgid'].";");
			header("Location: index.php?page=28&message=Sikeresen%20törölve%20lett!");
			
		} else if ($_POST['action'] == "egyebforgmentes") {
			mysql_query("UPDATE `".$database."`.`pubOptions` SET `value` = '".$_POST['egyebforgvalue']."' WHERE `pubOptions`.`ID` = ".$_POST['egyebforgid'].";");
			header("Location: index.php?page=28&message=Sikeresen%20módosítva%20lett!");
			
		}
		
		mysql_close($kapcsolat);
	}
?>