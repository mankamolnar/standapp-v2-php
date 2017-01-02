<?php
	
	//ITTALLAP MÓDOSÍTÁS
	function ItalMod() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat); 
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		
		//LEKÉRDEZÉS AZ ITALOKHOZ
		$query = mysql_query("SELECT * FROM  `drinks` WHERE  `PID` =".$_SESSION['pub']->ID." ORDER BY  `List.ID` ASC;");
		
		//BETOLTÉS CLASSBA
		$italok = array();
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
			//$ID, $CSID, $MID, $list_id, $name, $price, $visible
			$italok[$i] = new italok($result['ID'], $result['CSID'], $result['MID'], $result['List.ID'], $result['name'], $result['price'], $result['visible'], $result['forditott'], $result['purchase_price']);
			
			$i++;
		}
		
		//CSOPORT SELECT LÉTREHOZÁS
		$csopSel = "";
		$csoport = array();
		$query = mysql_query("SELECT * FROM `AruCsop`;");
		while($result = mysql_fetch_assoc($query)) {
			
			$csoport[$result['ID']] = new italcsop($result['ID'], $result['csop']);
			$csopSel = $csopSel."<option value='".$result['ID']."'>".$result['csop']."</option>";
			
		}
		
		//Mértékegység select létrehozás
		$mertSel = "";
		$mert = array();
		$query = mysql_query("SELECT * FROM `Mert`;");
		while ($result = mysql_fetch_assoc($query)) {
		
			$mert[$result['ID']] = new mertekegyseg($result['ID'], $result['egyseg']);
			$mertSel = $mertSel."<option value='".$result['ID']."'>".$result['egyseg']."</option>";
			
		}
		
		//új itallap div táblázat
		echo "
			<form action='index.php?page=7' method='post' id='drFrm'>
			
			<input type='hidden' name='filterVis' value='1' />
			<input type='hidden' name='filterCsop' value='0' />
			<input type='hidden' id='pid' value='".$_SESSION['pub']->ID."' />
			<div id='itallap'>
				
				<div id='itallapMenuUp'>
					
					<div id='itallapSearchDiv'>
						
						<input type='text' id='itallapSearch' />
						
					</div>
					
					<div id='itallapSearchBut'></div>
					
					<div id='itallapMenuAdd'></div>
					
					<div id='itallapMenuSave'></div>
					
					<div id='itallapMenuFilter'></div>
					
				</div>
				
				<div id='itallapNewMenu'>
						
					<div class='newTermTd1'>
						<br /><button type='button' id='newFelBut' type='submit' />Felvitel</button>
					</div>
					
					<div class='newTermTd1'>
						<br /><input type='text' id='newName' />
					</div>
					
					<div class='newTermTd1'>
						<br /><input type='text' id='newAr' />
					</div>
					
					<div class='newTermTd1'>
						<br /><input type='text' id='newBar' />
					</div>
					
					<div class='newTermTd1'>
						<br /><select id='newCsop'>".$csopSel."</select>
					</div>
					
					<div class='newTermTd1'>
						<br /><select id='newMert'>".$mertSel."</select>
					</div>
					
					<div class='newTermTd1'>
						<br /><input type='checkbox' id='forditottNew' value='1' />
					</div>
					
					<div class='newTermTd1'>
						<br /><img src='img/close.png' width='22' height='22' id='closeNew' />
					</div>
						
					
					
				</div>
				
				<div id='itallapMenuDown'>
					
					<div class='itallapTd1'>
						<br/>Sorszám
					</div>
					
					<div class='itallapTd1'>
						<br/>Név
					</div>
					
					<div class='itallapTd1'>
						<br/>Ár
					</div>
					
					<div class='itallapTd1'>
						<br/>Beszerzési ár
					</div>
					
					<div class='itallapTd1'>
						<br/>Csoport
					</div>
					
					<div class='itallapTd1'>
						<br/>Mérték egység
					</div>
					
					<div class='itallapTd1'>
						<br/>Fordított Számolás
					</div>
					
					<div class='itallapTd2'>
						<br/>m
					</div>
				
				</div>
				
		";
		
		//KÖZTES SOR GEN
		for ($i = 0; $i < count($italok); $i++) {
			
			//LÁTHATÓSÁG HIVATKOZÁS GENERÁLÁS
			$visible = "";
			$price = "";
			$name = "";
			$move = "<img src='img/move.png' class='moveimg' id='$i' width='30' height='30' alt='Áthelyezés' />";
			
			if ($italok[$i]->visible == 0) {
				$visible = "<img src='img/eye_off.png' width='30' height='30' style='cursor: pointer;' class='visibleImg' id='vi$i' alt='Láthatóság' />";
				$name = '<input type="text" name="italNev['.$i.']" id="italNev'.$i.'" value="'.$italok[$i]->name.'" disabled="disabled" />';
				$price = '<input type="text" name="italAr['.$i.']" id="italAr'.$i.'" value="'.$italok[$i]->price.'" disabled="disabled" />';
				$pprice = '<input type="text" name="italBar['.$i.']" id="italBar'.$i.'" value="'.$italok[$i]->pprice.'" disabled="disabled" />';
			} else {
				$visible = "<img src='img/eye_on.png' width='30' height='30' style='cursor: pointer;' class='visibleImg' id='vi$i' alt='Láthatóság' />";
				$name = '<input type="text" onkeypress="setChg('.$i.');" name="italNev['.$i.']" id="italNev'.$i.'" value="'.$italok[$i]->name.'" />';
				$price = '<input type="text" onkeypress="setChg('.$i.');" name="italAr['.$i.']" id="italAr'.$i.'" value="'.$italok[$i]->price.'" />';
				$pprice = '<input type="text" onkeypress="setChg('.$i.');" name="italBar['.$i.']" id="italBar'.$i.'" value="'.$italok[$i]->pprice.'" />';
			}
			$csop = "<select name='csoport[$i]' id='csoport".$i."' class='itallapSelect' onclick='setChg(".$i.");'><option value='".$italok[$i]->CSID."'>jelenleg: ".$csoport[$italok[$i]->CSID]->csop."</option>".$csopSel."</select>";
			$mertek = "<select name='mertekegyseg[$i]' id='mertekegyseg".$i."' class='itallapSelect' onclick='setChg(".$i.");'><option value='".$italok[$i]->MID."'>jelenleg: ".$mert[$italok[$i]->MID]->egyseg."</option>".$mertSel."</select>";
			
			//Fordított számolás beállítás
			if ($italok[$i]->forditott == 0) {
				$forditott = "<input type='checkbox' onclick='setFord($i);'  />";
			} else {
				$forditott = "<input type='checkbox' onclick='setFord($i);' checked='checked' />";
			}
			
			//ID-MEGJELENÍTÉS HA RNEDSZERGAZDA
			$showid = "";
			if ($_SESSION['tether'] == 3) {
				$showid = " - ".$italok[$i]->ID;
			}
			
			//MEGJELENÍTÉS
			echo '
				<div class="itallapSor" id="italSor'.$italok[$i]->ID.'">
				
					<div class="sorTd">
						'.$italok[$i]->list_id.'.'.$showid.'
					</div>
					
					<div class="sorTd">
						<input type="hidden" name="ID['.$i.']" id="ID'.$i.'" value="'.$italok[$i]->ID.'" />
						<input type="hidden" name="visibleHidden['.$i.']" id="visibleHidden'.$i.'" value="'.$italok[$i]->visible.'" />
						<input type="hidden" name="chg['.$i.']" id="chg'.$i.'" value="0" />
						<input type="hidden" name="listID['.$i.']" id="listID'.$i.'" value="'.$italok[$i]->list_id.'" />
						'.$name.'
					</div>
					
					<div class="sorTd">
						'.$price.'
					</div>
					
					<div class="sorTd">
						'.$pprice.'
					</div>
					
					<div class="sorTd">
						'.$csop.'
					</div>
					
					<div class="sorTd">
						'.$mertek.'
					</div>
					
					<div class="sorTd">
						'.$forditott.'
						<input type="hidden" name="forditott['.$i.']" value="'.$italok[$i]->forditott.'" />
					</div>
					
					<div class="sorTd">
						'.$move.'
						'.$visible.'
						<a href="index.php?page=22&did='.$italok[$i]->ID.'"><img src="img/newAkcio.png" border="0" width="30" height="30" /></a>
					</div>
					
				</div>
			';
		}
		
		//TÁBLÁZAT ZÁR
		echo '
				<div id="filterDiv" style="visibility:hidden;">
					<div id="switchEline"></div>
					
					<div class="filterTitle">
						Láthatóság
					</div>
					
					<div class="switch">
						<div class="switchBt" id="filterVis"></div>
					</div>
				</div>
				
			</div>
			</form>
			 <script type="text/javascript">
				saveBut('.$i.');
			 </script>
			 ';
		
		//CLOSE
		mysql_close($kapcsolat);
	}
	
	//Itallap feltöltés
	function ItalModFel() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat); 
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//ÚJ TERMÉK FELVITELE
		if (isset($_POST['newName'])) {
			
			if (!$_POST['forditott']) {
				$forditott = 0;
			} else {
				$forditott = 1;
			}
			
			mysql_query("INSERT INTO `".$database."`.`drinks` (`ID`, `PID`, `CSID`, `MID`, `List.ID`, `name`, `price`, `purchase_price`, `visible`, `forditott`) VALUES (NULL, '".$_SESSION['pub']->ID."', '".$_POST['newCsop']."', '".$_POST['newMert']."', '".$_POST['all']."', '".$_POST['newName']."', '".$_POST['newAr']."', '".$_POST['newBar']."', '1', '".$forditott."');");
		
		//TERMÉK MÓDOSÍTÁS
		} else {
			//KAPOTT ÉRTÉKEK
			$chg = $_POST['chg'];
			$visibleHidden = $_POST['visibleHidden'];
			$italNev = $_POST['italNev'];
			$italAr = $_POST['italAr'];
			$italBar = $_POST['italBar'];
			$ID = $_POST['ID'];
			$csoport = $_POST['csoport'];
			$mertekegyseg = $_POST['mertekegyseg'];
			$forditott = $_POST['forditott'];
			
			//TÖMB BEJÁRÁS ÉS MÓDOSÍTÁS ELVÉGZÉSE
			$i = 0;
			while (isset($chg[$i])) {
				
				//HA TÖRTÉNT VÁLTOZTATÁS
				if ($chg[$i] == 1) {
					
					//HA VISIBLE 1
					if ($visibleHidden[$i] != 0) {
						
						//query the drinks current visible, if it was invisible it'll put it to the first place!
						$query = mysql_query("SELECT `drinks`.`visible` FROM `drinks` WHERE `drinks`.`ID` = ".$ID[$i].";");
						$result = mysql_fetch_assoc($query);
						
						if ($result['visible'] == 0) {
						
							mysql_query("UPDATE `drinks` SET `List.ID` = `List.ID`+1 WHERE `drinks`.`PID` = ".$_SESSION['pub']->ID." AND `drinks`.`visible` = 1;");
							mysql_query("UPDATE `".$database."`.`drinks` SET `visible` = $visibleHidden[$i], `List.ID` = 1 WHERE `ID` = $ID[$i];");
						
						} else {
						
							mysql_query("UPDATE `".$database."`.`drinks` SET `name` = '$italNev[$i]', `price` = $italAr[$i], `purchase_price` = '$italBar[$i]', `CSID` = $csoport[$i], `MID` = $mertekegyseg[$i], `visible` = $visibleHidden[$i], `forditott` = $forditott[$i] WHERE `ID` = $ID[$i];");
						}
						
					} else {
						
						//query the modificated drinks list id
						$query = mysql_query("SELECT `drinks`.`List.ID` as `list` FROM `drinks` WHERE `drinks`.`ID` = $ID[$i];");
						$result = mysql_fetch_assoc($query);
						
						mysql_query("UPDATE `".$database."`.`drinks` SET `visible` = $visibleHidden[$i] WHERE `ID` = $ID[$i];");
						mysql_query("UPDATE `drinks` SET `List.ID` = `List.ID`-1 WHERE `drinks`.`PID` = ".$_SESSION['pub']->ID." AND `drinks`.`visible` = 1 AND `drinks`.`List.ID` > ".$result['list'].";");
					}
				}
				
				$i++;
			}
		}
		
		//újra sorszámozás
		$query = mysql_query("SELECT * FROM `drinks` WHERE `drinks`.`PID` = ".$_SESSION['pub']->ID." AND `drinks`.`visible` = 1 ORDER BY `List.ID` ASC;");
		$i = 1;
		while ($result = mysql_fetch_assoc($query)) {
			
			mysql_query("UPDATE  `".$database."`.`drinks` SET  `List.ID` =  '".$i."' WHERE  `drinks`.`ID` =".$result['ID'].";");
			
			$i++;
		}
		
		mysql_close($kapcsolat);
		
		Header("Location: index.php?page=6");
		
		
	}
	
	//Ital HELY MODOSÍTÁS
	function MoveDrink() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//get listId
		$query = mysql_query("SELECT * FROM `drinks` WHERE `ID` = ".$_POST['ID'].";");
		$result = mysql_fetch_assoc($query);
		$aktListID = $result['List.ID'];
		
		//MELY ÉRTÉKEKEK NÖVELJE
		if ($aktListID > $_POST['moveTo']) {
			
			//MÓDOSÍTANDÓ REKORD
			mysql_query("UPDATE `".$database."`.`drinks` SET `List.ID` = ".$_POST['moveTo']." WHERE `ID` = ".$_POST['ID'].";");
			
			//TÖBBI IGAZÍTÁSA
			mysql_query("UPDATE `".$database."`.`drinks` SET `List.ID` = (`List.ID`+1) WHERE `List.ID` >= ".$_POST['moveTo']." AND `List.ID` < ".$aktListID." AND `PID` = ".$_SESSION['pub']->ID." AND `ID` != ".$_POST['ID'].";");
		
		} else if ($aktListID < $_POST['moveTo']) {
			
			//MÓDOSÍTANDÓ REKORD
			mysql_query("UPDATE `".$database."`.`drinks` SET `List.ID` = ".$_POST['moveTo']." WHERE `ID` = ".$_POST['ID'].";");
			
			//TÖBBI IGAZÍTÁSA
			mysql_query("UPDATE `".$database."`.`drinks` SET `List.ID` = (`List.ID`-1) WHERE `List.ID` <= ".$_POST['moveTo']." AND `List.ID` > ".$aktListID." AND `PID` = ".$_SESSION['pub']->ID." AND `ID` != ".$_POST['ID'].";");
		}
		
		mysql_close($kapcsolat);
		
		header("Location: index.php?page=6");
	}
	
	//AKCIO beállítása
	function setAkcio() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat); 
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//jelenlegi ital megállapítása
		$query = mysql_query("SELECT * FROM `drinks` WHERE `drinks`.`ID` = ".$_GET['did'].";");
		$currentDrink = mysql_fetch_assoc($query);
		
		//új akció kitöltése
		echo "
			<p class='anchor2'>Új akció beállítása ".$currentDrink['name']." termékre:</p><br />
			<form action='index.php' method='get'>
				<input type='hidden' name='page' value='23' />
				<input type='hidden' name='did' value='".$_GET['did']."' />
				Akciós ár: 
				<input type='text' name='price' /><br /><br/>
				
				Akció napja: 
				<select name='day'>
					<option value='Monday'>Hétfő</option>
					<option value='Tuesday'>Kedd</option>
					<option value='Wednesday'>Szerda</option>
					<option value='Thursday'>Csütörtök</option>
					<option value='Friday'>Péntek</option>
					<option value='Saturday'>Szombat</option>
					<option value='Sunday'>Vasárnap</option>
				</select><br /><br />
				
				Rendszeres-e:
				<input type='checkbox' name='usual' value='1' /><br /><br />
				
				<input type='submit' value='Akció hozzáadása' />
			</form>
			<hr />
			<p class='anchor2'>".$currentDrink['name']." termékhez tartozó akciók:</p><br />
		";
		
		//Ehhez az italhoz tartozó aktuáliis akciók
		$query = mysql_query("SELECT * FROM `Akciok` WHERE `DID` = ".$_GET['did'].";");
		
		//table nyitás
		echo "
			<table cellpadding='0' cellspacing='0' border='0' width='600' style='border-left:1px solid #000;border-top:1px solid #000; border-right:1px solid #000;'>
				<tr>
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'><b>Ár</b></td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'><b>Nap</b></td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'><b>Rendszeresség</b></td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'><b>Aktív-e</b></td>
					
					<td  style='border-bottom:1px solid #000;'><b>Törlés</b></td>
				</tr>
		";
		
		//Kiolvasás mysqlbol
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			
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
			
			echo "
				<tr>
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'>
						<input type='hidden' name='saleID$i' value='".$result['ID']."' />
						<input type='text' name='sealPrice$i' value='".$result['price']."' /> 
						<img src='img/floppy.png' onclick='changePrice($i);' style='cursor:pointer; width:30px; height:30px;' />
					</td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'>".$result['date']."</td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'>".$result['usual']."</td>
					
					<td style='border-bottom:1px solid #000; border-right:1px solid #000;'>".$result['activated']."</td>
					
					<td style='border-bottom:1px solid #000;'><a href='index.php?page=24&did=".$_GET['did']."&id=".$result['ID']."'><img src='img/close.png' width='30' height='30' border='0' /></a></td>
				</tr>
			";
			
			$i++;
		}
		
		//ha nincs találat
		if ($i == 0) {
			echo "
				<tr>
					<td colspan='5' style='border-bottom:1px solid #000;'>Nincs találat!</td>
				</tr>
			";
		}
		
		//table zárás
		echo "</table>";
		
		//close mysql connection
		mysql_close($kapcsolat);
	}
	
	//new seal
	function newSeal() {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat); 
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//hibák
		if (!is_numeric($_GET['price']) || $_GET['price'] == "") {
			echo "<b>Nem töltötted ki az Ár mezőt!</b><br />";
		} else {
			
			//usual beáll
			if (isset($_GET['usual'])) {
				$usual = 1;
			} else {
				$usual = 0;
			}
			
			//akció felvitele
			if (date('l') == $_GET['day']) {
				mysql_query("INSERT INTO `".$database."`.`Akciok` (`ID`, `DID`, `PID`, `date`, `usual`, `price`, `activated`) VALUES (NULL, '".$_GET['did']."', '".$_SESSION['pub']->ID."', '".date('Y-m-d')."', '".$usual."', '".$_GET['price']."', 1);");
			} else {
				mysql_query("INSERT INTO `".$database."`.`Akciok` (`ID`, `DID`, `PID`, `date`, `usual`, `price`, `activated`) VALUES (NULL, '".$_GET['did']."', '".$_SESSION['pub']->ID."', '".date('Y-m-d', strtotime('next ' . $_GET['day']))."', '".$usual."', '".$_GET['price']."', 1);");
			}
			
			echo "<b>Feltöltés kész!</b><br /><br />";
			
		}
		
		//close
		mysql_close($kapcsolat);
		
		echo setAkcio();
	}
	
	//Delete Seal
	function deleteSeal() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat); 
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		mysql_query("UPDATE  `".$database."`.`Akciok` SET  `activated` =  0 WHERE  `Akciok`.`ID` =".$_GET['id'].";");
		
		echo "<b>Sikeresen törölve lett az akció</b><br /><br />"; 
		
		mysql_close($kapcsolat);
		
		echo setAkcio();
	}
?>