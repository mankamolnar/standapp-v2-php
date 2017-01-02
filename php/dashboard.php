<?php

function format_price($price) {
	return number_format($price,0,'.',' ');
}

//dashboard
function dashboard() {
	
	
	//seeable by everyone
	$str = "
		
		<table cellspacing='0'>
		
			<tr>
				<td class='td1'>
					<span class='anchor2'>Felhasználónév</span>
				</td>
				
				<td class='td1'>
					<b>".$_SESSION['name']."</b>
				</td>
				
				<td class='td3'>
				
				</td>
			<tr>
			
			<tr>
				<td class='td1'>
					<span class='anchor2'>Üzlet</span>
				</td>
				
				<td class='td1'>
					<b>".$_SESSION['pub']->name."</b>
				</td>
				
				<td class='td3'>
				
				</td>
			<tr>
			
			<tr>
				<td class='td2'>
					
				</td>
				
				<td class='td2'>
					
				</td>
				
				<td>
				
				</td>
			<tr>
			
		</table>";
		
	//csak alkalmazottnál nagyobb jogkörű
	if ($_SESSION['tether'] > 0) {
		
		//kocsma beállításainak betöltése
		$pubOptions = array();
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = ".$_SESSION['pub']->ID.";");
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result['value'];
		}
		
		//mysql queries
		$query = mysql_query("
			SELECT count(*) as `standokSzama` 
			FROM `standok` 
			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
			AND `standok`.`PID` = ".$_SESSION['pub']->ID."
			AND `standok`.`finished` = 1;");
		$standokSzama = mysql_fetch_assoc($query);
		
		//új univerzális forgalom lekérdezés
		if (isset($pubOptions['ujforgSzamol']) && $pubOptions['ujforgSzamol'] == 1) {
			
			$query = mysql_query("SELECT SUM(`forgalom`) FROM `standok` WHERE `PID` = ".$_SESSION['pub']->ID." AND `date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."';");
			$forgalom = mysql_fetch_assoc($query);
			
			//calculate kiadasok
			$query = mysql_query("
				SELECT
				sum(`kiadasok`.`ertek`) as `ertek`
				FROM `standok` 
				LEFT JOIN `kiadasok` ON `standok`.`ID` = `kiadasok`.`SID`
				WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
				AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
			");
			$kiadasok = mysql_fetch_assoc($query);
			$forgalom['forgalom'] += intval($kiadasok['ertek']);
			
			//bankkártyás fizetések
			$bankkartyas = array();
			$bankkartyas['ertek'] = 0;
			if (isset($pubOptions['bankkartya']) && $pubOptions['bankkartya'] == 1) {
				$query = mysql_query("
					SELECT
					sum(`bankkartyasFizetes`.`value`) as `ertek`
					FROM `standok` 
					LEFT JOIN `bankkartyasFizetes` ON `standok`.`ID` = `bankkartyasFizetes`.`SID`
					WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
					AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
				");
				$bankkartyas = mysql_fetch_assoc($query);
			}
			$forgalom['forgalom'] += intval($bankkartyas['ertek']);
			
		//régi számolás
		} else {
			
			//HA PLUSZ RAKTÁROS
			if (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 1) {
				
				$tmpMaradvanyok = array();
				
				//legutóbbi standlap
				$sorok = array();
				$forgalom = array();
				$forgalom['forgalom'] = 0;
				$query = mysql_query("
				SELECT
				`standok`.`ID` as `SID2`,
				`stand`.`DID` as `DID2`,
				`stand`.`nyito`,
				`stand`.`vetel`,
				(`stand`.`nyito`+`stand`.`vetel`-`stand`.`fogyas`) as `maradvany`,
				(`stand`.`nyito`+`stand`.`vetel`+`stand`.`fogyas`) as `maradvanyf`,
				`stand`.`price`,
				sum(`pluszRaktarMaradvany`.`maradvany`) as `pmaradvany`,

				(SELECT `standok`.`ID` 
				FROM `standok` 
				WHERE `standok`.`ID` < `SID2` 
				ORDER BY `standok`.`ID` DESC 
				LIMIT 0,1) 
				as `lastStand`,

				(SELECT SUM(`pluszRaktarMaradvany`.`maradvany`) FROM `pluszRaktarMaradvany` WHERE `pluszRaktarMaradvany`.`SID` = `lastStand` AND `pluszRaktarMaradvany`.`DID` = `DID2`) as `pnyito`,

				(SELECT SUM(`pvetel`.`value`) FROM `pvetel` WHERE `pvetel`.`SID` = `SID2` AND `pvetel`.`DID` = `DID2` ) as `pvetel`,
				`drinks`.`forditott`

				FROM `standok`

				LEFT JOIN `stand` 
				ON `standok`.`ID` = `stand`.`SID`

				LEFT JOIN `pluszRaktarMaradvany`
				ON `pluszRaktarMaradvany`.`SID` = `standok`.`ID`
				AND `pluszRaktarMaradvany`.`DID` = `stand`.`DID`

				LEFT JOIN `drinks`
				on `drinks`.`ID` = `stand`.`DID`
				WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
				AND `standok`.`PID` = ".$_SESSION['pub']->ID."

				GROUP BY `standok`.`ID`,`stand`.`DID`;");
				while ($result = mysql_fetch_assoc($query)) {
					
					//mysql result tömbbe
					$sorok[] = $result;
					
					//fogyas számolása
					$sorok[count($sorok)-1]['fogyas'] = 0;
					if ($sorok[count($sorok)-1]['forditott'] == 0) {
						$sorok[count($sorok)-1]['fogyas'] = $sorok[count($sorok)-1]['nyito']+$sorok[count($sorok)-1]['pnyito']+$sorok[count($sorok)-1]['vetel']+$sorok[count($sorok)-1]['pvetel']-$sorok[count($sorok)-1]['maradvany']-$sorok[count($sorok)-1]['pmaradvany'];
					} else {
						$sorok[count($sorok)-1]['fogyas'] = $sorok[count($sorok)-1]['maradvanyf']-$sorok[count($sorok)-1]['nyito']+($sorok[count($sorok)-1]['pmaradvany']-$sorok[count($sorok)-1]['pnyito'])-$sorok[count($sorok)-1]['vetel']-$sorok[count($sorok)-1]['pvetel'];
					}
					
					//érték számolása
					$sorok[count($sorok)-1]['ertek'] = $sorok[count($sorok)-1]['fogyas'] * $sorok[count($sorok)-1]['price'];
					$sorok[count($sorok)-1]['ertek'] = floor($sorok[count($sorok)-1]['ertek']);
					
					//to forgalom
					$forgalom['forgalom'] += $sorok[count($sorok)-1]['ertek'];
				}
				
				
			//HA NINCS PLUSZ RAKTÁR
			} else {
				
				//calculate forgalom
				$query = mysql_query("
					SELECT
					sum(`stand`.`fogyas` * `stand`.`price`) as `forgalom`
					FROM `standok` 
					LEFT JOIN `stand` ON `standok`.`ID` = `stand`.`SID`
					WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
					AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
				");
				$forgalom = mysql_fetch_assoc($query);
				
			}
			
		}
		
		//akcios stand fogyás
		$query = mysql_query("
			SELECT
			sum(`AkciosStand`.`fogyas` * `AkciosStand`.`price`) as `forgalom`
			FROM `standok` 
			LEFT JOIN `AkciosStand` ON `standok`.`ID` = `AkciosStand`.`SID`
			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
		");
		$akcios = mysql_fetch_assoc($query);
		$forgalom['forgalom'] += intval($akcios['forgalom']);
		
		//módosítható stand fogyás
		$query = mysql_query("
			SELECT
			sum(`modStandSor`.`ar` * (`modStandSor`.`nyito` + `modStandSor`.`vetel` - `modStandSor`.`maradvany`) ) as `forgalom`
			FROM `standok` 
			LEFT JOIN `modStandSor` ON `standok`.`ID` = `modStandSor`.`SID`
			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
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

			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."'  
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";");
			$etelfogy = mysql_fetch_assoc($equery);
			
			$forgalom['forgalom'] += $etelfogy['etelfogy'];
		}
		
		//egyéb forgalom
		if (isset($pubOptions['egyebForgalom']) && $pubOptions['egyebForgalom'] == 1) {
			$equery = mysql_query("
			SELECT sum(`egyebForgalom`.`value`) as `egyebfogy` FROM `standok`

			LEFT JOIN `egyebForgalom`
			ON `egyebForgalom`.`SID` = `standok`.`ID`

			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."'  
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";");
			$egyebfogy = mysql_fetch_assoc($equery);
			
			$forgalom['forgalom'] += $egyebfogy['egyebfogy'];
		}
		
		//saját forgalom
		if (isset($pubOptions['sajatFogyasztas']) && $pubOptions['sajatFogyasztas'] == 1) {
			$squery = mysql_query("
			SELECT sum(`sajatFogyasztas`.`value`) as `sajatfogy` FROM `standok`

			LEFT JOIN `sajatFogyasztas`
			ON `sajatFogyasztas`.`SID` = `standok`.`ID`

			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."'  
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";");
			$sajatfogy = mysql_fetch_assoc($squery);
			
			$forgalom['forgalom'] -= $sajatfogy['sajatfogy'];
		}
		
		//calculate kiadasok
		$query = mysql_query("
			SELECT
			sum(`kiadasok`.`ertek`) as `ertek`
			FROM `standok` 
			LEFT JOIN `kiadasok` ON `standok`.`ID` = `kiadasok`.`SID`
			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
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
				WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
				AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
			");
			$bankkartyas = mysql_fetch_assoc($query);
		}
		
		//calculate lotto 1%
		$query = mysql_query("
			SELECT
			sum(`LottoStand`.`NetForg`) / 100 as `1sz`
			FROM `standok` 
			LEFT JOIN `LottoStand` ON `standok`.`ID` = `LottoStand`.`SID`
			WHERE `standok`.`date` BETWEEN '".date("Y-m-01")."' AND '".date("Y-m-t")."' 
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";
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

			WHERE `standok`.`date` BETWEEN '".date('Y-m-01')."' AND '".date('Y-m-t')."'
			AND `standok`.`finished` = 1
			AND `standok`.`PID` = ".$_SESSION['pub']->ID.";");
			
		$salary = mysql_fetch_assoc($query);
		
		//date select generálás
		$dyoptions = "";
		for ($d = date("Y"); $d >= 2013; $d--) {
			$dyoptions .= "<option value='$d'>$d</option>";
		}
		
		//hónap selectedek
		$msel = array();
		for ($i = 0; $i < 12; $i++) {
			if (intval(date("m")) == $i+1) {
				$msel[$i] = "selected='selected'";
			} else {
				$msel[$i] = "";
			}
		}
		
		$str = $str."
			<br />
			<table cellspacing='0'>
				
				<tr>					
					<td class='td1' colspan='2'>
						
						<input type='hidden' id='dashboardPID' value='".$_SESSION['pub']->ID."' />
						
						<select id='dashboardDate'>
							$dyoptions
						</select>
						
						<select id='dashboardMonth'>
							<option value='01' ".$msel[0].">Január</option>
							<option value='02' ".$msel[1].">Február</option>
							<option value='03' ".$msel[2].">Március</option>
							<option value='04' ".$msel[3].">Április</option>
							<option value='05' ".$msel[4].">Május</option>
							<option value='06' ".$msel[5].">Június</option>
							<option value='07' ".$msel[6].">Július</option>
							<option value='08' ".$msel[7].">Augusztus</option>
							<option value='09' ".$msel[8].">Szeptember</option>
							<option value='10' ".$msel[9].">Október</option>
							<option value='11' ".$msel[10].">November</option>
							<option value='12' ".$msel[11].">December</option>
						</select>
						
						<button onclick='dashboardClick();'>Váltás!</button>
					</td>
					
					<td class='td3'>
					
					</td>
				</tr>
				
				<tr>
					<td class='td1'>
						<span class='anchor2'>E-havi felvitt standlapok</span>
					</td>
					
					<td class='td1'>
						<span id='standszamspan'>".round($standokSzama['standokSzama'])."</span> db
					</td>
					
					<td class='td3'>
					
					</td>
				<tr>
				
				<tr>
					<td class='td1'>
						<span class='anchor2'>E-havi forgalom</span>
					</td>
					
					<td class='td1'>
						<span id='haviforgalomspan'>".format_price(round($forgalom['forgalom']))."</span> Ft
					</td>
					
					<td class='td3'>
					
					</td>
				<tr>
				
				<tr>
					<td class='td1'>
						<span class='anchor2'>E-havi kiadások</span>
					</td>
					
					<td class='td1'>
						<span id='havikiadasspan'>".format_price(round($kiadasok['ertek']-2*$lotto['1sz']))."</span> Ft
					</td>
					
					<td class='td3'>
					
					</td>
				<tr>
				
				<tr>
					<td class='td1'>
						<span class='anchor2'>E-havi leadó</span>
					</td>
					
					<td class='td1'>
						<span id='havileadospan'>".format_price(round($forgalom['forgalom']+$kiadasok['ertek']-$lotto['1sz']-$bankkartyas['ertek']))."</span> Ft
					</td>
					
					<td class='td3'>
					
					</td>
				<tr>
				
				<tr>
					<td class='td1'>
						<span class='anchor2'>E-havi fizetések</span>
					</td>
					
					<td class='td1'>
						<span id='havifizetesspan'>".format_price(round($salary['salary']))." Ft <a href='?page=20'><img src='img/newItallap.png' width='25' height='25' /></a></span>
					</td>
					
					<td class='td3'>
					
					</td>
				<tr>
				
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
		
		//mysql_close($kapcsolat);
	}
	
	$str = $str."<hr />";
	
	return $str;
}
	
?>