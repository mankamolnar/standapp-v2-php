<?php
	
	//stand átadás
	function standAtad() {
		
		//MYSQL CONFIG
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset("utf8", $kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//elso lepes
		if (!isset($_GET['step'])) {
		
			if (isset($_GET['logfail'])) {
				echo "<b>HIBÁSAN BEÜTÖTT FELHASZNÁLÓNÉV VAGY JELSZÓ</b><br />";
			}
			
			echo "
				<b>Stand átadása</b><br /><br />
				
				<form action='index.php?page=25&step=2' method='post'>
					<input type='hidden' name='id' value='".$_GET['id']."' />
					Átadó felhasználó neve: <input type='text' name='user' /><br /><br />
					Átadó jelszava: <input type='password' name='pass' /><br /><br />
					<input type='submit' value='Tovább' />
				</form>
			";
			
		}
		
		//masodik. atado felhasználó ellenőrzés
		if (isset($_GET['step']) && $_GET['step'] == "2") {
			
			//query
			$query = mysql_query("SELECT * FROM  `user` WHERE  `uname` LIKE  '".mysql_real_escape_string($_POST['user'])."' LIMIT 0 , 30;");
		
			//CHECK
			$ok = false;
			while ($result = mysql_fetch_assoc($query)) {
			
				//LOAD INTO SESSION
				
				if ($_POST['user'] == $result['uname'] && md5($_POST['pass']) == $result['pass'] && $result['ID'] == $_SESSION['ID'] ) {
				
					$ok = true;
				
					break 1;
				}
			
			}
			
			//ha helyes a felhasználó
			if ($ok == true) {
				
				echo "
					<b>Stand átadása</b><br /><br />
					
					<form action='index.php?page=25&step=3' method='post'>
						<input type='hidden' name='id' value='".$_POST['id']."' />
						Átvevő felhasználó neve: <input type='text' name='user' /><br /><br />
						Átvevő jelszava: <input type='password' name='pass' /><br /><br />
						<input type='submit' value='Tovább' />
					</form>
				";
				
			} else {
				
				//header("Location: index.php?page=25&logfail=true");
				
			}
			
		}
		
		//masodik. atado felhasználó ellenőrzés
		if (isset($_GET['step']) && $_GET['step'] == "3") {
			
			//query
			$query = mysql_query("SELECT * FROM  `user` WHERE  `uname` LIKE  '".mysql_real_escape_string($_POST['user'])."' LIMIT 0 , 30;");
		
			//CHECK
			$ok = false;
			$gotUser = -1;
			while ($result = mysql_fetch_assoc($query)) {
			
				//LOAD INTO SESSION
				if ($_POST['user'] == $result['uname'] && md5($_POST['pass']) == $result['pass']) {
				
					$ok = true;
					$gotUser = $result['ID'];
				
					break 1;
				}
			
			}
			
			//ha helyes a felhasználó
			if ($ok == true) {
				
				mysql_query("UPDATE `".$database."`.`standok` SET `UID` = '".$gotUser."' WHERE `standok`.`ID` = ".$_POST['id'].";");
				header("Location: index.php");
				
			} else {
				
				header("Location: index.php?page=25&logfail=true");
				
			}
			
		}
		
		mysql_close($kapcsolat);
	}
	
	//ELSZÁMOLÁS FELTÖLTÉS
	function ElszamFeltolt() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset("utf8", $kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//session átrakása postba
		if ($_GET['convertSess'] == "true") {
			$_POST = $_SESSION['tmpData'];
		}

		//KIADÁSOK FELTÖLTÉSE
		if (isset($_POST['kiadasNev'])) {
		
			$kn = $_POST['kiadasNev'];
			$ka = $_POST['kiadasAr'];
			$i = 0;
			$j = 0;
			
			while (count($_POST['kiadasNev']) != $j) {
				
				if (isset($kn[$i])) {
					mysql_query("INSERT INTO `".$database."`.`kiadasok` (`ID`, `nev`, `ertek`, `SID`) VALUES (NULL, '".$kn[$i]."', '".$ka[$i]."', '".$_POST['sid']."');");
					$j++;
				}
				
				$i++;
			}
		}
		
		//LOTTO FELTÖLTÉS
		if ($_SESSION['pub']->isLotto == 1) {
			
			//lottó érték beállítások
			if (isset($_POST['LotNet'])) {
				$ln = $_POST['LotNet'];
				$lo = $_POST['LotAll'];
			} else {
				$ln = array(1);
				$lo = array(1);
			}
			
			$i = 0;
			$j = 0;
			
			while (count($ln) != $j) {
				
				if (isset($ln[$i])) {
					mysql_query("INSERT INTO `".$database."`.`LottoStand` (`ID`, `NetForg`, `Ossz`, `Nap`, `SID`) VALUES (NULL, '".$ln[$i]."', '".$lo[$i]."', '".($j+1)."', '".$_POST['sid']."');");
					$j++;
				}
				
				$i++;
			}
			
		}
		
		//WTIME & finished
		mysql_query("UPDATE  `".$database."`.`standok` SET  `wtime` =  '".$_POST['wtime']."', `finished` = '1' WHERE  `standok`.`ID` =".$_POST['sid'].";");
		
		//MYSQL CLOSE
		mysql_close($kapcsolat);
	}
	
	//ELSZÁMOLÁS MÓDOSÍTÁS FELTÖLTÉS
	function ElszamModFel() {
		
		//MYSQL CONFIG
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset("utf8", $kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//session átrakása postba
		if ($_GET['convertSess'] == "true") {
			$_POST = $_SESSION['tmpData'];
		}
		
		//AKCIÓS SOROK ELLENŐRZÉSE
		$i = 0;
		while (isset($_POST['ASID'.$i])) {
			
			//ellenőrzés hogy van-e már ilyen sor
			$akcquery = mysql_query("SELECT * FROM `AkciosStand` WHERE `AkciosStand`.`AID` = ".$_POST['AID'.$i]." AND `AkciosStand`.`SID` = '".$_POST['sid']."';");
			$akcresult = mysql_fetch_assoc($akcquery);
			
			//ez a sor már létezik
			if ($_POST['ASID'.$i] != -1) {
				mysql_query("UPDATE  `".$database."`.`AkciosStand` SET  `fogyas` =  '".$_POST['aFogy'.$i]."' WHERE  `AkciosStand`.`ID` =".$_POST['ASID'.$i].";");
			
			//új sor
			} else if ($_POST['ASID'.$i] == -1) {
				
				//csak akkor adja hozzá ha még nincs ilyen sor
				if (!is_array($akcresult)) {
				
					//ár lekérése
					$query = mysql_query("SELECT * FROM `Akciok` WHERE `Akciok`.`ID` = ".$_POST['AID'.$i].";");
					$result = mysql_fetch_assoc($query);
					
					mysql_query("INSERT INTO `".$database."`.`AkciosStand` (`ID`, `DID`, `AID`, `SID`, `price`, `fogyas`) VALUES (NULL, '".$_POST['DID'.$i]."', '".$_POST['AID'.$i]."', '".$_POST['sid']."', '".$result['price']."', '".$_POST['aFogy'.$i]."');");
				}
			}
			
			$i++;
		}
		
		//Standlap feltöltése
		$standId = $_POST['nsorId'];
		$standPrice = $_POST['standPrice'];
		$standVetel = $_POST['standVetel'];
		$standDid = $_POST['ndid'];
		$standForditott = $_POST['nforditott'];
		$standFogyas = array();
		if (isset($_POST['pstandVetel'])) {
			$standPvetel = $_POST['pstandVetel'];
		}
		//var_dump($_POST['standMaradvany0']);
		
		//HA VAN PLUSZ RAKTÁR
		if (isset($_POST['pluszRaktar']) && $_POST['pluszRaktar'] == 1) {
			
			$standPraktar = explode(";", $_POST['pluszRaktarak']);
			$i = 0;
			while (isset($standId[$i])) {
				
				//válrozók nyitó összesítéshez és arraybe gyűjtéshez 
				$nyitoAll = 0;
				$standNyito = array();
				
				//arraybe gyűjtés 
				$tmpNyito = $_POST['newNyito'.$i];
				$standNyito[0] = $tmpNyito[0];
				$nyitoAll += $tmpNyito[0];
				for ($j = 0; $j < count($standPraktar)-1; $j++) {
					$standNyito[$standPraktar[$j]] = $tmpNyito[$standPraktar[$j]];
					$nyitoAll += $tmpNyito[$standPraktar[$j]];
				}
				
				//változók maradvány összesítéshez és arraybe gyűjtéshez
				$maradvanyAll = 0;
				$standMaradvany = array();
				
				//arraybe gyűjtés
				$tmpMarad = $_POST['standMaradvany'.$i];
				$standMaradvany[0] = $tmpMarad[0];
				$maradvanyAll += $tmpMarad[0];
				for ($j = 0; $j < count($standPraktar)-1; $j++) {
					$standMaradvany[$standPraktar[$j]] = $tmpMarad[$standPraktar[$j]];
					$maradvanyAll += $tmpMarad[$standPraktar[$j]];
				}
				
				//maradványok feltöltése
				foreach ($standMaradvany as $key => $value) {
					if ($key != 0) {
						
						//lekérjük van-e már ilyen sor az adatbázisban
						$query = mysql_query("SELECT * FROM  `pluszRaktarMaradvany` WHERE  `SID` =".$_POST['sid']." AND  `DID` =".$standDid[$i]." AND `PRID` =".$key.";");
						$result = mysql_fetch_assoc($query);
						
						if (is_array($result)) {
							mysql_query("UPDATE  `".$database."`.`pluszRaktarMaradvany` SET  `maradvany` =  '".$value."' WHERE  `pluszRaktarMaradvany`.`ID` =".$result['ID'].";");
							
						} else {
							mysql_query("INSERT INTO `".$database."`.`pluszRaktarMaradvany` (`ID`, `SID`, `DID`, `PRID`, `maradvany`) VALUES (NULL, '".$_POST['sid']."', '".$standDid[$i]."', '".$key."', '".$value."');");
						
						}
						
					}
					
				}
				
				//plusz vételezés
				if (isset($standPvetel[$i])) {
					
					//lekérjük van-e már az adatbázisban hozzá sor
					$query = mysql_query("SELECT * FROM  `pvetel` WHERE  `DID` =".$standDid[$i]." AND  `SID` =".$_POST['sid'].";");
					$result = mysql_fetch_assoc($query);
					
					if (is_array($result)) {
						mysql_query("UPDATE  `".$database."`.`pvetel` SET  `value` =  '".$standPvetel[$i]."' WHERE  `pvetel`.`ID` =".$result['ID'].";");
					
					} else {
						mysql_query("INSERT INTO `".$database."`.`pvetel` (`ID`, `DID`, `SID`, `value`) VALUES (NULL, '".$standDid[$i]."', '".$_POST['sid']."', '".$standPvetel[$i]."');");
					
					}
					
				}
				
				//fogyás számolása
				if ($standForditott[$i] == "0") {
					$standFogyas[$i] = ($standNyito[0]+$standVetel[$i])-$standMaradvany[0];
				} else if ($standForditott[$i] == "1") {
					$standFogyas[$i] = $standMaradvany[0]-($standNyito[0]+$standVetel[$i]);
				}
				
				//ellenőrzés hogy van-e ilyen DID-ű már az adatbázisban
				$query = mysql_query("SELECT * FROM `stand` WHERE `SID` = '".$_POST['sid']."' AND `DID` = '".$standDid[$i]."';");
				$didResult = mysql_fetch_assoc($query);
				
				//standsor módosítás
				if (is_array($didResult)) {
					mysql_query("UPDATE  `".$database."`.`stand` SET `vetel` =  '".$standVetel[$i]."', `fogyas` =  ".$standFogyas[$i]." WHERE  `stand`.`ID` =".$didResult['ID'].";");
				} else {
					mysql_query("INSERT INTO `".$database."`.`stand` (`ID`, `nyito`, `vetel`, `fogyas`, `price`, `SID`, `DID`) VALUES (NULL, '".$standNyito[0]."', '".$standVetel[$i]."', ".$standFogyas[$i].", '".$standPrice[$i]."', '".$_POST['sid']."', '".$standDid[$i]."');");
				}
				
				$i++;
			}
			
		//ALAP FELTÖLTÉS
		} else {
			
			$standNyito = $_POST['standNyito'];
			$standMaradvany = $_POST['standMaradvany'];
			$i = 0;
			while (isset($standId[$i])) {
				
				//fogyás számolása
				if ($standForditott[$i] == "0") {
					$standFogyas[$i] = ($standNyito[$i]+$standVetel[$i])-$standMaradvany[$i];
				} else if ($standForditott[$i] == "1") {
					$standFogyas[$i] = $standMaradvany[$i]-($standNyito[$i]+$standVetel[$i]);
				}
				
				//ellenőrzés hogy van-e ilyen DID-ű már az adatbázisban
				$query = mysql_query("SELECT * FROM `stand` WHERE `SID` = '".$_POST['sid']."' AND `DID` = '".$standDid[$i]."';");
				$didResult = mysql_fetch_assoc($query);
				
				//standsor módosítás
				if (is_array($didResult)) {
					mysql_query("UPDATE  `".$database."`.`stand` SET `vetel` =  '".$standVetel[$i]."', `fogyas` =  ".$standFogyas[$i]." WHERE  `stand`.`ID` =".$didResult['ID'].";");
				} else {
					mysql_query("INSERT INTO `".$database."`.`stand` (`ID`, `nyito`, `vetel`, `fogyas`, `price`, `SID`, `DID`) VALUES (NULL, '".$standNyito[$i]."', '".$standVetel[$i]."', ".$standFogyas[$i].", '".$standPrice[$i]."', '".$_POST['sid']."', '".$standDid[$i]."');");
				}
				
				//echo mysql_error()." | ".$standId[$i]." ; ".$standNyito[$i]." ; ".$standVetel[$i]." ; ".$standMaradvany[$i]." ; ".$standFogyas[$i]." ; ".$standDid[$i]."<br />";
				$i++;
			}
			
		}
		
		//MÓDOSÍTHATÓ SOROK FELTÖLTÉSE
		$i = 0;
		while (isset($_POST['modSorID'.$i])) {
			
			//ellenőrzi van-e ilyen nevű sor az adatbázisban
			$modquery = mysql_query("SELECT * FROM `modStandSor` WHERE `modStandSor`.`name` = '".$_POST['modSorNev'.$i]."' AND `modStandSor`.`SID` = '".$_POST['sid']."';");
			$modSorResult = mysql_fetch_assoc($modquery);
			
			//ha még nincs feltöltve és előző standról jött
			if ($_POST['modSorSID'.$i] != $_POST['sid'] && $_POST['modSorSID'.$i] != -1 && $_POST['modSorDel'.$i] == 0) {
				
				//akkor tölti fel ha nincs ilyen
				if (!is_array($modSorResult)) {
					mysql_query("INSERT INTO `".$database."`.`modStandSor` (`ID`, `name`, `nyito`, `vetel`, `maradvany`, `ar`, `lathatosag`, `SID`) VALUES (NULL, '".$_POST['modSorNev'.$i]."', '".$_POST['modSorNyito'.$i]."', '".$_POST['modSorVetel'.$i]."', '".$_POST['modSorMarad'.$i]."', '".$_POST['modSorAr'.$i]."', '1', '".$_POST['sid']."');");
					mysql_query("UPDATE `".$database."`.`modStandSor` SET `lathatosag` = '0' WHERE `modStandSor`.`ID` = ".$_POST['modSorID'.$i].";");
				}
				
			//ha még nincs feltöltve és új sor
			} else if ($_POST['modSorSID'.$i] != $_POST['sid'] && $_POST['modSorSID'.$i] == -1 && $_POST['modSorNev'.$i] != "" && $_POST['modSorDel'.$i] == 0) {
				
				//akkor tölti fel ha nincs ilyen
				if (!is_array($modSorResult)) {
					mysql_query("INSERT INTO `".$database."`.`modStandSor` (`ID`, `name`, `nyito`, `vetel`, `maradvany`, `ar`, `lathatosag`, `SID`) VALUES (NULL, '".$_POST['modSorNev'.$i]."', '".$_POST['modSorNyito'.$i]."', '".$_POST['modSorVetel'.$i]."', '".$_POST['modSorMarad'.$i]."', '".$_POST['modSorAr'.$i]."', '1', '".$_POST['sid']."');");
				}
			
			//ha már fel van töltve
			} else if ($_POST['modSorSID'.$i] == $_POST['sid'] && $_POST['modSorDel'.$i] == 0) {
				mysql_query("UPDATE `".$database."`.`modStandSor` SET `name` = '".$_POST['modSorNev'.$i]."', `vetel` = '".$_POST['modSorVetel'.$i]."', `maradvany` = '".$_POST['modSorMarad'.$i]."', `ar` = '".$_POST['modSorAr'.$i]."' WHERE `modStandSor`.`ID` = ".$_POST['modSorID'.$i].";");
			}
			
			$i++;
		}
		
		//LOTTÓ FELTÖLTÉS
		if ($_SESSION['pub']->isLotto == 1) {
		
			//post->array
			$lid = $_POST['lid'];
			$LotNet = $_POST['LotNet'];
			$LotAll = $_POST['LotAll'];
			
			//feltöltés
			for ($i = 0; $i < count($LotNet); $i++) {
				
				//Ha módosítjuk a standlapot
				if (isset($lid[$i]) && $lid[$i] != -1) {
				
					mysql_query("UPDATE  `".$database."`.`LottoStand` SET  `NetForg` =  '".$LotNet[$i]."', `Ossz` =  '".$LotAll[$i]."' WHERE  `LottoStand`.`ID` =".$lid[$i].";");
					
				//Ha új standlapot mentünk
				} else {
					
					//ellenőrizzük a sor be került -e már véletlen!
					$query = mysql_query("SELECT * FROM  `LottoStand` WHERE  `Nap` = ".($i+1)." AND  `SID` = ".$_POST['sid'].";");
					$result = mysql_fetch_assoc($query);
					
					//ha nincs még úúj sor
					if (!$result) {
						mysql_query("INSERT INTO `".$database."`.`LottoStand` (`ID`, `NetForg`, `Ossz`, `Nap`, `SID`) VALUES (NULL, '".$LotNet[$i]."', '".$LotAll[$i]."', ".($i+1).", '".$_POST['sid']."');");
						
					//ha van már frissít
					} else {
						mysql_query("UPDATE  `".$database."`.`LottoStand` SET  `NetForg` =  '".$LotNet[$i]."', `Ossz` =  '".$LotAll[$i]."' WHERE  `LottoStand`.`ID` =".$result['ID'].";");
					}
					
					
				}
				
			}
			
		}
		
		
		//KIADÁS FELTÖLTÉS
		//ÚJ BEVÉTELES KIADÁS RÉSZ
		if (isset($_POST['bevetelNev'])) {
			
			//post->array
			$bid = $_POST['bid'];
			$bevetelNev = $_POST['bevetelNev'];
			$bevetelAr = $_POST['bevetelAr'];
			
			//bejárás
			$i = 0;
			while (isset($bevetelNev[$i])) {
				
				//Ha ki van töltve
				if ($bevetelNev[$i] != "" && $bevetelAr[$i] != "") {
					
					//ellenőrzés van-e ugyanilyen nevű
					$bevetelQuery = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`nev` = '".$bevetelNev[$i]."' AND `kiadasok`.`SID` = '".$_POST['sid']."';");
					$bevetelResult = mysql_fetch_assoc($bevetelQuery);
					
					//frissítés
					if (is_array($bevetelResult)) {
						mysql_query("UPDATE  `".$database."`.`kiadasok` SET  `nev` =  '".$bevetelNev[$i]."', `ertek` =  '".$bevetelAr[$i]."' WHERE  `kiadasok`.`ID` =".$bevetelResult['ID'].";");
					} else {
						mysql_query("INSERT INTO `".$database."`.`kiadasok` (`ID`, `nev`, `ertek`, `SID`) VALUES (NULL, '".$bevetelNev[$i]."', '".$bevetelAr[$i]."', '".$_POST['sid']."');");
					}
				
				}
				
				$i++;
			}
			
			//post->array
			$kid = $_POST['kid'];
			$kiadasNev = $_POST['kiadasNev'];
			$kiadasAr = $_POST['kiadasAr'];
			if (is_array($kiadasNev)) {
				$kiadasKeys = array_keys($kiadasNev);
			}
			
			//Módosítás bejárás
			$i = 0;
			while($i < count($kiadasNev)) {
				
				//ellenőrzés van-e ugyanilyen nevű
				$kiadasQuery = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`nev` = '".$kiadasNev[$kiadasKeys[$i]]."' AND `kiadasok`.`SID` = '".$_POST['sid']."';");
				$kiadasResult = mysql_fetch_assoc($kiadasQuery);
				
				//módosítás
				if (isset($kid[$kiadasKeys[$i]]) && $kid[$kiadasKeys[$i]] != -1) {
					mysql_query("UPDATE  `".$database."`.`kiadasok` SET  `nev` =  '".$kiadasNev[$kiadasKeys[$i]]."', `ertek` =  '".($kiadasAr[$kiadasKeys[$i]]*-1)."' WHERE  `kiadasok`.`ID` =".$kid[$kiadasKeys[$i]].";");
				
				//új sor
				} else if (isset($kid[$kiadasKeys[$i]]) && $kid[$kiadasKeys[$i]] == -1 && isset($kiadasNev[$kiadasKeys[$i]]) && $kiadasNev[$kiadasKeys[$i]] != "") {
					
					//ha van már ilyen nem tölti fel újra
					if (!is_array($kiadasResult)) {
						mysql_query("INSERT INTO `".$database."`.`kiadasok` (`ID`, `nev`, `ertek`, `SID`) VALUES (NULL, '".$kiadasNev[$kiadasKeys[$i]]."', '".($kiadasAr[$kiadasKeys[$i]]*-1)."', '".$_POST['sid']."');");
					}
				}
				
				$i++;
			}
			
		//RÉGI BEVÉTELES RENDSZER
		} else {
			
			//post->array
			$kid = $_POST['kid'];
			$kiadasNev = $_POST['kiadasNev'];
			$kiadasAr = $_POST['kiadasAr'];
			if (is_array($kiadasNev)) {
				$kiadasKeys = array_keys($kiadasNev);
			}
			
			//Módosítás bejárás
			$i = 0;
			while($i < count($kiadasNev)) {
				
				//ellenőrzés van-e ugyanilyen nevű
				$kiadasQuery = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`nev` = '".$kiadasNev[$kiadasKeys[$i]]."' AND `kiadasok`.`SID` = '".$_POST['sid']."';");
				$kiadasResult = mysql_fetch_assoc($kiadasQuery);
				
				//módosítás
				if (isset($kid[$kiadasKeys[$i]]) && $kid[$kiadasKeys[$i]] != -1) {
					mysql_query("UPDATE  `".$database."`.`kiadasok` SET  `nev` =  '".$kiadasNev[$kiadasKeys[$i]]."', `ertek` =  '".$kiadasAr[$kiadasKeys[$i]]."' WHERE  `kiadasok`.`ID` =".$kid[$kiadasKeys[$i]].";");
				
				//új sor
				} else if (isset($kid[$kiadasKeys[$i]]) && $kid[$kiadasKeys[$i]] == -1 && isset($kiadasNev[$kiadasKeys[$i]]) && $kiadasNev[$kiadasKeys[$i]] != "") {
					
					//ha van már ilyen nem tölti fel újra
					if (!is_array($kiadasResult)) {
						mysql_query("INSERT INTO `".$database."`.`kiadasok` (`ID`, `nev`, `ertek`, `SID`) VALUES (NULL, '".$kiadasNev[$kiadasKeys[$i]]."', '".$kiadasAr[$kiadasKeys[$i]]."', '".$_POST['sid']."');");
					}
				}
				
				$i++;
			}
		}
		
		//SAJÁT FOGYASZTÁS FELTÖLTÉS
		if (isset($_POST['sajatFogyasztas0'])) {
			
			//bejárjuk a postot és egyesével feltöltjük
			$i = 0;
			while (isset($_POST['sajatFogyasztas'.$i])) {
				
				//ellenőrizzük van-e már ilyen az adatbázisban
				$squery = mysql_query("SELECT * FROM  `sajatFogyasztas` WHERE  `SID` =".$_POST['sid']." AND  `NEVID` =".$_POST['sajatFogyasztasID'.$i].";");
				$sresult = mysql_fetch_assoc($squery);
				
				//módosítani kell
				if (is_array($sresult)) {
					mysql_query("UPDATE  `".$database."`.`sajatFogyasztas` SET  `value` =  '".$_POST['sajatFogyasztas'.$i]."' WHERE  `sajatFogyasztas`.`ID` =".$sresult['ID'].";");
					
				//új sor
				} else {
					mysql_query("INSERT INTO `".$database."`.`sajatFogyasztas` (`ID`, `SID`, `NEVID`, `value`) VALUES (NULL, ".$_POST['sid'].", '".$_POST['sajatFogyasztasID'.$i]."', '".$_POST['sajatFogyasztas'.$i]."');");
					
				}
				$i++;
				
			}
			
			
		}
		
		//Egyéb forgalom feltöltése
		if (isset($_POST['egyebForgalom0'])) {
			
			//bejárjuk a postot és egyesével feltöltjük
			$i = 0;
			while (isset($_POST['egyebForgalom'.$i])) {
				
				//ellenőrizzük van-e már ilyen az adatbázisban
				$squery = mysql_query("SELECT * FROM  `egyebForgalom` WHERE  `SID` =".$_POST['sid']." AND  `EID` =".$_POST['egyebForgalomID'.$i].";");
				$sresult = mysql_fetch_assoc($squery);
				
				//módosítani kell
				if (is_array($sresult)) {
					mysql_query("UPDATE  `".$database."`.`egyebForgalom` SET  `value` =  '".$_POST['egyebForgalom'.$i]."' WHERE  `egyebForgalom`.`ID` =".$sresult['ID'].";");
					
				//új sor
				} else {
					mysql_query("INSERT INTO `".$database."`.`egyebForgalom` (`ID`, `EID`, `SID`, `value`) VALUES (NULL, ".$_POST['egyebForgalomID'.$i].", '".$_POST['sid']."', '".$_POST['egyebForgalom'.$i]."');");
					
				}
				$i++;
				
			}
			
			
		}
		
		//ételforgalom mentése
		if (isset($_POST['etelforgalom0'])) {
			
			//bejárjuk a postot és egyesével feltöltjük
			$i = 0;
			while (isset($_POST['etelforgalom'.$i])) {
				
				//ellenőrizzük van-e már ilyen az adatbázisban
				$equery = mysql_query("SELECT * FROM  `etelFogyasztas` WHERE  `nap` =  '".$_POST['etelforgalomdate'.$i]."' AND  `SID` =".$_POST['sid'].";");
				$eresult = mysql_fetch_assoc($equery);
				
				//módosítani kell
				if (is_array($eresult)) {
					mysql_query("UPDATE  `".$database."`.`etelFogyasztas` SET  `fogyas` =  '".$_POST['etelforgalom'.$i]."' WHERE  `etelFogyasztas`.`ID` =".$eresult['ID'].";");
					
				//új sor
				} else {
					mysql_query("INSERT INTO `".$database."`.`etelFogyasztas` (`ID`, `nap`, `fogyas`, `SID`) VALUES (NULL, '".$_POST['etelforgalomdate'.$i]."', '".$_POST['etelforgalom'.$i]."', '".$_POST['sid']."');");
					
				}
				
				$i++;
			}
			
		}
		
		//bankkártya mentése
		if (isset($_POST['kartyaForgalom'])) {
			
			//lekérés
			$bquery = mysql_query("SELECT * FROM  `bankkartyasFizetes` WHERE  `SID` =".$_POST['sid'].";");
			$bresult = mysql_fetch_assoc($bquery);
			
			//frissít
			if (is_array($bresult)) {
				mysql_query("UPDATE  `".$database."`.`bankkartyasFizetes` SET  `value` =  '".$_POST['kartyaForgalom']."' WHERE  `bankkartyasFizetes`.`ID` =".$bresult['ID'].";");
				
			//beilleszt
			} else {
				mysql_query("INSERT INTO `".$database."`.`bankkartyasFizetes` (`ID`, `SID`, `value`) VALUES (NULL, '".$_POST['sid']."', '".$_POST['kartyaForgalom']."');");
				
			}
			
		}
		
		
		//CHATBOX: ellenőrzés van -e már rekord
		$chatQuery = mysql_query("SELECT * FROM `messages` WHERE `messages`.`SID` = '".$_POST['sid']."';");
		$chatResult = mysql_fetch_assoc($chatQuery);
		
		//chatbox új feltöltés
		if ($_POST['chatboxID'] == -1) {
			
			//csak akkor fut le ha nics még ilyen sor
			if (!is_array($chatResult)) {
				mysql_query("INSERT INTO `".$database."`.`messages` (`ID`, `SID`, `message`) VALUES (NULL, '".$_POST['sid']."', '".$_POST['chatboxText']."');");
			}
			
		//chatbox módosítás
		} else if (is_numeric($_POST['chatboxID']) && $_POST['chatboxID'] != -1) {
			mysql_query("UPDATE `".$database."`.`messages` SET `message` = '".$_POST['chatboxText']."' WHERE `messages`.`ID` = ".$_POST['chatboxID'].";");
			
		}
		
		//BORRAVALÓ: ellenőrzés van-e már rekord
		$borravaloQuery = mysql_query("SELECT * FROM `borravalo` WHERE `borravalo`.`SID` = '".$_POST['sid']."';");
		$borravaloResult = mysql_fetch_assoc($borravaloQuery);
		
		//borravaló feltöltése
		if ($_POST['bid'] == -1) {
			
			//csak akkor fut le ha nincs még ilyen sor
			if (!is_array($borravaloResult)) {
				mysql_query("INSERT INTO `".$database."`.`borravalo` (`ID`, `ar`, `SID`) VALUES (NULL, '".$_POST['borravalo']."', '".$_POST['sid']."');");
			}
			
		} else {
			mysql_query("UPDATE  `".$database."`.`borravalo` SET  `ar` =  '".$_POST['borravalo']."' WHERE  `borravalo`.`ID` =".$_POST['bid'].";");
		}
		
		//ELLENŐRIZVE
		if (isset($_POST['standEllenorizve']) && $_POST['standEllenorizve'] == 1) {
			$equery = mysql_query("SELECT * FROM `standEllenorizve` WHERE `standEllenorizve`.`SID` =".$_POST['sid'].";");
			$eresult = mysql_fetch_assoc($equery);
			
			if (!is_array($eresult)) {
				
				mysql_query("INSERT INTO `".$database."`.`standEllenorizve` (`ID`, `SID`, `value`) VALUES (NULL, '".$_POST['sid']."', '1');");
			}
			
		} 
		
		//stand fejléc módosítása
		mysql_query("UPDATE  `$database`.`standok` SET `finished` = '1', `forgalom` = '".$_POST['forgalom2save']."'  WHERE  `standok`.`ID` =".$_POST['sid'].";");
		
		//lezárás időpontjának megadása
		//Lekéri van-e már ehhez a standlaphoz adat
		$query = mysql_query("SELECT * FROM `standLezarasIdopont` WHERE `sid` = ".$_POST['sid'].";");
		$result = mysql_fetch_assoc($query);
		
		//ha nincs standlap akkor beinzertálja
		if (!isset($result['ID'])) {
			mysql_query("INSERT INTO `".$database."`.`standLezarasIdopont` (`ID`, `sid`, `pid`, `idopont`) VALUES (NULL, '".$_POST['sid']."', '".$_SESSION['pub']->ID."', CURRENT_TIMESTAMP);");
		}
		
		//kapcsolat bezárás
		mysql_close($kapcsolat);
		
		echo "<b>Sikeres feltöltés!</b><br /><br />".menu(3);
		
	}
	
	//STAND TÖRLÉSE
	function standTorles() {
		echo "
			<script type='text/javascript'>
				standTorlesClient(".$_GET['id'].");
			</script>
		";
	}
?>
