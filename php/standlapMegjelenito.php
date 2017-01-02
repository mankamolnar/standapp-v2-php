<?php
	
	//Új elszámolás megadása
	function setElszam() {
		
		if (!isset($_POST['do'])) {
			
			//Form
			echo "
				<div class='anchor2'>Új stand felvitele</div><br />
				
				<form action='index.php?page=1' method='post'>
					<input type='hidden' name='do' value='createStand' />
					<b>Mettől meddig dolgozol?</b><br />
					<input type='text' name='tol' id='dateset' autocomplete='off' />-tól <input type='text' name='ig' id='dateset2' autocomplete='off' />-ig <br />
					<input type='submit' value='Létrehozás' />
				</form>
			";
		
		//stand létrehozás	
		} else if ($_POST['do'] == "createStand") {
			
			//Dátumok ellenőrzése
			if (preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['tol']) && preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_POST['ig'])) {
				
				//timestamppé alakít és elvégzi a kivonást
				$diff = abs(strtotime($_POST['ig']) - strtotime($_POST['tol']));
				
				//napok
				$days = floor($diff / (60*60*24));
				$days++;
				
				include("conf/mysql.php");
		
				//MYSQL SERVER
				$kapcsolat = mysql_connect($szerver, $user, $pass);
				mysql_set_charset('utf8',$kapcsolat);
				if ( ! $kapcsolat )
				{
					die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
				}
				mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
				
				//ellenőrző query
				$query = mysql_query("
					SELECT * 
					FROM `standok` 
					WHERE `PID` = ".$_SESSION['pub']->ID." 
					AND `finished` = '0' 
					LIMIT 0, 1;
				");
				
				//ha nincs lezáratlan stand
				if (!$result = mysql_fetch_assoc($query)) {
				
					//stand létrehozás
					mysql_query("INSERT INTO `".$database."`.`standok` VALUES (NULL, 0, '".mysql_real_escape_string($_POST['ig'])." 00:00:00', ".$days.", ".$_SESSION['pub']->ID.", ".$_SESSION['ID'].", 0);");
				
					$thisId = mysql_insert_id();
					
				}
				
				//lezár
				mysql_close($kapcsolat);
				
				if (isset($thisId)) {
					header("Location: index.php?page=3&id=$thisId&mod=0");
				} else {
					header("Location: index.php");
				}
				
			//hibás dátum
			} else {
				
				echo "Hibásan adtad meg a dátumot!".menu(3);
				
			}
			
		}
	
	}
	
	function standHeader($id) {
		
		if (is_numeric($id)) {
			
			//stand Fejléc lekérdezése
			$query = mysql_query("SELECT `standok`.`ID`, `standok`.`date`, `standok`.`wtime`, `user`.`uname` FROM `standok` LEFT JOIN `user` ON `user`.`ID` = `standok`.`UID` WHERE `standok`.`ID` = ".$id.";");
			$result = mysql_fetch_assoc($query);
			
			//ha alkalmazottnál nagyobb jogosultság előző következő menü jelenjen meg
			if ($_SESSION['tether'] > 0) {
				
				//előző gomb
				$prevQ = mysql_query("SELECT `standok`.`ID` FROM `standok` WHERE `ID` < ".$_GET['id']." AND `standok`.`PID` = ".$_SESSION['pub']->ID." ORDER BY `standok`.`ID` DESC LIMIT 0,1;");
				$prevSt = mysql_fetch_assoc($prevQ);
				
				//ha van válasz, ha nincs
				echo "<div id='prevMenu'>";
				if (is_array($prevSt)) {
					echo "<a href='index.php?page=3&id=".$prevSt['ID']."&mod=".$_GET['mod']."' class='anchor2'>Elözö stand</a>";
				} else {
					echo "<a href='index.php?page=3&id=".$_GET['id']."&mod=".$_GET['mod']."' class='anchor2'>Elözö stand</a>";
				}
				echo "</div>";
				
				//következő gomb
				$nextQ = mysql_query("SELECT `standok`.`ID` FROM `standok` WHERE `ID` > ".$_GET['id']." AND `standok`.`PID` = ".$_SESSION['pub']->ID." ORDER BY `standok`.`ID` ASC LIMIT 0,1;");
				$nextSt = mysql_fetch_assoc($nextQ);
				
				//ha van válasz, ha nincs
				echo "<div id='nextMenu'>";
				if (is_array($nextSt)) {
					echo "<a href='index.php?page=3&id=".$nextSt['ID']."&mod=".$_GET['mod']."' class='anchor2'>Következö stand</a>";
				} else {
					echo "<a href='index.php?page=3&id=".$_GET['id']."&mod=".$_GET['mod']."' class='anchor2'>Következö stand</a>";
				}
				echo "</div>";
			}
			
			//Kezdő és befejező dátum formázása és számolása
			$datetime = new DateTime($result['date']);
			$end_date = $datetime->format('Y.m.d'); //Eredeti format idővel: format('Y-m-d H:i:s')
			$real_wtime = $result['wtime'] - 1; //1 nappal kevesebbet kell levonni
			date_sub($datetime, new DateInterval('P'.$real_wtime.'D')); //wtime kivonása a befejező dátumból
			$start_date = $datetime->format('Y.m.d');
			
			//lekérjük van-e a standhoz tartozó ellenőrizve
			$equery = mysql_query("SELECT * FROM standEllenorizve WHERE `SID` =".$_GET['id']." ;");
			$eresult = mysql_fetch_assoc($equery);
			
			$tick = "";
			if (is_array($eresult)) {
				$tick = "<img src='img/tick.png' class='standHeadIcon' />";
			}

			echo "
				<table border='0' width='30%' cellpadding='3' cellspacing='0'>
					<tr align='center' class='osszesitoSor'>
						<td class='td1'><b>".$start_date."-tól ".$end_date."-ig <img src='img/print.png' class='standHeadIcon' onclick='window.print();' /> $tick</b></td>
						<td class='td1'><b>".$result['uname']."</b></td>
						<td class='td3'></td>
					</tr>
					<tr align='center'>
						<td class='td2'></td>
						<td class='td2'><br /></td>
						<td></td>
					</tr>
				</table>
			";
			
		}
		
	}
	
	function listAkciosTermekek($disabled, $co) {
		
		//akcios termekek
		$query = mysql_query("
SELECT `Akciok`.*,
`drinks`.`name`,
`standok`.`date` as `sdate`,
`standok`.`wtime`,
`AkciosStand`.`ID` as `ASID`,
`AkciosStand`.`price` as `price2`,
`AkciosStand`.`fogyas`,
`AkciosStand`.`SID`,
`Akciok`.`ID` as `AID`,
`AkciosStand`.`fogyas` * `AkciosStand`.`price` AS `akErtek`,

WEEKOFYEAR(ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1)),
WEEKOFYEAR(`standok`.`date`),
ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1),
DAYOFWEEK(`Akciok`.`date`)

FROM `Akciok`

LEFT JOIN `drinks`
ON `drinks`.`ID` = `Akciok`.`DID`

LEFT JOIN `AkciosStand`
ON `AkciosStand`.`AID` = `Akciok`.`ID`
AND `AkciosStand`.`SID` = ".$_GET['id']."

LEFT JOIN `standok`
ON `standok`.`ID` = ".$_GET['id']."
 
