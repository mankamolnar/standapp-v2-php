<?php
	
	//KERESŐ
	function ElszamSearch() {
		//sessionbe töltés
		if (isset($_POST['tol'])) { // van POST
		
			//session searchdate létrehozás és változó beállítás
			$tol = $_SESSION['searchDate']->tol = $_POST['tol'];
			$ig = $_SESSION['searchDate']->ig = $_POST['ig'];
		
		} else if (isset($_SESSION['searchDate'])) { // van SESSION
			
			//változó beállítás
			$tol = $_SESSION['searchDate']->tol;
			$ig = $_SESSION['searchDate']->ig;
			
		} else { // nincs még semmi
			
			//változó beállítás e hónapra
			$_SESSION['searchDate'] = new keresoDatum(date('Y-m-01'), date("Y-m-t"));
			$tol = $_SESSION['searchDate']->tol;
			$ig = $_SESSION['searchDate']->ig;
			
		}
		
		echo "
			<p class='anchor2'>Keresés</p>
			<form action='index.php?page=4' method='post'>
				<p>
					<input type='hidden' name='search' value='TRUE' />
					<input type='text' id='dateset' name='tol' value='".$tol."' />-tól <input type='text' id='dateset2' name='ig' value='".$ig."' />-ig<br />
					<input type='submit' value='Keresés!' />
				</p>
			</form>
			
		";
		
		//Date ellenőrzés
		if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $tol) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $ig)) {
			
			//LOADER elhelyezesése
			echo "
				<script type='text/javascript'>
					//LOADING
					$(\".signo\").append(\"<div id='loader'><center><br /><font class='anchor3'>LOADING...</font></center></div>\");
				</script>
			";
			
			//dátum felrobbantása
			$tol2 = explode("-", $tol);
			$ig2 = explode("-", $ig);
			
			//2 dátum különbsége
			$diff = abs(strtotime($ig) - strtotime($tol));
			
			//napok
			$days = round($diff / (60*60*24));
			
			//hét napjai
			$week = array();
			$week[0] = "Mon";
			$week[1] = "Tue";
			$week[2] = "Wed";
			$week[3] = "Thu";
			$week[4] = "Fri";
			$week[5] = "Sat";
			$week[6] = "Sun"; //Here comes the S.U.N.
			
			//div kinyitása calendarnak és PID
			echo "
				<hr />
				<input type='hidden' name='pid' value='".$_SESSION['pub']->ID."' />
				<div id='searchCalendar'>
					<div class='daysCalendar'>Hétfő</div>
					<div class='daysCalendar'>Kedd</div>
					<div class='daysCalendar'>Szerda</div>
					<div class='daysCalendar'>Csütörtök</div>
					<div class='daysCalendar'>Péntek</div>
					<div class='daysCalendar'>Szombat</div>
					<div class='daysCalendar'>Vasárnap</div>
			";
			
			//Üres napok a calendar elején
			$i = 0;
			while (trim(date("D", strtotime($tol))) != trim($week[$i])) {
				echo "<div class='emptyDay'></div>";
				
				$i++;
			}
			
			//rá következő napok
			for ($i = 0; $i <= $days; $i++) {
				
				//hétvége vagy hétköznapi doboz
				if (date("D", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0])) == "Sat" || date("D", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0])) == "Sun") {
				
					echo "
						<div class='dayCalendarWeekend' id='d".date("Ynj", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0]))."'>
							".date("j", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0]))."
						</div>
					";
					
				} else {
					
					echo "
						<div class='dayCalendar' id='d".date("Ynj", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0]))."'>
							".date("j", mktime(0,0,0, $tol2[1], $tol2[2]+$i, $tol2[0]))."
						</div>
					";
					
					
				}
				
			}
			
			//div lezárása
			echo "
				</div>
				<div style='position:relative;'>&nbsp;</div>
			";
			
		} else {
			echo "<hr />A beírt dátum nem helyes.<br/> ÉÉÉÉ-HH-NN formátumban kell megadni.";
		}
		
	}
	
?>