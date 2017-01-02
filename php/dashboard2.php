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
		
		//HA PLUSZ RAKTÁROS
		if (isset($pubOptions['pluszRaktar']) && $pubOptions['pluszRaktar'] == 1) {
			
			$tmpMaradvanyok = array();
			
			
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
		
		//Jutalék van-e
		if (isset($pubOptions['jutalek']) && $pubOptions['jutalek'] != 0) {
			$forgalom['forgalom'] = round($forgalom['forgalom']-(($forgalom['forgalom'] / 100) * $pubOptions['jutalek']));
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
						<span id='havileadospan'>".format_price(round($forgalom['forgalom']+$kiadasok['ertek']-$lotto['1sz']))."</span> Ft
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