WHERE
`Akciok`.`PID` = ".$_SESSION['pub']->ID."
AND
(
  `Akciok`.`activated` = 1
  OR
  ISNULL(`AkciosStand`.`price`) = 0
)
AND
(
  (
    `Akciok`.`usual` = 0
	
    AND
	
	`Akciok`.`date` BETWEEN
    ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1)
    AND `standok`.`date`
	
  )
  OR
  (
    `Akciok`.`usual` = 1
	
	AND
	
	DAYOFWEEK(ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1)) > DAYOFWEEK(`standok`.`date`)
	
	AND
	
	(
	  DAYOFWEEK(`Akciok`.`date`) <= DAYOFWEEK(`standok`.`date`)
	  
	  OR
	  
	  DAYOFWEEK(`Akciok`.`date`) >= DAYOFWEEK(ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1))
	)
  )
  OR
  (
    `Akciok`.`usual` = 1
	
	AND
	
	`standok`.`wtime` = 1
	
	AND
	
	(
	  DAYOFWEEK(`Akciok`.`date`) = DAYOFWEEK(`standok`.`date`)
	  
	)
  )
  OR
  (
	`Akciok`.`usual` = 1
	
	AND
	
	DAYOFWEEK(ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1)) < DAYOFWEEK(`standok`.`date`)
	
	AND
	
	(
	  DAYOFWEEK(`Akciok`.`date`) <= DAYOFWEEK(`standok`.`date`)
	  
	  AND
	  
	  DAYOFWEEK(`Akciok`.`date`) >= DAYOFWEEK(ADDDATE(CAST(`standok`.`date` AS DATE), (`standok`.`wtime` -1) * -1))
	)
  )
);");
		
		//legelső modulként Form megnyitása
		echo "<form action='index.php?page=5' method='post' id='frm'>";
		
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//kezdő sor kiírása ha első
			if ($i == 0) {
				
				//Akcios termékek kiírása
				echo "
					<p class='anchor2'>Napi akciók</p>
					<table border='0' width='90%' cellpadding='3' cellspacing='0'>
						<tr align='center' class='osszesitoSor'>
							
							<td class='td1'>
								<b>Név</b>
							</td>
							
							<td class='td1'>
								<b>Fogyás</b>
							</td>
							
							<td class='td1'>
								<b>Egységár</b>
							</td>
							
							<td class='td1'>
								<b>Érték</b>
							</td>
							
							<td class='td3'>
								
							</td>
				";
				
			}
			
			//Ár beállítás
			if ($result['price2'] != "") {
			      $result['price'] = $result['price2'];
			}
			
			//érték beállítás
			if ($result['akErtek'] == "") {
				$result['akErtek'] = 0;
			} else {
				$result['akErtek'] = round($result['akErtek']);
			}
			
			//init AkciosStand id
			if ($result['ASID'] == "") {
				$result['ASID'] = -1;
			}
			
			//set fogyas
			if ($result['fogyas'] != "") {
				$result['fogyas'] = round($result['fogyas'], 2);
			}
			
			//date set
			$result['date'] = date('l', strtotime($result['date']));
			switch ($result['date']) {
				case 'Monday':
					$result['date'] = "Hétfő";
					break;
				case 'Tuesday':
					$result['date'] = "Kedd";
					break;
				case 'Wednesday':
					$result['date'] = "Szerda";
					break;
				case 'Thursday':
					$result['date'] = "Csütörtök";
					break;
				case 'Friday':
					$result['date'] = "Péntek";
					break;
				case 'Saturday':
					$result['date'] = "Szombat";
					break;
				case 'Sunday':
					$result['date'] = "Vasárnap";
					break;
			}
			
			//akFeltoltve beállítása
			if ($result['ASID'] == -1) {
				$akFeltoltve = 0;
			} else {
				$akFeltoltve = 1;
			}
			
			echo "
				<tr align='center' class='sor".($i%2)."' id='asor$i'>
					<td class='tdName'>
						<span id='akciosNeve$i'>".$result['name']."</span> (".$result['date'].")
						<input type='hidden' name='AID$i' value='".$result['AID']."' />
						<input type='hidden' name='DID$i' value='".$result['DID']."' />
						<input type='hidden' name='ASID$i' value='".$result['ASID']."' />
					</td>
					
					<td class='td1'>
						<input type='text' name='aFogy$i' class='inputText' onblur='countAkLine($i);' onkeyup='checkKeyPressed($i);' onfocus='setCo($co, $i, 0);' value='".$result['fogyas']."' $disabled />
					</td>
					
					<td class='td1'>
						<span id='aPrice$i'>".$result['price']."</span> Ft
					</td>
					
					<td class='td1'>
						<span id='aErtek$i'>".$result['akErtek']."</span> Ft
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
			
			$i++;
			
		}
		
		//ha voltak sorok lezárás
		if ($i != 0) {
			//table close
			echo "
				<tr align='center'>
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td class='td2'>
						
					</td>
					
					<td>
					
					</td>
				</tr>
			</table><br /><br />";
			
			$co++;
		}
		
		return $co;
	}
	
	function listStandlap($disabled, $pubOptions, $mertekegyseg, $co, $finished) {
		
		//HA VANNAK PLUSZ RAKTÁROK LEKÉRJÜK ŐKET
		$storages = "";
		if (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 1) {
			
			//RAKTÁROK LEKÉRÉSE
			$tmp = "";
			$query = mysql_query("SELECT * FROM `pluszRaktar` WHERE `PID` = ".$_SESSION['pub']->ID." ORDER BY `ID` ASC;");
			while ($result = mysql_fetch_assoc($query)) {
				$tmp .= $result['ID'].";";
			}
			$storages = "<input type='hidden' name='pluszRaktarak' value='$tmp' />";
			
		}
		$nyitoto = "";
		if (isset($pubOptions['nyitoToMaradvany']) && $pubOptions['nyitoToMaradvany'] == 1) {
			$nyitoto = "<input type='hidden' id='nyitoToMaradvany' value='1' />";
		}
		
		//ha nincs plusz raktár a puboptions tömbben
		if (!isset($pubOptions['pluszRaktar'])) {
			$pubOptions['pluszRaktar'] = 0;
		}
		
		//ÖSSZES ITALHOZ LEKÉRDEZÉS
		$query = mysql_query('
SELECT 
`standok`.`ID` as `SID`, 
`standok`.`finished`,
`drinks`.`ID` as `DID`,
`drinks`.`CSID`,
`drinks`.`MID`,
`drinks`.`name`,
`drinks`.`forditott`,
`stand`.`ID` as `ID`,
`stand`.`nyito` as `nyito`,
`stand`.`vetel` as `vetel`,
`stand`.`fogyas` as `fogyas`,
`standOld`.`SID` as `oldSID`,
sum(`AkciosStand`.`fogyas`) as `AkFogyas`,

CASE WHEN `drinks`.`forditott` = 0 AND `stand`.`nyito` IS NOT NULL THEN
  (`stand`.`nyito` + `stand`.`vetel` - `stand`.`fogyas`)
WHEN `drinks`.`forditott` = 1 AND `stand`.`nyito` IS NOT NULL THEN
  (`stand`.`nyito` + `stand`.`vetel` + `stand`.`fogyas`)
END as `maradvany`,

CASE WHEN `drinks`.`forditott` = 0 AND `stand`.`nyito` IS NULL THEN
  (`standOld`.`nyito` + `standOld`.`vetel` - `standOld`.`fogyas`)
WHEN `drinks`.`forditott` = 1 AND `stand`.`nyito` IS NULL THEN
  (`standOld`.`nyito` + `standOld`.`vetel` + `standOld`.`fogyas`)
END as `maradvanyOld`,

CASE WHEN `stand`.`nyito` IS NOT NULL THEN
	(`stand`.`nyito` + `stand`.`vetel`)
ELSE
	0
END as `osszesen`,

CASE WHEN `stand`.`price` IS NULL THEN
	`drinks`.`price`
ELSE
	`stand`.`price`
END as `price`

FROM `standok` 

LEFT JOIN `drinks` 
ON `drinks`.`PID` = `standok`.`PID`

LEFT JOIN `stand` 
ON `drinks`.`ID` = `stand`.`DID` 
AND `stand`.`SID` = '.$_GET['id'].'

LEFT JOIN `stand` as `standOld`
ON `drinks`.`ID` = `standOld`.`DID`
AND `standOld`.`SID` = (
  SELECT `standok`.`ID`
  FROM `standok`
  WHERE `standok`.`ID` < '.$_GET['id'].'
  AND `standok`.`PID` = '.$_SESSION['pub']->ID.'
  ORDER BY `ID` DESC
  LIMIT 0,1
)
AND `stand`.`nyito` IS NULL

LEFT JOIN `AkciosStand`
ON `drinks`.`ID` = `AkciosStand`.`DID`
AND `AkciosStand`.`SID` = (
  SELECT `standok`.`ID`
  FROM `standok`
  WHERE `standok`.`ID` < '.$_GET['id'].'
  AND `standok`.`PID` = '.$_SESSION['pub']->ID.'
  ORDER BY `ID` DESC
  LIMIT 0,1
)


WHERE `standok`.`ID` = '.$_GET['id'].'
AND (
  (`drinks`.`visible` = 1 AND `standok`.`finished` = 0) 
  OR (`standok`.`finished` = 1 AND `stand`.`nyito` IS NOT NULL)
)

GROUP BY `drinks`.`ID`
ORDER BY `drinks`.`CSID`, `drinks`.`List.ID` ASC;
		');	
		
		//STANDOLÁS
		//TABLE CÍMSOROK
		echo "
			<p class='anchor2'>Standlap</p>
			<table border='0' width='90%' cellpadding='3' cellspacing='0'>
				<tr align='center' class='osszesitoSor'>
					<td class='td1'>
						<input type='hidden' id='sid' name='sid' value='".$_GET['id']."' />
						<input type='hidden' id='pid' name='pid' value='".$_SESSION['pub']->ID."' />
						<input type='hidden' id='uid' name='uid' value='".$_SESSION['ID']."' />
						<input type='hidden' id='pluszRaktar' name='pluszRaktar' value='".$pubOptions['pluszRaktar']."' />
						$storages
						$nyitoto
						<input type='hidden' id='standLezar' name='standLezar' value='".$finished."' />
						<b>Áru</b>
					</td>
					<td class='td1'><b>Nyitó</b></td>
					<td class='td1'><b>Vétel</b></td>
					<td class='td1'><b>Összesen</b></td>
					<td class='td1'><b>Maradvány</b></td>
					<td class='td1'><b>Fogyás</b></td>
					<td class='td1'><b>Egységár</b></td>
					<td class='td1'><b>Érték</b></td>
					<td class='td3'></td>
				</tr>
		";
		
		//KIIRAT
		$szin = 0;
		$aktCsop = 0;
		$j=0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//HA ELSŐ FUTÁS
			if ($szin == 0) {
				$aktCsop = $result['CSID'];
			}
			
			//Záró összesítő sor
			if ($aktCsop != $result['CSID']) {
				
				echo "
					<tr align='center' class='osszesitoSor'>
						<td class='td1'>
							<b>Összesen</b>
						</td>
						<td class='td1' colspan='7' align='right'>
							<input autocomplete='off' disabled='disabled' class='inputText' type='text' id='osszInp".$aktCsop."' onblur='countFinVal($aktCsop);' />
						</td>
						<td class='td3'>
						
						</td>
					</tr>";
				
				$aktCsop = $result['CSID'];
			
			}
			
			//megjeleníti az adott sort kivéve ha be van fejezve a stand és a nyitó mégis nulla (azaz a stand felvitelekor ez a sor még nem létezett)
			if (!($result['finished'] == 1 && $result['nyito'] == "")) {
			
				//Ha nyito null akkor az előző maradványát írja ki
				if ($result['nyito'] == "") {
					
					$result['ertek'] = 0;
					
					//Ha az előző maradványa is null akkor 0
					if ($result['maradvanyOld'] == "") {
						$result['nyito'] = 0;
					} else {
						
						$result['nyito'] = $result['maradvanyOld'];
						
						//ha az akcios fogyas nem nulla akkor azt levonja / hozzáadja a nyitohoz (forditott szamolastol függően)
						if ($result['AkFogyas'] != "") {
							
							if ($result['forditott'] == 0) {
								
								$result['nyito'] = $result['nyito'] - $result['AkFogyas'];
								
							} else {
								
								$result['nyito'] = $result['nyito'] + $result['AkFogyas'];
								
							}
							
						}
					}
					
					//nyitó maradványhoz másolása autómatikusan ha van ilyen beállítás
					if (isset($pubOptions['nyitoToMaradvany']) && $pubOptions['nyitoToMaradvany'] == 1) {
						
						if (!isset($pubOptions['pluszRaktar']) || (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 0)) {
							$result['maradvany'] = round($result['nyito'], 2);
						}
						
					}
					
				//ha nyito nem null akkor érték számolás és vétel meg maradvány kerekítés
				} else {
					
					$result['ertek'] = round($result['fogyas'] * $result['price']);
					$result['vetel'] = round($result['vetel'],2);
					
					//if maradvany null then dont round becouse it return 0 instead of null
					if ($result['maradvany'] != "") {
						$result['maradvany'] = round($result['maradvany'],2);
					}
					
				}
				
				//vétel doboznak kell-e szín
				$tmpCss = "";
				if ($result['vetel'] < 0) {
					$tmpCss = "style='background:#FF9FA4;'";
				} else if ($result['vetel'] > 0) {
					$tmpCss = "style='background:#A8FF9F;'";
				}
				
				//NYITÓ ÉS MARADVÁNY ha több raktár is van
				$newNyito = round($result['nyito'], 2);
				$newMaradvany = "<input $disabled autocomplete='off' class='inputTextSmall' type='text' name='standMaradvany".$j."[0]' id='maradvany".$j."0' onfocus='setCo($co, $j, 1);' onblur='standSor[$j].countAll($j, 1); countFinVal(".$result['CSID'].");' onkeyup='checkKeyPressed($j);' value='".$result['maradvany']."' />";
				$newPvetel = "";
				if (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 1) {
					
					//PULT KÉSZLET STRINGBE!
					$newNyito = "
						<div class='newNyitoDiv'>
							<input type='hidden' name='newNyito".$j."[0]' id='newNyito".$j."0' value='".round($result['nyito'], 2)."' />
							<span class='nyitonev'>Pult:</span>
							<span class='nyitoertek'>".round($result['nyito'], 2)."</span>
						</div>
					";
					
					//HA VAN ELÉRHETŐ LEGUTÓBBI STAND
					$nemvolt = true;
					$lastStand = mysql_query("
						  SELECT `standok`.`ID`
						  FROM `standok`
						  WHERE `standok`.`ID` < ".$_GET['id']."
						  AND `standok`.`PID` = ".$_SESSION['pub']->ID."
						  ORDER BY `ID` DESC
						  LIMIT 0,1
					");
					$lastStand = mysql_fetch_assoc($lastStand);
					if (is_array($lastStand)) {
						
						//lekéri a többi raktár nyitóját
						$stquery = mysql_query("
							SELECT * 
							FROM `pluszRaktarMaradvany` 
							
							LEFT JOIN `pluszRaktar` 
							ON `pluszRaktarMaradvany`.`PRID` = `pluszRaktar`.`ID` 
							
							WHERE `pluszRaktarMaradvany`.`SID` = ".$lastStand['ID']." 
							AND `pluszRaktarMaradvany`.`DID` = ".$result['DID'].";
						");
						while ($stresult = mysql_fetch_assoc($stquery)) {
							$nemvolt = false;
							$newNyito .= "
								<div class='newNyitoDiv'>
									<input type='hidden' name='newNyito".$j."[".$stresult['PRID']."]' id='newNyito".$j.$stresult['PRID']."' value='".round($stresult['maradvany'], 2)."' />
									<span class='nyitonev' id='nyitonev".$j.$stresult['ID']."'>".$stresult['nev'].":</span>
									<span class='nyitoertek'>".round($stresult['maradvany'], 2)."</span>
								</div>
							";
						}
					
					
					} 
					
					//NINCS ELÉRHETŐ STAND
					if (!is_array($lastStand) || $nemvolt) {
						
						//CSAK A RAKTÁRAKAT KÉRJE LE
						$stquery = mysql_query("SELECT * FROM `pluszRaktar` WHERE `pluszRaktar`.`PID` = ".$_SESSION['pub']->ID.";");
						while ($stresult = mysql_fetch_assoc($stquery)) {
							$newNyito .= "
								<div class='newNyitoDiv'>
									<input type='hidden' name='newNyito".$j."[".$stresult['ID']."]' id='newNyito".$j.$stresult['ID']."' value='0' />
									<span class='nyitonev' id='nyitonev".$j.$stresult['ID']."'>".$stresult['nev'].":</span>
									<span class='nyitoertek'>0</span>
								</div>
							";
						}
						
					}
					
					//MARADVÁNYOK GENERÁLÁSA HA ÚJ STANDLAP VAN
					if (trim($result['maradvany']) == "") {
						
						//LEKÉRJÜK A RAKTÁRAKAT ÉS MINDEGYIKRE LÉTREHOZUNK EGY ÜRES RUBRIKÁT
						$stquery = mysql_query("SELECT * FROM `pluszRaktar` WHERE `pluszRaktar`.`PID` = ".$_SESSION['pub']->ID.";");
						$newMaradvany = "<input $disabled autocomplete='off' class='inputTextSmall' type='text' name='standMaradvany".$j."[0]' id='maradvany".$j."0' onfocus='setCo($co, $j, 1, 0);' onblur='standSor[$j].countAll($j, 1); countFinVal(".$result['CSID'].");' onkeyup='checkKeyPressed($j);' value='' /><br />";
						while ($stresult = mysql_fetch_assoc($stquery)) {
							$newMaradvany .= "<input $disabled autocomplete='off' class='inputTextSmall' type='text' name='standMaradvany".$j."[".$stresult['ID']."]' id='maradvany".$j.$stresult['ID']."' onfocus='setCo($co, $j, 1, ".$stresult['ID'].");' onblur='standSor[$j].countAll($j, 1); countFinVal(".$result['CSID'].");' onkeyup='checkKeyPressed($j);' value='' /><br />";
						}
					}
					
					//HA LEZÁRT STANDLAPON VAGYUNK
					if ($finished == 1) {
						
						//plusz vételezés lekérése
						$pvquery = mysql_query("SELECT * FROM `pvetel` WHERE `DID` = ".$result['DID']." AND `SID` = ".$result['SID'].";");
						$pvresult = mysql_fetch_assoc($pvquery);
						
						if (is_array($pvresult)) {
							$newPvetel = '<br /><input autocomplete="off" class="inputTextSmall" type="text" name="pstandVetel['.$j.']" id="pvetel'.$j.'" onfocus="setCo(0, '.$j.', 0, 1);" onblur="standSor['.$j.'].countAll('.$j.', 1); countFinVal('.$result['CSID'].');" onkeyup="checkKeyPressed('.$j.');" value="'.round($pvresult['value'], 2).'">&nbsp;';
						}
						
						//maradványok lekérése
						$pmquery = mysql_query("SELECT * FROM  `pluszRaktarMaradvany` WHERE  `SID` = ".$result['SID']." AND  `DID` = ".$result['DID']." ORDER BY `PRID`;");
						while ($pmresult = mysql_fetch_assoc($pmquery)) {
							$newMaradvany .= "<br /><input $disabled autocomplete='off' class='inputTextSmall' type='text' name='standMaradvany".$j."[".$pmresult['PRID']."]' id='maradvany".$j.$pmresult['PRID']."' onfocus='setCo($co, $j, 1, ".$pmresult['PRID'].");' onblur='standSor[$j].countAll($j, 1); countFinVal(".$result['CSID'].");' onkeyup='checkKeyPressed($j);' value='".round($pmresult['maradvany'], 2)."' />";
						}
					}
				
				}
				
				//stand korrigálás ha lezárt a stand módosításra lett megnyitva és a jogosultság alkalmazott feletti
				$vonclick = "";
				if ($finished == 1 && $_GET['mod'] == 0 && $_SESSION['tether'] > 0) {
					$vonclick = "onclick='standKorrigalas(".$j.");'";
				}
				
				//SOR MEGJELENÍTÉS
				echo "
					<tr align='center' class='sor".($szin%2)."' id='stl$j'>
						<td class='tdName'>
							<span id='standlapNeve$j'>".$result['name']."</span>
							<input type='hidden' id='did$j' name='ndid[$j]' value='".$result['DID']."' />
							<input type='hidden' id='csid$j' name='ncsid[$j]' value='".$result['CSID']."' />
							<input type='hidden' id='sorId$j' name='nsorId[$j]' value='".$result['ID']."' />
							<input type='hidden' id='forditott$j' name='nforditott[$j]' value='".$result["forditott"]."' />
							<input type='hidden' name='standNyito[$j]' value='".round($result['nyito'],2)."' />
							<input type='hidden' name='standPrice[$j]' value='".$result['price']."' />
						</td>
						
						<td class='td1' id='nyito$j'>
							".$newNyito."
						</td>
						
						<td class='td1'>
							<input $disabled autocomplete='off' class='inputTextSmall' type='text' name='standVetel[$j]' id='vetel".$j."' onfocus='setCo($co, $j, 0);' onblur='standSor[$j].countAll($j, 1); countFinVal(".$result['CSID'].");' onkeyup='checkKeyPressed($j);' $vonclick value='".$result['vetel']."' $tmpCss />
							".$newPvetel."
						</td>
						
						<td class='td1' id='osszesen".$j."'>
							".round($result['osszesen'],2)."
						</td>
						
						<td class='td1'>
							".$newMaradvany."
						</td>
						
						<td class='td1' id='fogyas$j'>
							".round($result['fogyas'],2)."
						</td>
						
						<td class='td1'>
							<div style='position:relative; float:left;' id='egysegar$j'>".$result['price']."</div>
							<div style='position:relative; text-align:right;'>Ft</div>
						</td>
						
						<td class='td1' id='ertek$j'>
							".$result['ertek']."
						</td>
						
						<td class='td3' id='status$j'>
							<b>".($j+1).".</b>
						</td>
					</tr>
				";
				
				$szin++;
				$j++;
			}
			
		}
		
		echo "
			<tr align='center' class='osszesitoSor'>
				<td class='td1'>
					<b>Összesen</b>
				</td>
				<td class='td1' colspan='7' align='right'>
					<input disabled='disabled' autocomplete='off' class='inputText' type='text' id='osszInp".$aktCsop."' onblur='countFinVal($aktCsop);' />
				</td>
				<td class='td3'>
				
				</td>
			</tr>";
		
		
		return ++$co;
	}
	
	function listModLines($pubOptions, $disabled, $co, $finished) {
		
		//módosítható sorok megadása
		if (isset($pubOptions['modosithatoStand']) && $pubOptions['modosithatoStand'] == 1) {
			
			//autocomplete off az inputokon
			$ac0 = "autocomplete='off' $disabled";
			
			//feltöltött sorok kiirasa
			$query = mysql_query("
SELECT *,
(SELECT `standok`.`ID` FROM `standok` WHERE `ID` < ".$_GET['id']." AND `PID` = ".$_SESSION['pub']->ID." ORDER BY `ID` DESC LIMIT 0,1) AS 'lastID'
FROM `modStandSor`
HAVING `SID` = ".$_GET['id']." OR `SID` = `lastID`
ORDER BY `modStandSor`.`name` ASC;");

			$i = 0;
			while ($result = mysql_fetch_assoc($query)) {
				
				if ($result['SID'] <= $_GET['id']) {
					
					//csak főnöknek vagy rendszergazdának jelenik meg delete gomb ha nincs a stand lezárva
					if ($_SESSION['tether'] > 1 && $finished == 0) {
						$deleteBt = "<img src='img/close.png' onclick='deleteModSor($i);' style='width:30px;height:30px;cursor:pointer;' />";
					} else {
						$deleteBt = "<b>".($i+1).".</b>";
					}
					
					//MIKOR JELENJEN MEG A SOR
					if ($_GET['id'] == $result['SID']) {
						
						//vétel doboznak kell-e szín
						$tmpCss = "";
						if ($result['vetel'] < 0) {
							$tmpCss = "style='background:#FF9FA4;'";
						} else if ($result['vetel'] > 0) {
							$tmpCss = "style='background:#A8FF9F;'";
						}
						
						echo "
							<tr align='center' id='modsorline$i' class='sor".($i%2)."'>
								<td class='tdName'>
									<input type='hidden' name='modSorID".$i."' value='".$result['ID']."' />
									<input type='hidden' name='modSorDel".$i."' value='0' />
									<input type='hidden' name='modSorNev".$i."' value='".$result['name']."' />
									<input type='hidden' name='modSorNyito".$i."' value='".$result['nyito']."' />
									<input type='hidden' name='modSorSID".$i."' value='".$result['SID']."' />
									<input $ac0 type='text' class='inputText' id='secureMnev".$i."' value='".$result['name']."' onfocus='setCo($co, $i, 0);' disabled='disabled' />
								</td>
								<td class='td1'><input type='text' class='inputTextSmall' id='secureMnyito".$i."' value='".$result['nyito']."' disabled='disabled' /></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' $tmpCss name='modSorVetel".$i."' value='".$result['vetel']."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 1);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorOssz".$i."'>0</span></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorMarad".$i."' value='".$result['maradvany']."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 2);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorFogy".$i."'>0</span></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorAr".$i."' value='".$result['ar']."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 3);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorErtek".$i."'>0</span></td>
								<td class='td3'>$deleteBt</td>
							</tr>
						";
						
						$i++;
					} else if ($_GET['id'] != $result['SID'] && $result['lathatosag'] == 1) {
						echo "
							<tr align='center' id='modsorline$i' class='sor".($i%2)."'>
								<td class='tdName'>
									<input type='hidden' name='modSorID".$i."' value='".$result['ID']."' />
									<input type='hidden' name='modSorDel".$i."' value='0' />
									<input type='hidden' name='modSorNev".$i."' value='".$result['name']."' />
									<input type='hidden' name='modSorNyito".$i."' value='".$result['maradvany']."' />
									<input type='hidden' name='modSorSID".$i."' value='".$result['SID']."' />
									<input $ac0 type='text' class='inputText' id='secureMnev".$i."' value='".$result['name']."' onfocus='setCo($co, $i, 0);' disabled='disabled' />
								</td>
								<td class='td1'><input type='text' class='inputTextSmall' id='secureMnyito".$i."' value='".$result['maradvany']."' disabled='disabled' /></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorVetel".$i."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 1);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorOssz".$i."'>0</span></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorMarad".$i."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 2);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorFogy".$i."'>0</span></td>
								<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorAr".$i."' value='".$result['ar']."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 3);modSorKijeloles($i);' /></td>
								<td class='td1'><span id='modSorErtek".$i."'>0</span></td>
								<td class='td3'>$deleteBt</td>
							</tr>
						";
						
						$i++;
					}
				}
				
			}
			echo mysql_error();
			
			//üres sorok
			for ($j = 0; $j < 10; $j++) {
				echo "
					<tr align='center' id='modsorline$i' class='sor".($i%2)."'>
						<td class='tdName'>
							<input type='hidden' name='modSorID".$i."' value='-1' />
							<input type='hidden' name='modSorDel".$i."' value='0' />
							<input type='hidden' name='modSorNyito".$i."' value='0' />
							<input type='hidden' name='modSorSID".$i."' value='-1' />
							<input type='text' $ac0 name='modSorNev".$i."' class='inputText' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 0);modSorKijeloles($i);' />
						</td>
						<td class='td1'><input type='text' class='inputTextSmall' id='secureMnyito".$i."' value='0' disabled='disabled' /></td>
						<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorVetel".$i."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 1);modSorKijeloles($i);' /></td>
						<td class='td1'><span id='modSorOssz".$i."'>0</span></td>
						<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorMarad".$i."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 2);modSorKijeloles($i);' /></td>
						<td class='td1'><span id='modSorFogy".$i."'>0</span></td>
						<td class='td1'><input type='text' $ac0 class='inputTextSmall' name='modSorAr".$i."' onblur='modvetelnull($i);modosithatoKeyEvent(".$i.");' onfocus='setCo($co, $i, 3);modSorKijeloles($i);' /></td>
						<td class='td1'><span id='modSorErtek".$i."'>0</span></td>
						<td class='td3'></td>
					</tr>
				";
				$i++;
			}
			
			$co++;
		
		}
		
		//TABLE LEZÁRÁS
		echo "
				<tr>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td class='td2'></td>
					<td><br /></td>
				</tr>
			</table>
		";
		return $co;
	}
	
	function listLottoStand($disabled, $co) {
		
		//Lotto stand
		if ($_SESSION['pub']->isLotto == 1) {
			
			//legyen-e hozzáadó gomb
			if ($_GET['mod'] == 1) {
				$addImg = "";
			} else {
				$addImg = "<img src='img/add.png' width='15' height='15' onclick='addTrToLot();' alt='Sor hozzáadása' />";
			}
			
			//LOTTO TERMINÁL TÁBLA
			echo "
				<table border='0' width='700' cellpadding='0' cellspacing='0' id='lottoTbl'>
					
					<tr align='center' class='osszesitoSor'>
						<td class='td1'>
							$addImg <b>Nap</b>
						</td>
						
						<td class='td1'>
							<b>Nettó forgalom</b>
						</td>
						
						<td class='td1'>
							<b>Összesen</b>
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
			";
			
			//LEKÉRDEZÉS
			$query = mysql_query("SELECT * FROM `LottoStand` WHERE `SID` = ".$_GET['id'].";");
			$i = 0;
			while ($result = mysql_fetch_assoc($query)) {
				echo "
					
					<tr align='center' class='lottoSor$i' id='sor".($i%2)."'>
						<td class='td1'>
							".$result['Nap'].".
							<input type='hidden' name='lid[$i]' value='".$result['ID']."' />
						</td>
						
						<td class='td1'>
							<input $disabled class='inputText' autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' name='LotNet[$i]' id='lotNet$i' value='".$result['NetForg']."' onblur='countLotPer();' />
						</td>
						
						<td class='td1'>
							<input $disabled class='inputText' autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='LotAll[$i]' id='lotAll$i' value='".$result['Ossz']."' onblur='countLotAll();' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
				";
				$i++;
			}
			
			//HA NINCS TALÁLAT
			if ($i == 0) {
				echo "
					<tr align='center' class='lottoSor$i' id='sor".($i%2)."'>
						<td class='td1'>
							1.
							<input type='hidden' name='lid[0]' value='-1' />
						</td>
						
						<td class='td1'>
							<input $disabled autocomplete='off' class='inputText' onfocus='setCo($co, 0, 0);' type='text' name='LotNet[0]' id='lotNet0' value='0' onblur='countLotPer();' />
						</td>
						
						<td class='td1'>
							<input $disabled autocomplete='off' class='inputText' onfocus='setCo($co, 0, 1);' type='text' name='LotAll[0]' id='lotAll0' value='0' onblur='countLotAll();' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
				";
			}
			
			//tábla zárás
			echo "
					<tr align='center'>
						<td class='td1'>
							<b>Lottó pénz:</b>
						</td>
						
						<td class='td1' colspan='2' align='right'>
							<input $disabled class='inputText' autocomplete='off' type='text' id='lottoAllI' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='3'>
							
						</td>
						
						<td >
							<br />
						</td>
					</tr>
				</table>
				
			";
			$co++;
		}
		return $co;
	}
	
	function listKiadasok($disabled, $co, $standHead, $pubOptions) {
		
		//ÚJ BEVÉTEL KIADÁS TÁBLA
		if (isset($pubOptions['ujKiadas']) && $pubOptions['ujKiadas'] == 1) {
			
			//LEgyen-e kiadás sor hozzáadó gomb
			if ($_GET['mod'] == 0) {
				$addbImg = "<img src='img/add.png' width='15' height='15' onclick='newBevetLine();' alt='Sor hozzáadása' />";
				$addkImg = "<img src='img/add.png' width='15' height='15' onclick='newKiadLine();' alt='Sor hozzáadása' />";
			} else {
				$addImg = "";
			}
			
			//bevételezés tábla
			echo "
				<table border='0' width='700' cellpadding='0' cellspacing='0' id='bevetelezesTbl'>
			
					<tr align='center' class='osszesitoSor'>
						<td class='td1'>
							$addbImg <b>Bevételezések</b>
						</td>
						
						<td class='td1'>
							<b>Ár</b>
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
			";
			
			//létező bevételezések lekérése
			$query = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`SID` = ".$_GET['id']." AND `kiadasok`.`ertek` >= 0 ORDER BY `ID` ASC;");
			$lottoAr = 0;
			$i = 0;
			while ($result = mysql_fetch_assoc($query)) {
			
				echo "
					<tr align='center'>
						<td class='td1'>
							<input $disabled class='inputText' autocomplete='off' onfocus='setCo($co, $i, 0);' onblur='bevetelFocusEvent($i);' type='text' name='bevetelNev[$i]' id='bevetelNev$i' value='".$result['nev']."' />
							<input type='hidden' name='bid[$i]' value='".$result['ID']."' />
						</td>
						
						<td class='td1'>
							<input class='inputText' $disabled autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='bevetelAr[$i]' id='bevetelAr$i' onblur='countBevetAll();bevetelFocusEvent($i);' value='".$result['ertek']."' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
				";
			
				$i++;
				
			}
			
			//ha nem volt találat, 10 kezdő sor
			if ($i == 0) {
			
				//10 sor generálása
				$f = 0;
				$f = $i + 10;
				for ($i = $i; $i < $f; $i++) {

					echo "
						<tr align='center'>
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' class='inputText' $disabled name='bevetelNev[$i]' id='bevetelNev$i' onblur='bevetelFocusEvent($i);' />
								<input type='hidden' name='bid[$i]' value='-1' />
							</td>
							
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='bevetelAr[$i]' class='inputText' $disabled id='bevetelAr$i' onblur='countBevetAll();bevetelFocusEvent($i);' />
							</td>
							
							<td class='td3'>
								
							</td>
						</tr>
					";
					
				}
				
			}
			
			//KIADÁSOK RÉSZ
			$co++;
			echo "
				<tr align='center' id='BevetelOsszTr'>
					<td class='td1'>
						<b>Összesen</b>
					</td>
					
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='bevetelOssz' onblur='countBevetAll()' value='0' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center' class='osszesitoSor'>
					<td class='td1'>
						$addkImg <b>Kiadások</b>
					</td>
					
					<td class='td1'>
						<b>Ár</b>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
			
			//Fix sorok lekérdezése
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID." AND `pubOptions`.`option` LIKE 'fixKiadas';");
			$i = 0;
			$f = 0;
			if ($standHead['finished'] == 0) {
				while ($result = mysql_fetch_assoc($query)) {

					echo "
						<tr align='center'>
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' class='inputText' $disabled name='kiadasNev[$i]' id='kiadasNev$i' onblur='kiadasFocusEvent($i);' value='".$result['value']."' />
								<input type='hidden' name='kid[$i]' value='-1' />
							</td>
							
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' class='inputText' $disabled id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' />
							</td>
							
							<td class='td3'>
							
							</td>
						</tr>
					";
					
					$i++;
					$f++;
				}
			}
			
			//KÖZTÉS SOROK LEKÉRÉSE
			$query = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`SID` = ".$_GET['id']." AND `kiadasok`.`ertek` < 0 ORDER BY `ID` ASC;");
			$lottoAr = 0;
			while ($result = mysql_fetch_assoc($query)) {
				
				//HA LOTTO 1% sor
				if ($result['nev'] == "Lottó 1%") {
					
					$lottoAr = $result['ertek'];
					
				} else {
					echo "
						<tr align='center'>
							<td class='td1'>
								<input $disabled class='inputText' autocomplete='off' onfocus='setCo($co, $i, 0);' onblur='kiadasFocusEvent($i);' type='text' name='kiadasNev[$i]' id='kiadasNev$i' value='".$result['nev']."' />
								<input type='hidden' name='kid[$i]' value='".$result['ID']."' />
							</td>
							
							<td class='td1'>
								<input class='inputText' $disabled autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' value='".($result['ertek']*-1)."' />
							</td>
							
							<td class='td3'>
								
							</td>
						</tr>
					";
				}
				$i++;
				
			}
			
			//ha nem volt találat, 10 kezdő sor
			if ($i == 0 || ($i != 0 && $f != 0)) {
			
				//10 sor generálása
				$f = 0;
				$f = $i + 10;
				for ($i = $i; $i < $f; $i++) {

					echo "
						<tr align='center'>
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' class='inputText' $disabled name='kiadasNev[$i]' id='kiadasNev$i' onblur='kiadasFocusEvent($i);' />
								<input type='hidden' name='kid[$i]' value='-1' />
							</td>
							
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' class='inputText' $disabled id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' />
							</td>
							
							<td class='td3'>
								
							</td>
						</tr>
					";
					
				}
				
			}
			
			//Lotto 1% sor beillesztés
			if ($_SESSION['pub']->isLotto == 1) {
				
				echo "
					<tr align='center' id='lottoPercTr'>
						<td class='td1'>
							Lottó 1%
							<input type='hidden' name='lottoPercid' value='".$result['ID']."' />
						</td>
						
						<td class='td1'>
							<input autocomplete='off' type='text' name='lottoPerc0' class='inputText' $disabled id='lottoPerc' value='".$lottoAr."' onblur='countKiadAll();' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
				";
			}
			
			//TABLE lezárás
			echo "
					<tr align='center' id='KiadasOsszTr'>
						<td class='td1'>
							<b>Összesen</b>
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='KiadasOssz' onblur='countKiadAll()' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='2'>
							
						</td>
						
						<td>
							<br />
						</td>
					</tr>
				</table>
			";
		
		//RÉGI KIADÁS TÁBLA
		} else {
			
			//LEgyen-e kiadás sor hozzáadó gomb
			if ($_GET['mod'] == 0) {
				$addImg = "<img src='img/add.png' width='15' height='15' onclick='addRowToStand();' alt='Sor hozzáadása' />";
			} else {
				$addImg = "";
			}
			
			//KIADÁS TABLE
			echo "
				<table border='0' width='700' cellpadding='0' cellspacing='0' id='kiadasTbl'>
			
					<tr align='center' class='osszesitoSor'>
						<td class='td1'>
							$addImg <b>Kiadások</b>
						</td>
						
						<td class='td1'>
							<b>Ár</b>
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
			";
			
			//Fix sorok lekérdezése
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID." AND `pubOptions`.`option` LIKE 'fixKiadas';");
			$i = 0;
			$f = 0;
			if ($standHead['finished'] == 0) {
				while ($result = mysql_fetch_assoc($query)) {

					echo "
						<tr align='center'>
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' class='inputText' $disabled name='kiadasNev[$i]' id='kiadasNev$i' onblur='kiadasFocusEvent($i);' value='".$result['value']."' />
								<input type='hidden' name='kid[$i]' value='-1' />
							</td>
							
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' class='inputText' $disabled id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' />
							</td>
							
							<td class='td3'>
							
							</td>
						</tr>
					";
					
					$i++;
					$f++;
				}
			}
			
			//KÖZTÉS SOROK LEKÉRÉSE
			$query = mysql_query("SELECT * FROM `kiadasok` WHERE `kiadasok`.`SID` = ".$_GET['id']." ORDER BY `ID` ASC;");
			$lottoAr = 0;
			while ($result = mysql_fetch_assoc($query)) {
				
				//HA LOTTO 1% sor
				if ($result['nev'] == "Lottó 1%") {
					
					$lottoAr = $result['ertek'];
					
				} else {
					echo "
						<tr align='center'>
							<td class='td1'>
								<input $disabled class='inputText' autocomplete='off' onfocus='setCo($co, $i, 0);' onblur='kiadasFocusEvent($i);' type='text' name='kiadasNev[$i]' id='kiadasNev$i' value='".$result['nev']."' />
								<input type='hidden' name='kid[$i]' value='".$result['ID']."' />
							</td>
							
							<td class='td1'>
								<input class='inputText' $disabled autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' value='".$result['ertek']."' />
							</td>
							
							<td class='td3'>
								
							</td>
						</tr>
					";
				}
				$i++;
				
			}
			
			//ha nem volt találat, 10 kezdő sor
			if ($i == 0 || ($i != 0 && $f != 0)) {
			
				//10 sor generálása
				$f = 0;
				$f = $i + 10;
				for ($i = $i; $i < $f; $i++) {

					echo "
						<tr align='center'>
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' class='inputText' $disabled name='kiadasNev[$i]' id='kiadasNev$i' onblur='kiadasFocusEvent($i);' />
								<input type='hidden' name='kid[$i]' value='-1' />
							</td>
							
							<td class='td1'>
								<input autocomplete='off' onfocus='setCo($co, $i, 1);' type='text' name='kiadasAr[$i]' class='inputText' $disabled id='kiadasAr$i' onblur='countKiadAll();kiadasFocusEvent($i);' />
							</td>
							
							<td class='td3'>
								
							</td>
						</tr>
					";
					
				}
				
			}
			
			//Lotto 1% sor beillesztés
			if ($_SESSION['pub']->isLotto == 1) {
				
				echo "
					<tr align='center' id='lottoPercTr'>
						<td class='td1'>
							Lottó 1%
							<input type='hidden' name='lottoPercid' value='".$result['ID']."' />
						</td>
						
						<td class='td1'>
							<input autocomplete='off' type='text' name='lottoPerc0' class='inputText' $disabled id='lottoPerc' value='".$lottoAr."' onblur='countKiadAll();' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
				";
			}
			
			//TABLE lezárás
			echo "
					<tr align='center' id='KiadasOsszTr'>
						<td class='td1'>
							<b>Összesen</b>
						</td>
						
						<td class='td1'>
							<input class='inputText' $disabled autocomplete='off' type='text' name='KiadasOssz' onblur='countKiadAll()' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='2'>
							
						</td>
						
						<td>
							<br />
						</td>
					</tr>
				</table>
				
			";
			
		}
		
		return ++$co;
	}
	
	function listSajatFogyasztas($disabled, $co, $pubOptions) {
		if (isset($pubOptions['sajatFogyasztas']) && $pubOptions['sajatFogyasztas'] == 1) {
			
			//lekérjük a neveket
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `option` LIKE 'sajatFogyasztasNev' AND `PID` = ".$_SESSION['pub']->ID.";");
			$i = 0;
			while ($result = mysql_fetch_assoc($query)) {
				
				//táblázat nyitó
				if ($i == 0) {
					
					//bevételezés tábla
					echo "
						<table border='0' width='700' cellpadding='0' cellspacing='0' id='bevetelezesTbl'>
					
							<tr align='center' class='osszesitoSor'>
								<td class='td1' colspan='2'>
									<b>Saját fogyasztás</b>
								</td>
								
								<td class='td3'>
								
								</td>
							</tr>
					";
					
				}
				
				//GET DATA FROM mysql
				$sajatFogyValue = "";
				$squery = mysql_query("SELECT * FROM  `sajatFogyasztas` WHERE  `SID` =".$_GET['id']." AND  `NEVID` =".$result['ID'].";");
				$sresult = mysql_fetch_assoc($squery);
				if (is_array($sresult)) {
					$sajatFogyValue = $sresult['value'];
				}
				
				//köztes sorok
				echo "
					<tr align='center'>
						<td class='td1'>
							".$result['value']."
							<input type='hidden' name='sajatFogyasztasID$i' value='".$result['ID']."' />
						</td>
						
						<td class='td1'>
							<input class='inputText' $disabled autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' name='sajatFogyasztas$i' id='sajatFogyasztas$i' onblur='countSajatFogyAll();sajatFogyFocus($i);' value='".$sajatFogyValue."' />
						</td>
						
						<td class='td3'>
							
						</td>
					</tr>
				";
				
				$i++;
			}
			
			//TABLE lezárás
			if ($i != 0) {
				echo "
						<tr align='center' id='SajatFogyOsszTr'>
							<td class='td1'>
								<b>Összesen</b>
							</td>
							
							<td class='td1'>
								<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='sajatFogyOssz' onblur='' value='0' />
							</td>
							
							<td class='td3'>
							
							</td>
						</tr>
						
						<tr align='center'>
							<td class='td2' colspan='2'>
								
							</td>
							
							<td>
								<br />
							</td>
						</tr>
					</table>
				";
			}
			
		}
		
		//csak akkor növelje a coordinátát ha volt sor
		if ($i != 0) {
			$co++;
		}
		
		return $co;
	}
	
	function listChatbox($pubOptions, $disabled, $co) {
		
		//üzenőfal
		if (isset($pubOptions['chatbox']) && $pubOptions['chatbox'] == 1) {
			
			
			//JELENLEGI STANDHOZ TARTOZÓ LEKÉRÉSE
			$query = mysql_query("SELECT * FROM `messages` WHERE `SID` = ".$_GET['id'].";");
			$chatboxId = mysql_fetch_assoc($query);
			
			//érték beállítások
			if ($chatboxId == FALSE) {
				$chatboxId['ID'] = -1;
				$chatboxId['message'] = "";
			}
			
			echo "
				<p class='anchor2'>Üzenöfal</p>
				<input type='hidden' id='chatboxID' name='chatboxID' value='".$chatboxId['ID']."' />
				<textarea id='chatbox' name='chatboxText' style='width:694px;height:200px;' onblur='chatboxKeyEvent();' onfocus='setCo($co, 0, 0);'>".str_replace("[br]", "\n", $chatboxId['message'])."</textarea><br /><br />
			";
			
			$co++;
			
		}
		
		return $co;
	}
	
	function listEtelFogy($disabled, $standHead, $co) {
		
		$enddate = date("Y-m-d", strtotime($standHead['date']));
		
		//étel forgalom
		echo "
			<table border='0' width='700' cellpadding='0' cellspacing='0'>
				
				<tr align='center' class='osszesitoSor'>
					<td class='td1' colspan='2'>
						<b>Ételforgalom</b>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
		";
		
		//köztes sorok generálása
		$j = 0;
		for ($i = $standHead['wtime']-1; $i >= 0; $i--) {
			
			$tmpday = date("l",strtotime("-$i days", strtotime($enddate)));
			switch ($tmpday) {
				case 'Monday':
					$tmpday = "Hétfő";
					break;
				case 'Tuesday':
					$tmpday = "Kedd";
					break;
				case 'Wednesday':
					$tmpday = "Szerda";
					break;
				case 'Thursday':
					$tmpday = "Csütörtök";
					break;
				case 'Friday':
					$tmpday = "Péntek";
					break;
				case 'Saturday':
					$tmpday = "Szombat";
					break;
				case 'Sunday':
					$tmpday = "Vasárnap";
					break;
			}
			$databaseDate = date("Y-m-d",strtotime("-$i days", strtotime($enddate)));
			
			//Lekérjük az adatbázisból van-e már ehhez tartozó sor
			$etelfogyval = "";
			$equery = mysql_query("SELECT * FROM  `etelFogyasztas` WHERE  `nap` =  '".$databaseDate."' AND  `SID` =".$_GET['id'].";");
			$eresult = mysql_fetch_assoc($equery);
			if (is_array($eresult)) {
				$etelfogyval = $eresult['fogyas'];
			}
			
			echo "
				<tr align='center'>
					<td class='td1'>
						Ételforgalom ($tmpday)
						<input type='hidden' name='etelforgalomdate$j' value='".$databaseDate."' />
					</td>
					
					<td class='td1'>
						<input class='inputText' $disabled autocomplete='off' type='text' name='etelforgalom$j' onfocus='setCo($co, $j, 0);' onblur='countEtelFogyAll();' value='".$etelfogyval."' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
			
			$j++;
		}
		echo "
			<tr align='center' id='etelFogyOsszTr'>
				<td class='td1'>
					<b>Összesen</b>
				</td>
				
				<td class='td1'>
					<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='etelFogyOssz' onblur='' value='0' />
				</td>
				
				<td class='td3'>
				
				</td>
			</tr>
			
			<tr align='center'>
				<td class='td2' colspan='2'>
					
				</td>
				
				<td>
					<br />
				</td>
			</tr>
		</table>";
		
		
		return ++$co;
	}
	
	function listEgyebForgalom($disabled, $co) {
		//lekérjük a neveket
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `option` LIKE 'egyebForgalomNev' AND `PID` = ".$_SESSION['pub']->ID.";");
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//táblázat nyitó
			if ($i == 0) {
				
				//bevételezés tábla
				echo "
					<table border='0' width='700' cellpadding='0' cellspacing='0' id='bevetelezesTbl'>
				
						<tr align='center' class='osszesitoSor'>
							<td class='td1' colspan='2'>
								<b>Egyéb forgalom</b>
							</td>
							
							<td class='td3'>
							
							</td>
						</tr>
				";
				
			}
			
			//GET DATA FROM mysql
			$sajatFogyValue = "";
			$squery = mysql_query("SELECT * FROM  `egyebForgalom` WHERE  `SID` =".$_GET['id']." AND  `EID` =".$result['ID'].";");
			$sresult = mysql_fetch_assoc($squery);
			if (is_array($sresult)) {
				$egyebForgalomValue = $sresult['value'];
			}
			
			//köztes sorok
			echo "
				<tr align='center'>
					<td class='td1'>
						".$result['value']."
						<input type='hidden' name='egyebForgalomID$i' value='".$result['ID']."' />
					</td>
					
					<td class='td1'>
						<input class='inputText' $disabled autocomplete='off' onfocus='setCo($co, $i, 0);' type='text' name='egyebForgalom$i' id='egyebForgalom$i' onblur='countEgyebForgalomAll();' value='".$egyebForgalomValue."' />
					</td>
					
					<td class='td3'>
						
					</td>
				</tr>
			";
			
			$i++;
		}
		
		//TABLE lezárás
		if ($i != 0) {
			echo "
					<tr align='center' id='egyebForgalomOsszTr'>
						<td class='td1'>
							<b>Összesen</b>
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='egyebForgalomOssz' onblur='' value='0' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='2'>
							
						</td>
						
						<td>
							<br />
						</td>
					</tr>
				</table>
			";
		}
		
		//csak akkor növelje a coordinátát ha volt sor
		if ($i != 0) {
			$co++;
		}
		
		return $co;
		
	}
	
	function listCardPay($disabled, $co) {
		
		$cardval = "";
		$query = mysql_query("SELECT * FROM `bankkartyasFizetes` WHERE `SID` = ".$_GET['id'].";");
		$result = mysql_fetch_assoc($query);
		
		if (is_array($result)) {
			$cardval = $result['value'];
		}
		
		//bankkártya forgalom
		echo "
			<table border='0' width='700' cellpadding='0' cellspacing='0'>
				
				<tr align='center' class='osszesitoSor'>
					<td class='td1' colspan='2'>
						<b>Kártyaforgalom</b>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center'>
					<td class='td1'>
						Bankkártya
					</td>
					
					<td class='td1'>
						<input class='inputText' $disabled autocomplete='off' type='text' name='kartyaForgalom' onfocus='setCo($co, 0, 0);' onblur='countFinalForgalom();' value='".$cardval."' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center'>
					<td class='td2' colspan='2'>
						
					</td>
					
					<td>
						<br />
					</td>
				</tr>
			</table>
		";
		
		return ++$co;
	}
	
	function listOsszesito($pubOptions, $disabled) {
		
		//összesítő táblázat
		echo "
			<table border='0' width='700' cellpadding='0' cellspacing='0'>
				
				<tr align='center' class='osszesitoSor'>
					<td class='td1' colspan='2'>
						<b>Forgalom</b>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
		";
		
		//ha van saját fogyasztás külön saját forgalom vendég forgalom
		if (isset($pubOptions['sajatFogyasztas']) && $pubOptions['sajatFogyasztas'] == 1) {
			
			echo "
					<tr align='center'>
						<td class='td1'>
							Vendég forgalom
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalForg' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
			";
			
		//ha nincs alap össz forgalom
		} else {
			
			echo "
					<tr align='center'>
						<td class='td1'>
							Forgalom összes
						</td>
						
						<td class='td1'>
							<input type='hidden' name='forgalom2save' />
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalForg' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
			";
			
		}
		
		//ételfogyasztas
		if (isset($pubOptions['etelFogyasztas']) && $pubOptions['etelFogyasztas'] == 1) {
			echo "
				<tr align='center'>
					<td class='td1'>
						Étel forgalom
					</td>
					
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalEtelForg' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
		}
		
		//egyéb forgalom
		if (isset($pubOptions['egyebForgalom']) && $pubOptions['egyebForgalom'] == 1) {
			echo "
				<tr align='center'>
					<td class='td1'>
						Egyéb forgalom
					</td>
					
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalEgyebForgalom' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
		}
		
		//összes fogyasztás
		if (isset($pubOptions['sajatFogyasztas']) && $pubOptions['sajatFogyasztas'] == 1) {
			
			echo "
					<tr align='center'>
						<td class='td1'>
							<b>Összes forgalom</b>
						</td>
						
						<td class='td1'>
							<input type='hidden' name='forgalom2save' />
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalOsszForg' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
			";
			
		}
		
		//bevételezés
		if (isset($pubOptions['ujKiadas']) && $pubOptions['ujKiadas'] == 1) {
			echo "
				<tr align='center'>
					<td class='td1'>
						Bevételezés
					</td>
					
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalBevet' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
		}
		
		//kiadások
		echo "
			<tr align='center'>
				<td class='td1'>
					Kiadás
				</td>
				
				<td class='td1'>
					<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalKiad' />
				</td>
				
				<td class='td3'>
				
				</td>
			</tr>
		";
		
		//LOTTÓ ÖSSZESÍTŐ
		if ($_SESSION['pub']->isLotto == 1) {
			echo "
				<tr align='center'>
					<td class='td1'>
						Lottó
					</td>
					
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalLotto' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
			";
		}
		
		//JUTALÉK SZÁMOLÓ
		if (isset($pubOptions['jutalek']) && $pubOptions['jutalek'] != 0) {
			echo "
				<tr align='center'>
					<td class='td1'>
						Jutalék (".$pubOptions['jutalek']."%)
						<input type='hidden' id='hiddenJutalek' value='".$pubOptions['jutalek']."' />
					</td>
				
					<td class='td1'>
						<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='finalJutalek' value='0' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>";
		}
		
		//KÁRTYAFORGALOM 
		if (isset($pubOptions['bankkartya']) && $pubOptions['bankkartya'] == 1) {
			
			echo "
					<tr align='center'>
						<td class='td1'>
							Összes leadó
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='kartyaLeado' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td1'>
							<b>KP Leadó</b>
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='leado' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='2'>
							
						</td>
						
						<td>
							<br />
						</td>
					</tr>
				</table>
			";
			
		//ALAP LEADÓ ALJ
		} else {
			echo "
					<tr align='center'>
						<td class='td1'>
							<b>Leadó</b>
						</td>
						
						<td class='td1'>
							<input class='inputText' disabled='disabled' autocomplete='off' type='text' name='leado' />
						</td>
						
						<td class='td3'>
						
						</td>
					</tr>
					
					<tr align='center'>
						<td class='td2' colspan='2'>
							
						</td>
						
						<td>
							<br />
						</td>
					</tr>
				</table>
			";
		}
		
	}
	
	function borravaloSzamoloTabla($disabled, $co) {
		
		//borravaló lekérése
		$query = mysql_query("
			SELECT 
			
			`borravalo`.`ID`,
			`borravalo`.`ar`
			
			FROM `borravalo`
			WHERE `borravalo`.`SID` = ".$_GET['id'].";
		");
		
		if (!($borravalo = mysql_fetch_assoc($query))) {
			$borravalo['ar'] = "";
			$borravalo['ID'] = -1;
		}
		
		echo "
			<table border='0' width='700' cellpadding='0' cellspacing='0'>
				<tr align='center' class='osszesitoSor'>
					<td class='td1' colspan='2'>
						<b>Borravaló számolás</b>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center'>
					<td class='td1'>
						Kézpénz
					</td>
					
					<td class='td1'>
						<input class='inputText' $disabled autocomplete='off' type='text' name='kp' onblur='borravaloSzamolas(0);' onfocus='setCo($co, 0, 0);' />
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center'>
					<td class='td1'>
						Borravaló
					</td>
					
					<td class='td1'>
						<input type='hidden' name='bid' value='".$borravalo['ID']."' />
						<input type='text' class='inputText' name='borravalo' value='".$borravalo['ar']."' autocomplete='off' onblur='borravaloSzamolas(1);' onfocus='setCo($co, 1, 0);' $disabled /> 
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr align='center'>
					<td class='td2' colspan='2'>
						
					</td>
					
					<td>
						<br />
					</td>
				</tr>
			</table>
		";
	}
	
	function saveButton($pubOptions, $standHead, $mod) {
		
		//ha van standlapEllenőrzés akkor dobja be a checkboxot
		if (isset($pubOptions['standEllenorzes']) && $pubOptions['standEllenorzes'] == 1 && $_SESSION['tether'] > 0) {
			
			//lekérjük le lett-e már ellenőrizve
			$equery = mysql_query("SELECT * FROM `standEllenorizve` WHERE `SID` =".$_GET['id'].";");
			$eresult = mysql_fetch_assoc($equery);
			
			if (is_array($eresult)) {
				echo "<input type='checkbox' name='standEllenorizve' value='1' checked='checked' /> Ellenőrizve";
			} else {
				echo "<input type='checkbox' name='standEllenorizve' value='1' /> Ellenőrizve";
			}
			
		}
		echo "</form>";
		
		//legyen e mentés // sorsjegy gomb
		if ($mod == 0) {
			
			//ha van sorsjegy
			if (isset($pubOptions['sorsjegy']) && $pubOptions['sorsjegy'] == 1 && $standHead['finished'] == 0) {
				echo "<button onclick='sorsjegyOsszesit();'>Összesítés sorsjeggyel!</button>";
			
			//ha nincs sorsjegy
			} else {
				echo "<button onclick='sendElszam(".$_SESSION['pub']->isLotto.")'>Mentés</button>";
			}
			
		}
	}
	
	//ELSZÁMOLÁS MÓDOSÍTÁS
	function ElszamMod($id) {
		
		//Akkor tölt be ha minden getes adat rendesen meg van adva
		if (isset($_GET['id']) && $_GET['id'] != "" && isset($_GET['mod']) && $_GET['mod'] != "") {
		
			//módosíthatóság
			if ($_GET['mod'] == 1) {
			
				$disabled = "disabled='disabled'";
			
			} else {
				
				$disabled = "";
				
			}
			
			include("conf/mysql.php");
			
			//MYSQL SERVER
			$kapcsolat = mysql_connect($szerver, $user, $pass);
			mysql_set_charset('utf8',$kapcsolat);
			if (!$kapcsolat ) {
				die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
			}
			mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
			
			//MÉRTÉKEGYSÉG QUERY
			$mertekegyseg = array();
			$query = mysql_query("SELECT * FROM `Mert`;");
			while ($result = mysql_fetch_assoc($query)) {
				$mertekegyseg[trim($result['ID'])] = $result['egyseg'];
			}
			
			//kocsma beállításainak betöltése
			$pubOptions = array();
			$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID.";");
			while ($result = mysql_fetch_assoc($query)) {
				$pubOptions[$result['option']] = $result['value'];
			}
			
			//stand fejléc lekérése
			$query = mysql_query("select * from `standok` where `standok`.`ID` = ".$_GET['id'].";");
			$standHead = mysql_fetch_assoc($query);
			
			//koordináta beállítás
			$co = 0;
			
			//csak akkor jelenítse meg a standlapot ha még nincs befejezve vagy ha befejezett legalább üzletvezető felhasználó és megtekintésre nyitja
			if (($standHead['finished'] == 0) || ($standHead['finished'] == 1 && $_SESSION['tether'] == 1) || ($standHead['finished'] == 1 && $_SESSION['tether'] > 1)) {
				standHeader($id);
				$co = listAkciosTermekek($disabled, $co);
				$co = listStandlap($disabled, $pubOptions, $mertekegyseg, $co, $standHead['finished']);
				$co = listModLines($pubOptions, $disabled, $co, $standHead['finished']);
				if (isset($pubOptions['etelFogyasztas']) && $pubOptions['etelFogyasztas'] == 1) {
					$co = listEtelFogy($disabled, $standHead, $co);
				}
				if (isset($pubOptions['egyebForgalom']) && $pubOptions['egyebForgalom'] == 1) {
					$co = listEgyebForgalom($disabled, $co);
				}
				$co = listLottoStand($disabled, $co);
				$co = listSajatFogyasztas($disabled, $co, $pubOptions);
				if (isset($pubOptions['bankkartya']) && $pubOptions['bankkartya'] == 1) {
					$co = listCardPay($disabled, $co);
				}
				$co = listKiadasok($disabled, $co, $standHead, $pubOptions);
				$co = listChatbox($pubOptions, $disabled, $co);
				listOsszesito($pubOptions, $disabled);
				$co = borravaloSzamoloTabla($disabled, $co);
				saveButton($pubOptions, $standHead, $_GET['mod']);
			
			//nincs jog a megnyitáshoz
			} else {
				echo "<b>Nincs jogosultságod a lap megtekintéséhez!</b>";
			}
			
			//MXSQL CLOSE
			mysql_close($kapcsolat);
		
		//stand fejlécében hiba
		} else {
			
			echo "<b>Valami hiba történt a lap megnyitása közben!<br />Kérlek lépj ki a főoldalra és próbáld meg újra!</b>";
			
		}
		
	}

?>