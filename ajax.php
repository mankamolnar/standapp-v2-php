<?php
	include("conf/mysql.php");
		
	//MYSQL SERVER
	$kapcsolat = mysql_connect($szerver, $user, $pass);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
	
	//FELTÖLTÉS úgy hogy már van standlap
	if ($_GET['page'] == 1) {
		
		//redundancia keresése
		mysql_query("
			DELETE FROM `$database`.`stand`
			WHERE `stand`.`DID`= ".$_GET['did']." AND `stand`.`SID` = ".$_GET['sid'].";
		");
		
		//$_GET['f'] generálása ha nem null
		if ($_GET['f'] != "NULL") {
			$_GET['f'] = "'".$_GET['f']."'";
		}
		
		//feltöltés
		$query = "INSERT INTO `".$database."`.`stand` (`ID`, `nyito`, `vetel`, `fogyas`, `price`, `SID`, `DID`) VALUES (NULL, '".$_GET['ny']."', '".$_GET['v']."', ".$_GET['f'].", '".$_GET['p']."', '".$_GET['sid']."', '".$_GET['did']."');";
		mysql_query($query);
		$lastid = mysql_insert_id();
		
		//adat lekérés
		$query = mysql_query("SELECT * FROM `".$database."`.`stand` WHERE `stand`.`ID` = $lastid;");
		$result = mysql_fetch_assoc($query);
		echo $result['ID'].";".$result['vetel'].";".$result['fogyas'];
		
	//DELETED
	} else if ($_GET['page'] == 2) {
	
	
	
	//sor frissítés
	} else if ($_GET['page'] == 3) {
		
		//$_GET['f'] generálása ha nem null
		if ($_GET['f'] != "NULL") {
			$_GET['f'] = "'".$_GET['f']."'";
		}
		
		mysql_query("UPDATE  `".$database."`.`stand` SET `vetel` =  '".$_GET['v']."', `fogyas` =  ".$_GET['f']." WHERE  `stand`.`ID` =".$_GET['id'].";");
		
		//adat lekérés
		$query = mysql_query("SELECT * FROM `".$database."`.`stand` WHERE `stand`.`ID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		echo $result['ID'].";".$result['vetel'].";".$result['fogyas'];
	
	//egyszerű válasz
	} else if ($_GET['page'] == 4) {
		echo "1";
	
	//Mai dátum
	} else if ($_GET['page'] == 5) {
		echo date("Y-m-d");
	
	//standlap kereső lekérdezés
	} else if ($_GET['page'] == 6) {
		
		//LEKÉRDEZÉS
		$query = mysql_query("
			SELECT `standok`.* , `user`.`uname` as 'name', `standEllenorizve`.`value`
			FROM  `standok`
			
			LEFT JOIN `user`
			ON `user`.`ID` = `standok`.`UID`
			
			LEFT JOIN `standEllenorizve`
			ON `standEllenorizve`.`SID` = `standok`.`ID`
			
			WHERE  `date` 
			BETWEEN '".$_GET['tol']."' 
			AND '".$_GET['ig']."' 
			AND `PID` = ".$_GET['pid']."
			ORDER BY `date`, `standok`.`ID` ASC;
		");
		
		//Sorosítás
		$serial = "";
		while ($result = mysql_fetch_assoc($query)) {
			$serial = $serial.$result['ID'].",".$result['date'].",".$result['wtime'].",".$result['name'].",".$result['value'].";";
		}
		
		echo $serial;
	
	//új italsor felvitel 
	} else if ($_GET['page'] == 7) {
		
		//list.id++ in mysql becouse the new drinks list.id is 1
		mysql_query("UPDATE `drinks` SET `List.ID` = `List.ID`+1 WHERE `drinks`.`PID` = ".$_GET['pid']." ;");
		
		//FELTÖLTÉS
		$query = mysql_query("INSERT INTO `".$database."`.`drinks` (`ID`, `PID`, `CSID`, `MID`, `List.ID`, `name`, `price`, `purchase_price`, `forditott`, `visible`) VALUES (NULL, '".$_GET['pid']."', '".$_GET['csid']."', '".$_GET['mid']."', '1', '".$_GET['name']."', '".$_GET['price']."', '".$_GET['pprice']."', '".$_GET['forditott']."', '1');");
	
	//kapcsolótábla user kocsmákkal való kapcsolatának lekérése
	} else if ($_GET['page'] == 8) {
		$query = "SELECT * FROM `UsPuKapcs` WHERE `PID`=".$_POST['pub']." AND `UID`=".$_POST['user'];
		$result = mysql_query($query);
		if(mysql_num_rows($result)) echo 'van';
		else echo 'nincs';

	//kapcsolótábla actionok aka hozzáadás/eltávolítás
	} else if($_GET['page'] == 9) {
		if($_POST['action'] == 'add') $query = 'INSERT INTO `UsPuKapcs` (`PID`,`UID`) VALUES ('.$_POST['pid'].','.$_POST['uid'].')';
		if($_POST['action'] == 'remove') $query = 'DELETE FROM `UsPuKapcs` WHERE `PID`='.$_POST['pid'].' AND `UID`='.$_POST['uid'] ;
		if(mysql_query($query)) echo 'success';
		else echo 'error';

	//stand törlése
	} else if($_GET['page'] == 10) {
		$query = "DELETE FROM `standok` WHERE `ID`=".$_POST['SID'];
		if(mysql_query($query)) {
			$query = "DELETE FROM `stand` WHERE `SID`=".$_POST['SID'];
			if(mysql_query($query)) {
				$query = "DELETE FROM `LottoStand` WHERE `SID`=".$_POST['SID'];
				if(mysql_query($query)) {
					$query = "DELETE FROM `kiadasok` WHERE `SID`=".$_POST['SID'];
					$result = mysql_query($query);
					if($result) {
						/*if(mysql_num_rows($result) > 0) {
							$query = "DELETE FROM `kiadasok` WHERE `SID`=".$_POST['SID'];
							echo '5';
							if(mysql_query($query)) echo 'success';
						}
						else*/
						echo 'success';
					}
				}
			}
		}
	
	//DELETED
	} else if ($_GET['page'] == 11) {
		
		
		
	//Akciós ital feltöltés
	} else if ($_GET['page'] == 12) {
		
		//feltöltés
		mysql_query("INSERT INTO `".$database."`.`AkciosStand` (`ID`, `DID`, `AID`, `SID`, `price`, `fogyas`) VALUES (NULL, '".$_GET['did']."', '".$_GET['aid']."', '".$_GET['sid']."', '".$_GET['price']."', '".$_GET['fogyas']."');");
		$lastid = mysql_insert_id();
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `AkciosStand` WHERE `AkciosStand`.`ID` = $lastid;");
		$result = mysql_fetch_assoc($query);
		
		echo $lastid.";".$result['fogyas'];
		
		
	//Akciós ital sor frissítés
	} else if ($_GET['page'] == 13) {
	
		mysql_query("UPDATE  `".$database."`.`AkciosStand` SET  `fogyas` =  '".$_GET['fogyas']."' WHERE  `AkciosStand`.`ID` =".$_GET['id'].";");
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `AkciosStand` WHERE `AkciosStand`.`ID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		
		echo $result['ID'].";".$result['fogyas'];
		
	//update saled product price
	} else if ($_GET['page'] == 14) {
		
		mysql_query("UPDATE  `".$database."`.`Akciok` SET  `price` =  '".$_GET['price']."' WHERE  `Akciok`.`ID` =".$_GET['id'].";");
	
	//módosítható sor feltöltése
	} else if ($_GET['page'] == 15) {
		
		//adat feltöltés és előző láthatósága 0
		mysql_query("INSERT INTO `".$database."`.`modStandSor` (`ID`, `name`, `nyito`, `vetel`, `maradvany`, `ar`, `lathatosag`, `SID`) VALUES (NULL, '".$_GET['name']."', '".$_GET['nyito']."', '".$_GET['vetel']."', '".$_GET['maradvany']."', '".$_GET['ar']."', '1', '".$_GET['sid']."');");
		$insertid = mysql_insert_id();
		mysql_query("UPDATE `".$database."`.`modStandSor` SET `lathatosag` = '0' WHERE `modStandSor`.`ID` = ".$_GET['id'].";");
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `modStandSor` WHERE `modStandSor`.`ID` = $insertid;");
		$result = mysql_fetch_assoc($query);
		
		echo $insertid.";".$result['vetel'].";".$result['maradvany'];
	
	//sor frissítés
	} else if ($_GET['page'] == 16) {
		
		mysql_query("UPDATE `".$database."`.`modStandSor` SET `name` = '".$_GET['name']."', `vetel` = '".$_GET['vetel']."', `maradvany` = '".$_GET['maradvany']."', `ar` = '".$_GET['ar']."' WHERE `modStandSor`.`ID` = ".$_GET['id'].";");
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `modStandSor` WHERE `modStandSor`.`ID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		
		echo $result['ID'].";".$result['vetel'].";".$result['maradvany'];
		
	//chatbox sor feltöltés
	} else if ($_GET['page'] == 17) {
		
		mysql_query("INSERT INTO `".$database."`.`messages` (`ID`, `SID`, `message`) VALUES (NULL, '".$_GET['sid']."', '".$_GET['value']."');");
		echo mysql_insert_id();
		
	//chatbox sor frissítés
	} else if ($_GET['page'] == 18) {
		
		mysql_query("UPDATE `".$database."`.`messages` SET `message` = '".$_GET['value']."' WHERE `messages`.`ID` = ".$_GET['id'].";");
		
	//dashboard lekérés
	} else if ($_GET['page'] == 19) {
		
		//ddates
		$honap = $_GET['year']."-".$_GET['month']."-01";
		$veghonap = date("Y-m-t", strtotime($honap));
		
		//kocsma beállításainak betöltése
		$pubOptions = array();
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_GET['pid'].";");
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result['value'];
		}
		
		//mysql queries
		$query = mysql_query("
			SELECT count(*) as `standokSzama` 
			FROM `standok` 
			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."' 
			AND `standok`.`PID` = ".$_GET['pid']."
			AND `standok`.`finished` = 1;");
		$standokSzama = mysql_fetch_assoc($query);
		
		//HA PLUSZ RAKTÁROS
		if (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 1) {
			
			$tmpMaradvanyok = array();
			
			//legutóbbi standlap
			$sorok = array();
			$forgalom = array();
			$forgalom['forgalom'] = 0;
			$query = mysql_query("SELECT SUM(`forgalom`) FROM `standok` WHERE `PID` = ".$_GET['pid']." AND `date` BETWEEN '".$honap."' AND '".$veghonap."';");
			$forgalom = mysql_fetch_assoc($query);
			
			
		//HA NINCS PLUSZ RAKTÁR
		} else {
			
			//calculate forgalom
			$query = mysql_query("
				SELECT
				sum(`stand`.`fogyas` * `stand`.`price`) as `forgalom`
				FROM `standok` 
				LEFT JOIN `stand` ON `standok`.`ID` = `stand`.`SID`
				WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."' 
				AND `standok`.`PID` = ".$_GET['pid'].";
			");
			$forgalom = mysql_fetch_assoc($query);
			
		}
		
		//akcios stand fogyás
		$query = mysql_query("
			SELECT
			sum(`AkciosStand`.`fogyas` * `AkciosStand`.`price`) as `forgalom`
			FROM `standok` 
			LEFT JOIN `AkciosStand` ON `standok`.`ID` = `AkciosStand`.`SID`
			WHERE `standok`.`date` BETWEEN '$honap' AND '".date("Y-m-t", strtotime($honap))."' 
			AND `standok`.`PID` = ".$_GET['pid'].";
		");
		$akcios = mysql_fetch_assoc($query);
		$forgalom['forgalom'] += intval($akcios['forgalom']);
		
		//módosítható stand fogyás
		$query = mysql_query("
			SELECT
			sum(`modStandSor`.`ar` * (`modStandSor`.`nyito` + `modStandSor`.`vetel` - `modStandSor`.`maradvany`) ) as `forgalom`
			FROM `standok` 
			LEFT JOIN `modStandSor` ON `standok`.`ID` = `modStandSor`.`SID`
			WHERE `standok`.`date` BETWEEN '$honap' AND '".date("Y-m-t", strtotime($honap))."' 
			AND `standok`.`PID` = ".$_GET['pid'].";
		");
		$modsor = mysql_fetch_assoc($query);
		$forgalom['forgalom'] += intval($modsor['forgalom']);
		
		//Jutalék van-e
		if (isset($pubOptions['jutalek']) && $pubOptions['jutalek'] != 0) {
			$forgalom['forgalom'] = round($forgalom['forgalom']-(($forgalom['forgalom'] / 100) * $pubOptions['jutalek']));
		}
		
		//ételforgalom
		if (isset($pubOptions['etelFogyasztas']) && $pubOptions['etelFogyasztas'] == 1) {
			$equery = mysql_query("
			SELECT sum(`etelFogyasztas`.`fogyas`) as `etelfogy` FROM `standok`

			LEFT JOIN `etelFogyasztas`
			ON `etelFogyasztas`.`SID` = `standok`.`ID`

			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."'  
			AND `standok`.`PID` = ".$_GET['pid'].";");
			$etelfogy = mysql_fetch_assoc($equery);
			
			$forgalom['forgalom'] += $etelfogy['etelfogy'];
		}
		
		//egyéb forgalom
		if (isset($pubOptions['egyebForgalom']) && $pubOptions['egyebForgalom'] == 1) {
			$equery = mysql_query("
			SELECT sum(`egyebForgalom`.`value`) as `egyebfogy` FROM `standok`

			LEFT JOIN `egyebForgalom`
			ON `egyebForgalom`.`SID` = `standok`.`ID`

			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."'  
			AND `standok`.`PID` = ".$_GET['pid'].";");
			$egyebfogy = mysql_fetch_assoc($equery);
			
			$forgalom['forgalom'] += $egyebfogy['egyebfogy'];
		}
		
		//saját forgalom
		if (isset($pubOptions['sajatFogyasztas']) && $pubOptions['sajatFogyasztas'] == 1) {
			$squery = mysql_query("
			SELECT sum(`sajatFogyasztas`.`value`) as `sajatfogy` FROM `standok`

			LEFT JOIN `sajatFogyasztas`
			ON `sajatFogyasztas`.`SID` = `standok`.`ID`

			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."'  
			AND `standok`.`PID` = ".$_GET['pid'].";");
			$sajatfogy = mysql_fetch_assoc($squery);
			
			$forgalom['forgalom'] -= $sajatfogy['sajatfogy'];
		}
		
		//calculate kiadasok
		$query = mysql_query("
			SELECT
			sum(`kiadasok`.`ertek`) as `ertek`
			FROM `standok` 
			LEFT JOIN `kiadasok` ON `standok`.`ID` = `kiadasok`.`SID`
			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."' 
			AND `standok`.`PID` = ".$_GET['pid'].";
		");
		$kiadasok = mysql_fetch_assoc($query);
		
		//bankkártyás fizetések
		$bankkartyas = array();
		$bankkartyas['ertek'] = 0;
		if (isset($pubOptions['bankkartya']) && $pubOptions['bankkartya'] == 1) {
			$query = mysql_query("
				SELECT
				sum(`bankkartyasFizetes`.`value`) as `ertek`
				FROM `standok` 
				LEFT JOIN `bankkartyasFizetes` ON `standok`.`ID` = `bankkartyasFizetes`.`SID`
				WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."' 
				AND `standok`.`PID` = ".$_GET['pid'].";
			");
			$bankkartyas = mysql_fetch_assoc($query);
		}
		
		//calculate lotto 1%
		$query = mysql_query("
			SELECT
			sum(`LottoStand`.`NetForg`) / 100 as `1sz`
			FROM `standok` 
			LEFT JOIN `LottoStand` ON `standok`.`ID` = `LottoStand`.`SID`
			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."' 
			AND `standok`.`PID` = ".$_GET['pid'].";
		");
		$lotto = mysql_fetch_assoc($query);
		$lotto['1sz'] = floor($lotto['1sz']);
		
		//e havi fizetések
		$query = mysql_query("
			SELECT
			sum(`pub`.`dfee` * `standok`.`wtime`) as `salary`

			FROM `user`
			LEFT JOIN `UsPuKapcs` ON `UsPuKapcs`.`UID` = `user`.`ID`
			LEFT JOIN `standok` ON `standok`.`UID` = `user`.`ID` AND `standok`.`PID` = `UsPuKapcs`.`PID`
			LEFT JOIN `pub` ON `pub`.`ID` = `UsPuKapcs`.`PID`

			WHERE `standok`.`date` BETWEEN '".$honap."' AND '".$veghonap."'
			AND `standok`.`finished` = 1
			AND `standok`.`PID` = ".$_GET['pid'].";");
			
		$salary = mysql_fetch_assoc($query);
		
		//RETURN
		echo $standokSzama['standokSzama'].";".$forgalom['forgalom'].";".$kiadasok['ertek'].";".($forgalom['forgalom']+$kiadasok['ertek']-$bankkartyas["ertek"]-$lotto['1sz']).";".$salary["salary"];
	
	//modsor 
	} else if ($_GET['page'] == 20) {
		
		//e havi fizetések
		mysql_query("UPDATE `".$database."`.`modStandSor` SET `lathatosag` = '0' WHERE `modStandSor`.`ID` = ".$_GET['id'].";");	
		
	//kiadas ÚJ 
	} else if ($_GET['page'] == 21) {
		
		//e havi fizetések
		mysql_query("INSERT INTO `".$database."`.`kiadasok` (`ID`, `nev`, `ertek`, `SID`) VALUES (NULL, '".$_GET['nev']."', '".$_GET['ar']."', '".$_GET['sid']."');");
		$lastid = mysql_insert_id();
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`ID` = $lastid;");
		$result = mysql_fetch_assoc($query);
		
		echo $result['ID'].";".$result['nev'].";".$result['ertek'];
	
	//modol
	} else if ($_GET['page'] == 22) {
		
		//e havi fizetések
		mysql_query("UPDATE `".$database."`.`kiadasok` SET `nev` = '".$_GET['nev']."', `ertek` = '".$_GET['ar']."' WHERE `kiadasok`.`ID` = ".$_GET['id'].";");
		
		//ellenőrzés
		$query = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`ID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		
		echo $result['ID'].";".$result['nev'].";".$result['ertek'];
		
	//akcios stand ellenőrzés
	} else if ($_GET['page'] == 23) {
		
		$query = mysql_query("
			SELECT *
			FROM `AkciosStand`
			WHERE `AkciosStand`.`ID` = ".$_GET['id'].";
		");
		$result = mysql_fetch_assoc($query);
		echo $result['fogyas'];
	
	//módosíthatü sorok ellenőrzése
	} else if ($_GET['page'] == 24) {
		$query = mysql_query("
			SELECT *
			FROM `modStandSor`
			WHERE `modStandSor`.`ID` = ".$_GET['id'].";
		");
		$result = mysql_fetch_assoc($query);
		echo $result['vetel'].";".$result['maradvany'];
	
	//kiadások értékének visszadobása
	} else if ($_GET['page'] == 25) {
		
		$query = mysql_query("
			SELECT *
			FROM `kiadasok`
			WHERE `kiadasok`.`ID` = ".$_GET['id'].";
		");
		$result = mysql_fetch_assoc($query);
		echo $result['nev'].";".$result['ertek'];
	
	//Új borravaló felvitele
	} else if ($_GET['page'] == 26) {
		
		mysql_query("INSERT INTO `".$database."`.`borravalo` (`ID`, `ar`, `SID`) VALUES (NULL, '".$_GET['value']."', '".$_GET['sid']."');");
		$bid = mysql_insert_id();
		$query = mysql_query("SELECT * FROM `borravalo` WHERE `borravalo`.`ID` = $bid;");
		$result = mysql_fetch_assoc($query);
		
		echo $bid.";".$result['ar'];
		
		
	} else if ($_GET['page'] == 27) {
		
		mysql_query("UPDATE  `".$database."`.`borravalo` SET `ar` =  '".$_GET['value']."' WHERE  `borravalo`.`ID` =".$_GET['id'].";");
		$query = mysql_query("SELECT * FROM `borravalo` WHERE `borravalo`.`ID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		
		echo $result['ar'];
	
	//biztonsági email elküldése
	} else if ($_GET['page'] == 28) {
		echo mail("markmanomolnar@gmail.com", "Stand stat: ".$database.".".$_GET['sid'], str_replace("[/n]", "\n", $_GET['string']));
		
	//kocsma beállítás változtatás
	} else if ($_GET['page'] == 29) {
		
		//setting alapján feladat
		//lottó ki be kapcsolása
		if ($_GET['setting'] == "isLotto") {
			
			mysql_query("UPDATE `".$database."`.`pub` SET `isLotto` = '".$_GET['value']."' WHERE `pub`.`ID` = ".$_GET['pid'].";");
			
		//egyéb beállítások
		} else if (!empty($_GET['setting']) && $_GET['setting'] != "isLotto") {
			
			//lekérjük van-e létrehozva rekord a beállításnak
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`option` LIKE '".$_GET['setting']."' AND `pubOptions`.`PID` = ".$_GET['pid'].";");
			$result = mysql_fetch_assoc($query);
			
			//ha van már rekord neki átállítás
			if (is_array($result)) {
				mysql_query("UPDATE `".$database."`.`pubOptions` SET `value` = '".$_GET['value']."' WHERE `pubOptions`.`ID` = ".$result['ID'].";");
				
			//ha nincs neki rekord
			} else {
				mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_GET['pid']."', '".$_GET['setting']."', '".$_GET['value']."');");
				
			}
			
		}
		
	//Sorsjegy beállítások
	} else if ($_GET['page'] == 30) {
		
		//Sorsjegyhez tartozó kocsma mentése
		if ($_GET['action'] == "sorsjegypidment") {
			
			//Lekéri van-e már beállítva sorsjegyPubId
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `PID` = ".$_GET['pid'].";");
			$pubOptions = array();
			while ($result = mysql_fetch_assoc($query)) {
				$pubOptions[$result['option']] = $result;
			}
			
			//Ha van frissíti
			if (isset($pubOptions['sorsjegyPubId'])) {
				
				mysql_query("UPDATE  `".$database."`.`pubOptions` SET  `value` =  '".$_GET['spubid']."' WHERE  `pubOptions`.`ID` =".$pubOptions['sorsjegyPubId']["ID"].";");
				
			//Ha nincs újat hoz létre	
			} else {
				
				mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_GET['pid']."', 'sorsjegyPubId', '".$_GET['spubid']."');");
			
			}
		
		//új aktív nyeremény mentése
		} else if ($_GET['action'] == "newnyeremeny") {
			//echo $_GET['pid']." ".$_GET['nyid']." ".$_GET['did'];
			mysql_query("INSERT INTO `".$database."`.`sorsjegyNyeremenyek` (`ID`, `pid`, `did`, `sid`, `fogy`) VALUES (NULL, '".$_GET['pid']."', '".$_GET['did']."', '".$_GET['nyid']."', '".$_GET['fogy']."');");
		
		//Jutalék mentése
		} else if ($_GET['action'] == "jutalekment") {
			
			//Lekérjük van-e már jutalék sor a pubOptions-ben
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_GET['pid']." AND `pubOptions`.`option` LIKE 'jutalek';");
			$result = mysql_fetch_assoc($query);
			
			//ha nincs eredmény újat hoz létre ha van csak módosít rajta
			if (!isset($result['ID'])) {
				mysql_query("INSERT INTO `".$database."`.`pubOptions` (`ID`, `PID`, `option`, `value`) VALUES (NULL, '".$_GET['pid']."', 'jutalek', '".$_GET['jutalek']."');");
			} else {
				mysql_query("UPDATE  `".$database."`.`pubOptions` SET  `value` =  '".$_GET['jutalek']."' WHERE  `pubOptions`.`ID` =".$result['ID'].";");
			}
		
		//nyeremény törlése
		} else if ($_GET['action'] == "nyeremenytorol") {
			mysql_query("DELETE FROM `".$database."`.`sorsjegyNyeremenyek` WHERE `sorsjegyNyeremenyek`.`ID` = ".$_GET['id'].";");
		}
	
	//sorsjegy összesítés standlapra
	} else if($_GET["page"] == 31) {
		
		//Legutóbbi stand lekérése
		$query = mysql_query("SELECT * FROM `standok` 
							LEFT JOIN `standLezarasIdopont`
							ON `standLezarasIdopont`.`sid` = `standok`.`ID`
							WHERE `standok`.`PID` = ".$_GET['pid']."
							AND `standok`.`finished` = 1
							ORDER BY `standok`.`ID` DESC
							LIMIT 0,1;");
		$lastStand = mysql_fetch_assoc($query);
		
		//ehhez a kocsmához tartozó nyeremények, mennyiségek és árak lekérése
		$query = mysql_query("SELECT * FROM `sorsjegyNyeremenyek` 
							LEFT JOIN `drinks`
							ON `sorsjegyNyeremenyek`.`did` = `drinks`.`ID`
							WHERE `sorsjegyNyeremenyek`.`pid` = ".$_GET['pid'].";");
		$nyeremenyek = array();
		while ($result = mysql_fetch_assoc($query)) {
			$nyeremenyek[$result['sid']] = $result;
		}
		
		//Lekéri van-e már beállítva sorsjegyPubId
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `PID` = ".$_GET['pid'].";");
		$pubOptions = array();
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result;
		}
		
		mysql_close($kapcsolat);
		
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
		
		//sorsjegy adatbázisból összes nyeremény lekérése
		$query = mysql_query("SELECT * FROM `nyeremenyek`;");
		$snyeremenyek = array();
		while ($result = mysql_fetch_assoc($query)) {
			$snyeremenyek[$result['ID']] = $result;
			
		}
		
		//nyertes sorsjegyek lekérése és összesítés megkezdése
		$all = array();
		$query = mysql_query("SELECT * 
FROM  `sorsjegyek` 
WHERE  `hasznalt` =".$pubOptions['sorsjegyPubId']['value']."
AND `nyeremeny` != 0
AND  `felhasznalt` >  '".$lastStand['idopont']."';");
		$sorsjegyek = array();
		while ($result = mysql_fetch_assoc($query)) {
			
			//Ha nincs még az allban a nyeremény
			if (!isset($all[$result['nyeremeny']]['sid'])) {
				$all[$result['nyeremeny']]['sid'] = $result['nyeremeny'];
				$all[$result['nyeremeny']]['nev'] = $snyeremenyek[$result['nyeremeny']]['nev'];
				$all[$result['nyeremeny']]['menny'] = $nyeremenyek[$result['nyeremeny']]['fogy'];
				$all[$result['nyeremeny']]['ar'] = $nyeremenyek[$result['nyeremeny']]['price'] * $nyeremenyek[$result['nyeremeny']]['fogy'];
			
			//Ha már benne van az allban
			} else {
				$all[$result['nyeremeny']]['menny'] += $nyeremenyek[$result['nyeremeny']]['fogy'];
				$all[$result['nyeremeny']]['ar'] += ($nyeremenyek[$result['nyeremeny']]['price'] * $nyeremenyek[$result['nyeremeny']]['fogy']);
				
			}
		}

		//all sorosítása
		$serial = "";
		foreach ($all as $sid => $value) {
			$serial .= $sid.",".$value['nev'].",".$value['menny'].",".$value['ar'].";";
		}
		
		//var_dump($pubOptions);
		echo $serial;
	
	//STATISZTIKA: Lekéri milyen italokhoz volt stand egy adott időszakban
	} else if ($_GET['page'] == 32) {
		
		$ret = "";
		$query = mysql_query("
			SELECT `drinks`.*
			FROM `stand`

			LEFT JOIN`standok`
			ON `standok`.`ID` = `stand`.`SID`

			LEFT JOIN `drinks`
			ON `stand`.`DID` = `drinks`.`ID`

			WHERE `standok`.`date` BETWEEN '".$_GET['sd']."' AND '".$_GET['ed']."'
			AND `standok`.`PID` = ".$_GET['pid']."

			GROUP BY `drinks`.`ID`
			ORDER BY `drinks`.`name` ASC;
		");
		while ($result = mysql_fetch_assoc($query)) {
			$ret .= $result['ID']."-".$result['name'].";";
		}
		echo $ret;
		
	//DASHSTAT: Két évet összehasonlító lekérés
	} else if ($_GET['page'] == 33) {
		
		//returnök
		$treturn = "";
		$lreturn = "";
		
		//tavalyi év lekérés
		$tavalyquery = mysql_query("
			SELECT ROUND(sum(`stand`.`fogyas`*`stand`.`price`)) as `_final`, `standok`.*
			FROM  `stand`

			LEFT JOIN `standok`
			ON `stand`.`SID` = `standok`.`ID`

			WHERE  `PID` =".$_GET['pid']."
			AND `standok`.`date` BETWEEN '".$_GET['lyear']."-01-01 00:00:00' AND '".$_GET['lyear']."-12-31 23:59:59'

			GROUP BY YEAR(`standok`.`date`), MONTH(`standok`.`date`)
			ORDER BY `standok`.`date` ASC;
		");
		
		//Idei év lekérés
		$ideiquery = mysql_query("
			SELECT ROUND(sum(`stand`.`fogyas`*`stand`.`price`)) as `_final`, `standok`.*
			FROM  `stand`

			LEFT JOIN `standok`
			ON `stand`.`SID` = `standok`.`ID`

			WHERE  `PID` =".$_GET['pid']."
			AND `standok`.`date` BETWEEN '".$_GET['tyear']."-01-01 00:00:00' AND '".$_GET['tyear']."-12-31 23:59:59'

			GROUP BY YEAR(`standok`.`date`), MONTH(`standok`.`date`)
			ORDER BY `standok`.`date` ASC;
		");
		
		//Lekérés elvégzése
		while ($result = mysql_fetch_assoc($tavalyquery)) {
			//echo ";";
			$treturn .= $result['_final'].";";
		}
		while ($result = mysql_fetch_assoc($ideiquery)) {
			$lreturn .= $result['_final'].";";
		}
		
		echo $treturn."|".$lreturn;
	
	//DASHSTAT: pultosok összehasonlítása
	} else if ($_GET['page'] == 34) {
		
		$query = mysql_query("
			SELECT `standok`.`UID`,

			(SELECT `user`.`uname` FROM `user` WHERE `user`.`ID` = `standok`.`UID`) as 'name',

			FLOOR(SUM((SELECT SUM(`stand`.`fogyas` * `stand`.`price`) FROM `stand` WHERE `stand`.`SID` = `standok`.`ID` )) / sum(`standok`.`wtime`)) as 'forgalom' 

			FROM `standok`
			WHERE `standok`.`PID` = ".$_GET['pid']." AND 
			`standok`.`date` BETWEEN '".$_GET['tol']."' AND '".$_GET['ig']."'
			GROUP BY `standok`.`UID`;
		");
		while ($result = mysql_fetch_assoc($query)) {
			$users .= $result['name'].";".$result['forgalom']."|";
		}
		echo $users;
	
	//STANDLAP: nyitó korrigálás, előző standlap
	} else if ($_GET['page'] == 35) {
		
		//legutóbbi standlap
		$query = mysql_query("
			SELECT * 
			FROM `standok` 

			WHERE `standok`.`PID` = ".$_GET['pid']." 
			AND `standok`.`ID` < ".$_GET['sid']." 
			ORDER BY `standok`.`ID` DESC 
			LIMIT 0,1;");
		$last_sid = mysql_fetch_assoc($query);
		
		$query = mysql_query("
		SELECT * 
		FROM  `stand` 
		WHERE  `SID` =".$_GET['sid']."
		AND  `DID` =".$_GET['did'].";
		");
		$ar = mysql_fetch_assoc($query);
		
		echo $ar['price'];
		
		/*//az előtti
		$query = mysql_query("
			SELECT * 
			FROM `standok` 

			WHERE `standok`.`PID` = ".$_GET['pid']." 
			AND `standok`.`ID` < ".$_GET['sid']." 
			ORDER BY `standok`.`ID` DESC 
			LIMIT 1,1;");
		$blast_sid = mysql_fetch_assoc($query);
		
		//előző előtti maradványok arraybe (előző nyitója lészen)
		$blast_maradvany = array();
		$query = mysql_query("
			SELECT sum(`pluszRaktarMaradvany`.`maradvany`) as `sum`, `pluszRaktarMaradvany`.* 
			FROM `pluszRaktarMaradvany` 

			WHERE `pluszRaktarMaradvany`.`SID` = ".$blast_sid['ID']."
			GROUP BY `pluszRaktarMaradvany`.`DID`;
		");
		while ($result = mysql_fetch_assoc($query)) {
			$blast_maradvany[$result['DID']] = $result['sum'];
		}
		
		//italforg lekérése
		$italforgalom = array();
		$query = mysql_query("
			SELECT `stand`.*, 
			`pvetel`.`value` as `pvetel`, 
			SUM(`pluszRaktarMaradvany`.`maradvany`) as `pmaradvany`
			FROM `stand` 

			LEFT JOIN `pvetel`
			ON `pvetel`.`DID` = `stand`.`DID` 
			AND `pvetel`.`SID` = `stand`.`SID`

			LEFT JOIN `pluszRaktarMaradvany`
			ON `pluszRaktarMaradvany`.`DID` = `stand`.`DID` 
			AND `pluszRaktarMaradvany`.`SID` = `stand`.`SID`

			WHERE `stand`.`SID` = ".$last_sid['ID']." 
			GROUP BY `stand`.`DID`;
		");
		while ($result = mysql_fetch_assoc($query)) {
			$italforgalom[$result['DID']] = $result;
		}
		
		//italforgalom számolása
		$all1 = 0;
		foreach ($italforgalom as $key => $value) {
			$all1 += (($value['nyito']+$blast_maradvany[$key]) + ($value['vetel']+$value['pvetel']) - $value['pmaradvany'] - ($value['nyito']+$value['vetel']-$value['fogyas']))*$value['price'];
		}
		
		//kiadások lekérése
		$all2 = 0;
		$query = mysql_query("SELECT SUM(`ertek`) as `all` FROM `kiadasok` WHERE `kiadasok`.`SID` = ".$last_sid['ID'].";");
		$result = mysql_fetch_assoc($query);
		if (is_array($result)) {
			$all2 = $result['all'];
		}
		
		//saját fogyasztás lekérése
		$all3 = 0;
		$query = mysql_query("SELECT SUM(`sajatFogyasztas`.`value`) as `all` FROM `sajatFogyasztas` WHERE `sajatFogyasztas`.`SID` = ".$last_sid['ID'].";");
		$result = mysql_fetch_assoc($query);
		if (is_array($result)) {
			$all3 = $result['all'];
		}
		
		//étel forgalom
		$all4 = 0;
		$query = mysql_query("SELECT SUM(`etelFogyasztas`.`fogyas`) as `all` FROM `etelFogyasztas` WHERE `etelFogyasztas`.`SID` = ".$last_sid['ID'].";");
		$result = mysql_fetch_assoc($query);
		if (is_array($result)) {
			$all4 = $result['all'];
		}
		
		//bankkártya forgalom
		$all5 = 0;
		$query = mysql_query("SELECT * FROM `bankkartyasFizetes` WHERE `bankkartyasFizetes`.`SID` = ".$last_sid['ID'].";");
		$result = mysql_fetch_assoc($query);
		if (is_array($result)) {
			$all5 = $result['value'];
		}
		
		echo $all1+$all2+$all3+$all4-$all5;*/
		
	//nyitó korrigálása
	} else if ($_GET['page'] == 36) {
		
		//ha pult raktár
		if ($_GET['choosedStorage'] == 0) {
			
			//eredeti sor lekérése
			$query = mysql_query("SELECT * FROM `stand` WHERE `ID` = ".$_GET['sorid'].";");
			$standsor = mysql_fetch_assoc($query);
			
			//lekérjük a plusz vételezést
			$query = mysql_query("SELECT * FROM `pvetel` WHERE `DID` = ".$_GET['sordid']." AND `SID` = ".$_GET['sid'].";");
			$pvetel = mysql_fetch_assoc($query);
			
			//módosítjuk a nyitót
			//mysql_query("UPDATE `stand` SET `stand`.`nyito`=".(intval($standsor['nyito'])+intval($standsor['vetel'])).", `stand`.`vetel` = ".$pvetel['value']." WHERE `stand`.`ID` = ".$_GET['sorid'].";");
			
			//töröljük a plusz vételezést
			//mysql_query("DELETE FROM `pvetel` WHERE `pvetel`.`ID`=".$pvetel['ID'].";");
			
			//legutóbbi standlap
			$query = mysql_query("
				SELECT * 
				FROM `standok` 

				WHERE `standok`.`PID` = ".$_GET['pid']." 
				AND `standok`.`ID` < ".$_GET['sid']." 
				ORDER BY `standok`.`ID` DESC 
				LIMIT 0,1;");
			$last_sid = mysql_fetch_assoc($query);
			
			//lekérjük a legutóbbi standlap standsorát
			$query = mysql_query("SELECT * FROM `stand` WHERE `DID` = ".$_GET['sordid']." AND `SID` = ".$last_sid['ID'].";");
			$last_standsor = mysql_fetch_assoc($query);
			
			//utolsó standlapon szereplő fogyás módosítása
			//mysql_query("UPDATE `stand` SET `stand`.`fogyas`=".(intval($last_standsor['fogyas'])-intval($standsor['vetel']))." WHERE `stand`.`ID` = ".$last_standsor['ID'].";");
			
		//ha plusz raktár
		} else {
			
			//eredeti sor lekérése
			$query = mysql_query("SELECT * FROM `stand` WHERE `ID` = ".$_GET['sorid'].";");
			$standsor = mysql_fetch_assoc($query);
			
			//lekérjük a plusz vételezést
			$query = mysql_query("SELECT * FROM `pvetel` WHERE `DID` = ".$_GET['sordid']." AND `SID` = ".$_GET['sid'].";");
			$pvetel = mysql_fetch_assoc($query);
			
			if (!is_array($pvetel)) {
				$pvetel = array();
				$pvetel['ID'] = -1;
				$pvetel['value'] = 0;
			}
			
			//módosítjuk a nyitót
			//mysql_query("UPDATE `stand` SET `stand`.`vetel` = ".$pvetel['value'].", `stand`.`fogyas` = ".($standsor['fogyas']-$standsor['vetel']+$pvetel['value'])." WHERE `stand`.`ID` = ".$_GET['sorid'].";");
			
			//töröljük a plusz vételezést
			if ($pvetel['ID'] != -1) {
				//mysql_query("DELETE FROM `pvetel` WHERE `pvetel`.`ID`=".$pvetel['ID'].";");
			}
			
			//legutóbbi standlap
			$query = mysql_query("
				SELECT * 
				FROM `standok` 

				WHERE `standok`.`PID` = ".$_GET['pid']." 
				AND `standok`.`ID` < ".$_GET['sid']." 
				ORDER BY `standok`.`ID` DESC 
				LIMIT 0,1;");
			$last_sid = mysql_fetch_assoc($query);
			
			//lekérjük a legutóbbi standlap pluszraktárát
			$query = mysql_query("SELECT * FROM `pluszRaktarMaradvany` WHERE `DID` = ".$_GET['sordid']." AND `SID` = ".$last_sid['ID'].";");
			$last_standsor = mysql_fetch_assoc($query);
			
			//utolsó standlapon szereplő fogyás módosítása
			//mysql_query("UPDATE `pluszRaktarMaradvany` SET `pluszRaktarMaradvany`.`maradvany`=".(intval($last_standsor['maradvany'])+intval($standsor['vetel']))." WHERE `pluszRaktarMaradvany`.`ID` = ".$last_standsor['ID'].";");
			
		}
		
		//korrigálás hozzáadása az adatbázishoz
		//mysql_query("INSERT INTO `".$database."`.`korrigalasok` (`ID`, `SID`, `UID`, `DID`, `stand_date`, `value`, `msg`) VALUES (NULL, '".$last_sid['ID']."', '".$last_sid['UID']."', '".$_GET['sordid']."', '".$last_sid['date']."', '".$_GET['valtozas']."', '".$_GET['message']."');");
		echo "INSERT INTO `".$database."`.`korrigalasok` (`ID`, `SID`, `UID`, `DID`, `stand_date`, `value`, `msg`) VALUES (NULL, '".$last_sid['ID']."', '".$last_sid['UID']."', '".$_GET['sordid']."', '".$last_sid['date']."', '".$_GET['valtozas']."', '".$_GET['message']."');";
	}
	
	//CLOSE
	mysql_close($kapcsolat);
?>