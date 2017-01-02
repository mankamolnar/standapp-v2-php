<?php

	//STATISZTIKA megjelenítés
	function statisztika() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//kocsma beállításainak betöltése
		$pubOptions = array();
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID.";");
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result['value'];
		}
		
		//Kiválasztás
		if (!isset($_POST['type'])) {
			
			//form a statisztikához
			echo "
				<input type='hidden' id='pid' value='".$_SESSION['pub']->ID."' />
				<form action='index.php?page=16' id='statForm' method='post'>

					<p class='anchor2'>
						<input type='radio' name='type' value='1' checked='checked' onclick=\"chooseStatForm('kocsmaOsszehasonlitas')\" /> Kocsmák összehasonlítása | 
						<input type='radio' name='type' value='2' onclick=\"chooseStatForm('italforgalomAdottHonapban')\" /> Italforgalom összehasonlítása | 
						<input type='radio' name='type' value='3' onclick=\"chooseStatForm('pultosForgalomAdottHonapban')\" /> Alkalmazottak összehasonlítása  | 
						<input type='radio' name='type' value='4' onclick=\"chooseStatForm('elmult1evforgalom')\" /> Elmúlt 1 év forgalomváltozásai | <br />
						<input type='radio' name='type' value='5' onclick=\"chooseStatForm('bevetelezesLekeres')\" /> Termék bevételezés lekérése | 
						<input type='radio' name='type' value='6' onclick=\"chooseStatForm('korrigalasLekeres')\" /> Korrigálások lekérése |  
					</p>
					
					<hr />
					
					<p id='formTags'>
						
						<b>Kezdő dátum:</b> <input type='text' name='startDate' /><br /><br />
						<b>Hány hónapon keresztül?</b> <input type='text' name='dateLength' /><br /><br />
						<input type='submit' value='Statisztika elkészítése!' />
						
					</p>
				</form>
			";
			
		}
		
		//statisztikához lekérések
		if (isset($_POST['type'])) {
			
			//type hidden kiír
			echo "<input type='hidden' id='type' value='".$_POST['type']."' />";
			
			//KOCSMA ÖSSZEHASONLÍTÁS
			if ($_POST['type'] == 1) {
				
				//KOCSMÁK LEKÉRÉSE
				$pubs = array();
				$pubname = array();
				$query = mysql_query("SELECT `standok`.`PID`, (SELECT `pub`.`name` FROM `pub` WHERE `pub`.`ID` = `standok`.`PID`) as 'name' FROM `standok` WHERE `standok`.`UID` = ".$_SESSION['ID']." GROUP BY `standok`.`PID`;");
				$i = 0;
				while($result = mysql_fetch_assoc($query)) {
					
					$pubs[$i] = $result['PID'];
					$pubname[$i] = $result['name'];
					$i++;
					
				}
				
				//bevétel, kiadások, lottók hiddenekbe töltése
				$k = 0;
				for ($i = 0; $i < count($pubs); $i++) {
					for ($j = 0; $j < $_POST['dateLength']; $j++) {
						
						$start = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 0, date("Y", strtotime($_POST['startDate'])) ));
						$end = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j + 1, 1, date("Y", strtotime($_POST['startDate'])) ));
						$thismonth = date('Y-m', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 1, date("Y", strtotime($_POST['startDate'])) ));
						
						//lekérdezés
						$query = mysql_query("
							SELECT

							IFNULL(SUM(
							IFNULL((SELECT SUM(`kiadasok`.`ertek`) FROM `kiadasok` WHERE `kiadasok`.`SID` = `standok`.`ID`), 0) +
							IFNULL((SELECT SUM(`stand`.`fogyas` * `stand`.`price`) FROM `stand` WHERE `stand`.`SID` = `standok`.`ID`), 0) +
							IFNULL((SELECT SUM(`LottoStand`.`Ossz`) FROM `LottoStand` WHERE `LottoStand`.`SID` = `standok`.`ID`), 0)
							), 0) as 'leado'

							FROM `standok`
							WHERE `standok`.`PID` = ".$pubs[$i]."
							AND `standok`.`date` BETWEEN '$start' AND '$end';
						");
						
						$result = mysql_fetch_assoc($query);
						
						//HIDDENEK ELHELYEZÉSE
						echo "
							<input type='hidden' id='pub$k' value='".$pubname[$i]."' />
							<input type='hidden' id='month$k' value='$thismonth' />
							<input type='hidden' id='leado$k' value='".$result['leado']."' />
						";
						
						$k++;
						
					}
				}
				
				//Container létrehozás
				echo '<div id="chartdiv" style="height:500px;width:1000px; "></div>';
				
			}
			
			//ITALFORGALOM
			if ($_POST['type'] == 2) {
				
				if ($_POST['dateLength'] == "") {
					$_POST['dateLength'] = 1;
				}
				
				//Hiddenek kiírása
				$i = 0;
				$k = 0;
				for ($j = 0; $j < $_POST['dateLength']; $j++) {
					
					//IDŐSZAK
					$start = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 0, date("Y", strtotime($_POST['startDate'])) ));
					$end = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j + 1, 1, date("Y", strtotime($_POST['startDate'])) ));
					$thismonth = date('Y-m', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 1, date("Y", strtotime($_POST['startDate'])) ));
					
					//HA ÚJ forgalom számolás van
					if (isset($pubOptions['ujforgSzamol']) && $pubOptions['ujforgSzamol'] == 1) {
						$query = mysql_query("
							SELECT
							`inner2`.`SID`,
							`inner2`.`date`,
							`inner2`.`wtime`,
							`inner2`.`UID`,
							`inner2`.`SSID`,
							`inner2`.`nyito`,
							`inner2`.`vetel`,
							`inner2`.`fogyas`,
							`inner2`.`price`,
							`inner2`.`DID`,
							`inner2`.`forditott`,
							`inner2`.`pmaradvany`,
							`inner2`.`pvetel`,
							`inner2`.`drinkname`

							FROM (
								SELECT
								`inner`.`SID`,
								`inner`.`date`,
								`inner`.`wtime`,
								`inner`.`UID`,
								`inner`.`SSID`,
								`inner`.`nyito`,
								`inner`.`vetel`,
								`inner`.`fogyas`,
								`inner`.`price`,
								`inner`.`DID`,
								`inner`.`forditott`,
								`inner`.`pmaradvany`,
								`inner`.`pvetel`,
								`inner`.`drinkname`

								FROM (
									SELECT
									`standok`.`ID` as `SID`,
									`standok`.`date`,
									`standok`.`wtime`,
									`standok`.`UID`,

									`stand`.`ID` as `SSID`,
									`stand`.`nyito`,
									`stand`.`vetel`,
									`stand`.`fogyas`,
									`stand`.`price`,
									`stand`.`DID`,
									`drinks`.`forditott`,
									`drinks`.`name` as `drinkname`,

									CASE WHEN SUM(`pluszRaktarMaradvany`.`maradvany`) IS NOT NULL
										THEN SUM(`pluszRaktarMaradvany`.`maradvany`)
										ELSE 0
									END AS `pmaradvany`,

									CASE WHEN `pvetel`.`value` IS NOT NULL
										THEN `pvetel`.`value`
										ELSE 0
									END AS `pvetel`

									FROM `stand`

									LEFT JOIN `standok`
									ON `standok`.`ID` = `stand`.`SID`

									LEFT JOIN `pluszRaktarMaradvany`
									ON `pluszRaktarMaradvany`.`SID` = `standok`.`ID`
									AND `pluszRaktarMaradvany`.`DID` = `stand`.`DID`

									LEFT JOIN `pvetel`
									ON `pvetel`.`SID` = `standok`.`ID`
									AND `pvetel`.`DID` = `stand`.`DID`

									LEFT JOIN `drinks`
									ON `drinks`.`ID` = `stand`.`DID`

									WHERE `standok`.`PID` = ".$_SESSION['pub']->ID."
									AND `standok`.`date` BETWEEN '".$start."' AND '".$end."'
									AND `finished` = 1

									GROUP BY `stand`.`ID`
								) as `inner`
							) as `inner2`
							ORDER BY `inner2`.`SID`, `inner2`.`DID` DESC;
						");

						//lekéréshez szükséges propertik
						$index = array();
						$didindex = array();
						$sorok = array();
						$tmp = array(); 
						$finalvalues = array();

						//Rendezzük SID és DID szerint az ömlesztett sorokat
						while ($sor = mysql_fetch_assoc($query)) {
							$sorok[$sor['SID']][$sor['DID']] = $sor;
						}

						//felépítünk egy olyan tömböt amiben eltároljuk csak a standlapok IDját és e-szerint fogunk haladni a kódban!
						$didfelterkepezes = true;
						foreach ($sorok as $SID => $drinks) {
							
							//standlapok ID-ja
							if ($index[count($index)-1] != $SID) {
								$index[] = $SID;
							}

							//italok IDja (did)
							if ($didfelterkepezes) {
								foreach ($drinks as $DID => $values) {
									$didindex[] = $DID;
								}
								$didfelterkepezes = false;
							}

						}

						//Végezze el a számolásokat a sorokon
						foreach ($sorok as $SID => $drinks) {
							foreach ($drinks as $DID => $values) {
								
								//pnyitók lekérdezése
								$talalat = false;
								for ($i = 0; $i < count($index); $i++) {
									if ($values['SID'] == $index[$i]) {
										$talalat = $i;
									}
								}

								//értékeadás pnyitónak
								if ($talalat == 0) {
									$values['pnyito'] = 0;
								} else {
									$values['pnyito'] = $sorok[$index[$talalat-1]][$values['DID']]['pmaradvany'];
								}

								//maradvany számolása
								if ($values['forditott'] == 1) {
									$values['maradvany'] = $values['nyito'] + $values['vetel'] + $values['fogyas'];
								} else {
									$values['maradvany'] = $values['nyito'] + $values['vetel'] - $values['fogyas'];
								}

								//érték kiszámolása
								if ($values['forditott'] == 1) {
									$values['ertek'] = ($values['maradvany'] + $values['pmaradvany'] - ($values['nyito'] + $values['pnyito'] + $values['vetel'] + $values['pvetel']));
								} else {
									$values['ertek'] = ($values['nyito'] + $values['pnyito'] + $values['vetel'] + $values['pvetel'] - $values['maradvany'] - $values['pmaradvany']);
								}

								//CSAK AKKOR AD ÉRTÉKET HA VAN PNYITÓ
								if ($talalat != 0) {

									//finalvalues értékadás ha még nincs
									if (!isset($finalvalues[$values['DID']]['ertek'])) {
										$finalvalues[$values['DID']]['ertek'] = 0;
									}

									$finalvalues[$values['DID']]['name'] = $values['drinkname'];
									$finalvalues[$values['DID']]['fogyas'] += ROUND($values['ertek'], 2);

								}
							}
						}

						//hiddenek generálása
						$namesstr = "";
						$valuesstr = "";
						$ittartok = 0;
						foreach ($finalvalues as $DID => $values) {
							if ($ittartok == count($finalvalues)-1) {
								$namesstr .= $values['name'];
								$valuesstr .= $values['fogyas'];
							} else {
								$namesstr .= $values['name'].";";
								$valuesstr .= $values['fogyas'].";";
							}

							$ittartok++;
						}

						echo "
							<input type='hidden' name='italnevek' value='$namesstr' />
							<input type='hidden' name='italfogyasok' value='$valuesstr' />
						";

					} else {
						//LEKÉRÉS
						$query = mysql_query("
							SELECT  `drinks`.`name`, `drinks`.`ID`,
							
							IFNULL((
								SELECT SUM(  `stand`.`fogyas` ) 
								FROM  `stand` 
								JOIN  `standok`
								ON  `stand`.`SID` =  `standok`.`ID` 
								WHERE  `drinks`.`ID` =  `stand`.`DID`
								AND `standok`.`date` BETWEEN '$start' AND '$end'
							),0) AS  'fogyas'
							FROM  `drinks` 
							WHERE  `drinks`.`PID` =".$_SESSION['pub']->ID.";
						");

						//honapok
						echo "
							<input type='hidden' id='date$i' value='$thismonth' />
						";
						
						//HIDDENEK
						while ($result = mysql_fetch_assoc($query)) {
							
							echo "
								<input type='hidden' id='italid$k' value='".$result['ID']."' />
								<input type='hidden' id='italnev$k' value='".$result['name']."' />
								
								<input type='hidden' id='".$thismonth."_".$result['ID']."' class='fogyas' value='".$result['fogyas']."' />
								
							";
							
							$k++;
						}
						
						$i++;
					}
					
				}
				
				//Container létrehozás
				if (isset($pubOptions['ujforgSzamol']) && $pubOptions['ujforgSzamol'] == 1) {
					echo '<canvas id="chart-right" width="1000" height="500"/>';
				} else {
					echo '<div id="chartdiv" style="height:500px;width:1000px; "></div>';
				}

				//új forgalom modulnak létrehozunk egy hiddent
				echo "<input type='hidden' name='ujforgSzamol' value='".$pubOptions['ujforgSzamol']."' />";
				
			}
		
			//Alkalmazottak
			if ($_POST['type'] == 3) {
				
				//IDŐSZAK
				$j = 0;
				$start = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 0, date("Y", strtotime($_POST['startDate'])) ));
				$end = date('Y-m-d', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j + 1, 1, date("Y", strtotime($_POST['startDate'])) ));
				$thismonth = date('Y-m', mktime(0,0,0, date("n", strtotime($_POST['startDate'])) + $j, 1, date("Y", strtotime($_POST['startDate'])) ));
				
				//LEKÉRDEZÉS
				$query = mysql_query("
					SELECT `standok`.`UID`,

					(SELECT `user`.`uname` FROM `user` WHERE `user`.`ID` = `standok`.`UID`) as 'name',

					SUM(
						IFNULL((SELECT SUM(`stand`.`fogyas` * `stand`.`price`) FROM `stand` WHERE `stand`.`SID` = `standok`.`ID` ), 0) + 
						IFNULL ((SELECT SUM(`LottoStand`.`Ossz`) FROM `LottoStand` WHERE `LottoStand`.`SID` = `standok`.`ID` ), 0) + 
						IFNULL((SELECT SUM(`kiadasok`.`ertek`) FROM `kiadasok` WHERE `kiadasok`.`SID` = `standok`.`ID` ), 0)
					) / sum(`standok`.`wtime`) as 'forgalom' 

					FROM `standok`
					WHERE `standok`.`PID` = ".$_SESSION['pub']->ID." AND 
					`standok`.`date` BETWEEN '$start' AND '$end'
					GROUP BY `standok`.`UID`;
				");
				
				//KAPOT STATISZTIKAI ADATOK TÖMBBE MENTÉSE
				$users = array();
				$i = 0;
				while ($result = mysql_fetch_assoc($query)) {
					
					echo "<input type='hidden' id='user$i' name='user$i' value='".$result['name'].";".$result['forgalom']."'>";
					
					$i++;
				}
				
				//canvas
				echo '
					<h1 class="anchor2">Pultosok napi átlagforgalma</h1><br />
					<div id="chartdiv" style="height:500px;width:1000px; ">
						<canvas id="chart-area" width="1000" height="500"/>
					</div>
				';
			}
			
			//elmúlt 1 év italforgalma
			if ($_POST['type'] == 4) {
				$query = mysql_query("
					SELECT ROUND(sum(`stand`.`fogyas`*`stand`.`price`)) as `_final`, `standok`.*
					FROM  `stand`

					LEFT JOIN `standok`
					ON `stand`.`SID` = `standok`.`ID`

					WHERE  `PID` =".$_SESSION['pub']->ID."
					AND `standok`.`date` BETWEEN '".(date("Y")-1)."-".date("m")."-01 00:00:00' AND '".date("Y-m-d")." 00:00:00'

					GROUP BY YEAR(`standok`.`date`), MONTH(`standok`.`date`)
					ORDER BY `standok`.`date` ASC
				");
				
				//adatok tömbbe
				$records = array();
				while ($result = mysql_fetch_assoc($query)) {
					$records[] = $result;
					
				}
				
				//tömbben a date átírása
				$datestr = "";
				$finalstr = "";
				for ($i = 0;$i < count($records); $i++) {
					$records[$i]["date"] = explode(" ", $records[$i]["date"]);
					$records[$i]["date"] = $records[$i]["date"][0];
					$records[$i]["date"] = explode("-", $records[$i]["date"]);
					$records[$i]["date"] = $records[$i]["date"][0]."-".$records[$i]["date"][1];
					$datestr .= $records[$i]["date"].";";
					$finalstr .= $records[$i]["_final"].";";
				}
				
				echo "<input type='hidden' id='dates' value='$datestr' />";
				echo "<input type='hidden' id='values' value='$finalstr' />";
				
				//canvas
				echo '
					<h1 class="anchor2">Elmúlt 1 év forgalomváltozásai</h1><br />
					<div id="chartdiv" style="height:500px;width:1000px; ">
						<canvas id="chart-area" width="1000" height="500"/>
					</div>
				';
			}
			
			//Vételezés lekérése
			if ($_POST['type'] == 5) {
				
				$query = mysql_query("
					SELECT `drinks`.`name`, sum(`stand`.`vetel`) as `vetel`
					FROM `stand`

					LEFT JOIN`standok`
					ON `standok`.`ID` = `stand`.`SID`

					LEFT JOIN `drinks`
					on `stand`.`DID` = `drinks`.`ID`

					WHERE `standok`.`date` BETWEEN '".$_POST['startDate']."' AND '".$_POST['endDate']."'
					AND `standok`.`PID` = ".$_SESSION['pub']->ID."
					AND `drinks`.`ID` = ".$_POST['termek'].";
				");
				$result = mysql_fetch_assoc($query);
				echo "<span class='anchor2'>összes <u>".$result['name']."</u> vételezés <u>".$_POST['startDate']."</u> és <u>".$_POST['endDate']."</u> között:</span><br />
				".$result['vetel'];
				
			}
			
			//Korrigálás lekérése
			if ($_POST['type'] == 6) {
				
				//cím
				echo "<span class='anchor2'><u>Fizetés</u> módosítások</span><br />";
				
				//Összesítve lekérés
				$users = array();
				$query = mysql_query("
					SELECT `user`.`ID`, `user`.`uname`, sum(`korrigalasok`.`value`) as `osszesen` 
					FROM `korrigalasok`

					LEFT JOIN `user`
					ON `user`.`ID` = `korrigalasok`.`UID`

					WHERE `stand_date` BETWEEN '".$_POST['ev']."-".$_POST['honap']."-01' AND '".date("Y-m-t", strtotime($_POST['ev']."-".$_POST['honap']."-01"))."'
					GROUP BY `UID`;
				");
				while ($result = mysql_fetch_assoc($query)) {
					echo "<b>".$result['uname']."</b>: ".($result['osszesen']*-1)."<br />";
					$users[$result['ID']] = $result['uname'];
				}
				
				//cím
				echo "<br /><br /><span class='anchor2'><u>Leadó</u> változások tételesen</span><br />";
				foreach ($users as $id => $name) {
					
					echo "<b>$name:</b><br />";
					$query = mysql_query("
						SELECT `korrigalasok`.*, `drinks`.`name`
						FROM `korrigalasok`

						LEFT JOIN `drinks`
						ON `drinks`.`ID` = `korrigalasok`.`DID`

						WHERE `stand_date` BETWEEN '".$_POST['ev']."-".$_POST['honap']."-01' AND '".date("Y-m-t", strtotime($_POST['ev']."-".$_POST['honap']."-01"))."'
						AND `UID` = $id;
					");
					while ($result = mysql_fetch_assoc($query)) {
						
						echo "
							<u>Termék:</u> ".$result['name']."<br />
							<u>Leadó változás:</u> ".$result['value']."<br />
							<u>Stand befejező dátuma:</u> ".$result['stand_date']."<br />
							<u>Üzenet:</u> ".$result['msg']."<br /><br />
						";
						
					}
					
				}
				
			}
		}
		
		//close conn
		mysql_close($kapcsolat);
	}
	
?>