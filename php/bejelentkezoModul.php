<?php
	
	//LOGIN
	function login_main() {
	
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//QUERY
		$query = mysql_query("SELECT * FROM  `pub` LIMIT 0 , 30;");
		
		//ARRAY LISTÁBA BERAK
		$i = 0;
		while ($result = mysql_fetch_assoc($query)) {
			$pub[$i]["name"] = $result['name'];
			$pub[$i]["id"] = $result['ID'];
			$i++;
		}
		
		//OPTIONs LÉTREHOZÁS
		//$option = "";
		//for ($i = 0; $i < count($pub); $i++) {
		//	$option = $option."<option value='".$pub[$i]["id"]."'>".trim($pub[$i]["name"])."</option>";
		//}
		
		//KIÍRÁS
		echo '
			<p class="welcome">ÜDvözlünk az oldalon!</p><br />
			<form action="index.php?page=main" method="post">
				<p id="loginform">
				<input type="hidden" name="ulogin" value="TRUE" />
				
				Felhasználónév<br />
				<input type="text" name="user" /><br /><br />
				
				Jelszó<br />
				<input type="password" name="pass" /><br /><br />
				
				<input type="submit" value="Bejelentkezés!" />
				</p>
			</form>
		';
		
		mysql_close($kapcsolat);
	}
	
	//CHECK USER
	function cuser() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//query
		$query = mysql_query("SELECT * FROM  `user` WHERE  `uname` LIKE  '".mysql_real_escape_string($_POST['user'])."' LIMIT 0 , 30;");
		
		//SESS ULOGINED SET
		$_SESSION['ulogined'] = 0;
		
		//CHECK
		while ($result = mysql_fetch_assoc($query)) {
			
			//LOAD INTO SESSION
			if ($_POST['user'] == $result['uname'] && md5($_POST['pass']) == $result['pass']) {
				
				$_SESSION['ID'] = $result['ID'];
				$_SESSION['name'] = $result['uname'];
				$_SESSION['pass'] = $result['pass'];
				$_SESSION['tether'] = $result['tether'];
				$_SESSION['pub'] = $_POST['pub'];
				$_SESSION['ulogined'] = 1;
				
				break 1;
			}
			
		}
		
		//PSELECT FORM
		if ($_SESSION['ulogined'] == 1) {
			
			//QUERY
			//$query = mysql_query("SELECT * FROM  `pub` WHERE  `ID` =".$_SESSION['pub']." LIMIT 0 , 30");
			//$result = mysql_fetch_assoc($query);
			
			echo '
						<form action="index.php?page=main" method="post">
							<input type="hidden" name="pselect" value="TRUE" />
							'.pubselect($kapcsolat).'
							<input type="submit" value="Kiválaszt!" />
						</form>
			';
			
			mysql_close($kapcsolat);
			
		//BEJELENTKEZÉS SIKERTELEN
		} else {
			
			mysql_close($kapcsolat);
			login_main();
			echo "<b>valamit elrontottál!</b>";
			
		}
		
		
	}
	
	//CHECK PUB
	function cpub() {
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		};
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//QUERY
		$query = mysql_query("
			SELECT `pub`.*
			FROM `pub`
			LEFT JOIN `UsPuKapcs`
			ON `pub`.`ID` = `UsPuKapcs`.`PID`

			WHERE `UsPuKapcs`.`PID` =".mysql_real_escape_string($_POST['pub'])."
			AND `UsPuKapcs`.`UID` = ".$_SESSION['ID'] );

		//BEJELENTKEZÉS
		if ($result = mysql_fetch_assoc($query)) {
			
			//CLOSE CONN
			mysql_close($kapcsolat);
			
			$_SESSION['plogined'] = 1;
			$_SESSION['pub'] = new pub($result['ID'], $result['name'], $result['dfee'], $result['isLotto']);
			header("Location: index.php");
		
		//SIKERTELEN BEJELENTKEZÉS
		} else {
			
			session_unset();
			
			//CLOSE CONN
			mysql_close($kapcsolat);
			
			login_main();
			echo "<b>Valamit elrontottál!</b>";
			
		}
	}
	
	function pubselect($kapcsolat) {
		
		//QUERY
		$query = mysql_query("
			SELECT `pub`.`ID` , `pub`.`name` 
			FROM `pub`
			LEFT JOIN `UsPuKapcs`
			ON `pub`.`ID` = `UsPuKapcs`.`PID`
			WHERE `UsPuKapcs`.`UID` = ".$_SESSION['ID'].";",$kapcsolat);
		
		//kocsmák inputba
		$tmpPubs = array();
		$i = 0;
		while($result = mysql_fetch_assoc($query)){
			$tmpPubs[$i][0] = $result['ID'];
			$tmpPubs[$i][1] = $result['name'];
			$i++;
		}
		
		//inputok generálása ha be van már jelentkezve első ahova van, többi sima
		if (isset($_SESSION['pub']->ID)) {
			
			$elsofel = "";
			$masodikfel = "";
			$showId = "";
			for ($i = 0; $i < count($tmpPubs); $i++) {
			
				//ha rendszergazda jelenítse meg az ID-t is
				if ($_SESSION['tether'] == 3) {
					$showId = "ID: ".$tmpPubs[$i][0];
				}
				
				//ha ez a bejelentkezett kocsma
				if ($tmpPubs[$i][0] == $_SESSION['pub']->ID) {
					$elsofel = "<select name='pub' id='pubselect'><option value='".$tmpPubs[$i][0]."'>".$tmpPubs[$i][1]." ".$showId."</option>";
				
				//összes többi
				} else {
					$masodikfel .= "<option value='".$tmpPubs[$i][0]."'>".$tmpPubs[$i][1]." ".$showId."</option>";
				}
				
			}
			
			$str = $elsofel.$masodikfel."</select>";
			
		//Nincs bejelentkezve
		} else {
			
			$str = "<select name='pub' id='pubselect'>";
			for ($i = 0; $i < count($tmpPubs); $i++) {
				
				$str = $str."<option value='".$tmpPubs[$i][0]."'>".$tmpPubs[$i][1]."</option>";
				
			}
			$str = $str."</select>";
		}
		
		return $str;
		
	}
	
	//ikonos menu
	function newMenu() {
		
		include("conf/mysql.php");
			
		//Új elszámolás vagy nem befejezett elszámolás
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//beállítások lekérése
		$query = mysql_query("SELECT * FROM `pubOptions` WHERE `pubOptions`.`PID` = '".$_SESSION['pub']->ID."';");
		
		//beállítások betöltése arraybe
		$pubOptions = array();
		while ($result = mysql_fetch_assoc($query)) {
			$pubOptions[$result['option']] = $result['value'];
		}
		
		if (isset($_SESSION['pub']->ID)) {
		
			//ellenőrző query
			$query = mysql_query("
				SELECT * 
				FROM `standok` 
				WHERE `PID` = ".$_SESSION['pub']->ID." 
				AND `finished` = '0' 
				LIMIT 0, 1;
			");
			$result = mysql_fetch_assoc($query);
			
			//menu alap
			$str = "<div id=\"menuContainer\">
						<div id='MenuFooldal'></div>
					 ";
			
			//ha tömböt ad vissza a result akkor nem jelenik meg az új stand felvitele gomb
			if (!is_array($result)) {
				$str = $str."<div id='MenuStand'></div>";
			}
			
			//szülinapos
			if (isset($pubOptions['likemywifi']) && $pubOptions['likemywifi'] == 1) {
				$str .= "<div id='MenuBirthday'></div>";
			}
			
			if (isset($_SESSION['tether'])) {
				
				if ($_SESSION['tether'] > 0) {
					
					$str = $str."<div id='MenuSearch'></div>
								 <div id='MenuStat'></div>
								 <div id='MenuKocsmaBeall'></div>";
				}
						
						
				if ($_SESSION['tether'] > 1) {
				
					$str = $str."<div id='MenuItal'></div>
								 <div id='MenuFelh'></div>";
				}
						
				if ($_SESSION['tether'] > 2) {
					$str = $str."<div id='MenuUp'></div>
								 <div id='MenuKocsma'></div>";
								 
					//ha van sorsjegy kezelő azt is vegye be
					if (isset($pubOptions["sorsjegy"]) && $pubOptions["sorsjegy"] == "1") {
						$str = $str."<div id='MenuSorsjegy'></div>";
					}
				}
			}
			
			//logout, personal setting, icons append to the end
			$str = $str."<div id='MenuSettings'></div>
						 <div id='MenuLogout'></div>
						 
						<form action='index.php?page=21' method='post'>
							<div id='MenuPubSelector'>
								".pubselect($kapcsolat)."
								<input type='submit' value='váltás!' />
							</div>
						</form>
						 
					 </div>";
		}
		
		mysql_close($kapcsolat);
		return $str;
		
	}
	
	//kocsma váltás
	function chgPub($pid) {
		
		unset($_SESSION['pub']);
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//lekérés
		$query = mysql_query("SELECT * FROM `pub` WHERE `pub`.`ID` = $pid;");
		$result = mysql_fetch_assoc($query);
		echo print_r($result);
		
		//sessionbe toltése
		$_SESSION['pub'] = new pub($result['ID'], $result['name'], $result['dfee'], $result['isLotto']);
		echo $_SESSION['pub']->ID;
		
		mysql_close($kapcsolat);
		
		header("Location: index.php");
	}
	
	//pub ellenőrzés
	function pubAuth($pid, $uid) {
		
		include("conf/mysql.php");
		
		//MYSQL SERVER
		$kapcsolat = mysql_connect($szerver, $user, $pass);
		mysql_set_charset('utf8',$kapcsolat);
		if ( ! $kapcsolat )
		{
			die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
		}
		mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
		
		//lekérés
		$query = mysql_query("SELECT * FROM `UsPuKapcs` WHERE `UsPuKapcs`.`PID` = $pid AND `UsPuKapcs`.`UID` = $uid;");
		$result = mysql_fetch_assoc($query);
		
		mysql_close($kapcsolat);
		
		//ha ok
		if (is_array($result)) {
			
		//ha nincs egyezés
		} else {
			session_unset();
			header("Location: index.php");
		}
		
	}
?>