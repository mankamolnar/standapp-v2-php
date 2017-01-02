<?php session_start(); ob_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
        	
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Stand app</title>

<?php
	
	//Js betöltő modul
	include("php/jsBetolto.php");
	include("php/cssBetolto.php");
	include("php/bejelentkezoModul.php");
	
	//kereso tol ig class
	class keresoDatum {
		
		//properties
		public $tol;
		public $ig;
		
		function __construct($tol, $ig) {
			$this->tol = $tol;
			$this->ig = $ig;
		}
	}
	
	//ital csoportok
	class italcsop {
		public $ID;
		public $csop;
		
		public function __construct($ID, $csop) {
			$this->ID = $ID;
			$this->csop = $csop;
		}
	}
	
	//MÉRTÉKEGYSÉGEK
	class mertekegyseg {
		public $ID;
		public $egyseg;
		
		public function __construct($ID, $egyseg) {
			$this->ID = $ID;
			$this->egyseg = $egyseg;
		}
	}
	
	//italok
	class italok {
		
		public $ID;
		public $CSID;
		public $MID;
		public $list_id;
		public $name;
		public $price;
		public $pprice;
		public $visible;
		public $forditott;
		
		public function __construct($ID, $CSID, $MID, $list_id, $name, $price, $visible, $forditott, $pprice=0) {
			$this->ID = $ID;
			$this->CSID = $CSID;
			$this->MID = $MID;
			$this->list_id = $list_id;
			$this->name = $name;
			$this->price = $price;
			$this->pprice = $pprice;
			$this->visible = $visible;
			$this->forditott = $forditott;
		}
	}
	
	//standsor
	class termek extends italok {
		
		//properties
		public $nyito;
		
		//KONSTRUKTOR
		public function __construct($ID, $CSID, $MID, $list_id, $name, $price, $visible, $nyito, $forditott) {
			
			parent::__construct($ID, $CSID, $MID, $list_id, $name, $price, $visible, $forditott);
			$this->nyito = $nyito;
			
		}
	}
	
	//KOCSMA CLASS
	class pub {
		
		//class properties
		public $ID;
		public $name;
		public $dfee;
		public $isLotto;
		
		public function __construct($ID, $name, $dfee=null, $isLotto=null) {
			$this->ID = $ID;
			$this->name = $name;
			$this->dfee = $dfee;
			$this->isLotto = $isLotto;
		}
	}
	
	//FELHASZNÁLÓ OSZTÁLY
	class User {
		public $ID;
		public $fullname;
		public $pubs = array();

		public function __construct($id,$fullname) {
			$this->ID = $id;
			$this->fullname = $fullname;
		}

		public function setID($id) {
			$this->ID = $id;
		}

		public function setFullname($fullname) {
			$this->fullname = $fullname;
		}

		public function pushPub($pub) {
			array_push($this->pubs, $pub);
		}
	}
	
	//KAPCSOLOTÁBLA OSZTÁLY
	class Kapcsolo {
		protected $pubs = array();
		protected $users = array();

		public function __construct() {
			$this->fetch();
			$this->display();
		}

		public function display() {
			if($_SESSION['tether'] >= 2) {
				echo '<script type="text/javascript" src="js/kapcsoloModul.js"></script>'; //ÁTTENNI JS INCLUDEOLÓBA ###############################################
				echo '<hr />
				<p class="anchor2">Kapcsolótábla</p>';
				//kocsmaadatok JSON-ba
				$pubjson = '[';
				foreach($this->pubs as $key => $val) {
					$pubjson .= '{"ID": "'.$val->ID.'", "name": "'.$val->name.'"}';
					if($key < sizeof($this->pubs)-1) $pubjson .= ",";
				}
				$pubjson .= ']';
				echo "<input type='hidden' id='pubjson' value='".$pubjson."'/>";

				//HTML select létrehozása
				echo "
					<select name='kapcsoloSelect' id='kapcsoloSelect'>
						<option value='NA'>Válassz felhasználót</option>
				";
				for($i=0;$i<sizeof($this->users);$i++) {
					echo "
						<option value='".$this->users[$i]->ID."'>".$this->users[$i]->fullname."</option>
					";
				}
				echo "</select>";

				//Kapcsolótábla
				echo "
				<div id='kapcsolotable'>

				</div>";
			}
		}

		protected function fetch() {
			//Saját kocsmák kigyűjtése
			$query = "
				SELECT `pub`.`ID`, `pub`.`name`
				FROM `UsPuKapcs`
				INNER JOIN `pub` on `UsPuKapcs`.`PID` = `pub`.`ID`
				WHERE `UsPuKapcs`.`UID` = ".$_SESSION['ID']."
				GROUP BY `pub`.`name`
			";
			$result = mysql_query($query);
			while($row = mysql_fetch_assoc($result)) {
				$pub = new pub($row['ID'], $row['name']);
				array_push($this->pubs,$pub);
			}

			//Saját kocsmák alá tartozó userek kigyűjtése
			$query = "
				SELECT `user`.`ID`, `user`.`fullname`
				FROM `UsPuKapcs` 
				INNER JOIN `pub` on `UsPuKapcs`.`PID` = `pub`.`ID`
				INNER JOIN `user` on `UsPuKapcs`.`UID` = `user`.`ID`
				WHERE `UsPuKapcs`.`UID` <> 5 AND 
				(";
			for($i=0;$i<sizeof($this->pubs);$i++) {
				$query .= "
				`UsPuKapcs`.`PID` = ".$this->pubs[$i]->ID."
				";
				if($i<sizeof($this->pubs)-1) $query.= " OR ";
			}	
			$query.= "
				) 
				GROUP BY `user`.`fullname`
			";
			$result = mysql_query($query);
			while($row = mysql_fetch_assoc($result)) {
				$user = new User($row['ID'],$row['fullname']);
				array_push($this->users, $user);
			}
		}
	}
	
	//FUNCTIONS
	//MENU
	function menu($side, $verzio = "") {
		
		//FOOTAGE
		$foot = "Online standoló alkalmazás ".$verzio;
		
		//LEKÉRJÜK VAN E LEZÁRATLAN STAND!
		include "conf/mysql.php";
		
		//Új elszámolás vagy nem befejezett elszámolás
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
		
		//volt-e felvitt stand
		$volteStand =  false;
		
		//dashboard
		include "php/dashboard.php";
		$str = dashboard().$str;
		
		//(FIX MENÜ RÉSZ MINDENKINEK!)
		//ha van nem befejezett standod
		if ($result = mysql_fetch_assoc($query)) {
			
			if($result['UID'] == $_SESSION['ID']){ // saját stand
				$str = $str."<b>Még van be nem fejezett standod!</b><br /><a href='index.php?page=3&id=".$result['ID']."&mod=0' class='anchor2'>".$result['date']."</a><br /><br />
							 <a href='index.php?page=25&id=".$result['ID']."' class='anchor2'>stand átadása</a> <br />";
			}else{
				$str = $str."<b>Nem lehet új standot kezdeni, amíg az előző nincs lezárva!</b><br /><br />";
				
				//stand forced close
				$tmpDate = $result['date'];
				$tmpDate = explode(" ", $tmpDate);
				$tmpDate = explode("-", $tmpDate[0]);
				
				if ($tmpDate[1] < date("m") || $tmpDate[2] < date("d")) {
					$str = $str."<a href='index.php?page=3&id=".$result['ID']."&mod=0&forceclose=1' class='anchor2'>Stand lezárás</a><br />";
				}
			}
			
			$str .= "<hr />";
			$volteStand = true;
		}
		
		//ALKALMAZOTTÓL FELFELE: dashstat
		if ($_SESSION['tether'] > 0) {
			
			//lekérjük van-e likemywifi
			$lmwquery = mysql_query("SELECT * FROM `pubOptions` WHERE `PID` = ".$_SESSION['pub']->ID." AND `option` LIKE 'likemywifi';");
			$lmwresult = mysql_fetch_assoc($lmwquery);
			
			//likemywifi ha van
			if (isset($lmwresult) && $lmwresult['value'] == "1") {
				
				//lekérjük van-e likemywifi
				$lmwquery = mysql_query("SELECT * FROM `pubOptions` WHERE `PID` = ".$_SESSION['pub']->ID." AND `option` LIKE 'likemywifiID';");
				$lmwresult = mysql_fetch_assoc($lmwquery);
				
			}
			
			//Lekérjük be van e kapcsolva a pluszraktar ha igen, nem jelennek meg a grafikonok
			$pluszraktarq = mysql_query("SELECT * FROM `pubOptions` WHERE `option` LIKE 'pluszRaktar';");
			$pluszraktarr = mysql_fetch_assoc($pluszraktarq);
			
			//ha plusz raktár be van kapcsolva akkor a dashstat nem tölt be.
			if ($pluszraktarr['value'] != "1") {
				//statisztikák
				include_once("php/dashstat.php");
				$str .= dashstat($kapcsolat);
			}
			
			
			//likemywifi ha van
			if (is_array($lmwresult)) {
				
				//szülinapok lekérése
				include_once("php/dash-birthday.php");
				$str .= dashBirthday($lmwresult['value']);
				
				//Új elszámolás vagy nem befejezett elszámolás
				$kapcsolat = mysql_connect($szerver, $user, $pass);
				mysql_set_charset('utf8',$kapcsolat);
				if ( ! $kapcsolat )
				{
					die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
				}
				mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
				
			}
		
		//ALKALMAZOTT!
		} else {
			
			//ha nem volt félkész stand, új felvitele
			if ($volteStand == false) {
				
				$str = $str.'<a href="index.php?page=1" class="anchor2">Új elszámolás felvitele</a> <br />
								<a href="index.php?page=logout" class="anchor2">Kijelentkezés</a> <br />
								<hr />';
				
			}
			
		}
		
		//HA MARADT NYITOTT MYSQL KAPCSOLAT ZÁRJUK!
		if (is_resource($kapcsolat)) {
			mysql_close($kapcsolat);
		}
		
		return $str.$foot;
		
	}
	
	//KIJELENTKEZÉS
	if ($_GET['page'] == "logout") {
		session_unset();
		header("Location: index.php");
	}

	//ceck user authority
	function authority($authority) {
		
		if ($authority > $_SESSION['tether']) {
			echo "<b>Nincs jogosultságod a lap megtekintésére</b>";
			exit;
		}
		
	}
?>
			<script src='js/OldalSpecFug.js?<?php echo time(); ?>' type='text/javascript'></script>
		</head>
        
		<body class='signo'>
            
                <div id="HEADER">
					
<?php
	
//új menü megjelenítése
if (isset($_SESSION['tether'])) {
	echo newMenu();	
}
	
?>
				</div>
				
                <div id="MoveDiv" style="visibility:hidden; position:absolute;">
                	
					<form action="index.php?page=8" method="post">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<td>
									<b>Áthelyezés ide:</b>
								</td>
								<td style="text-align:right;">
									<img src="img/close.png" width="30" height="30" onclick="closeMove();" alt="Bezárás!" />
								</td>
							</tr>
						</table>
					
						<p>
						<input type="hidden" name="ID" id="moveID" value="" />
						<input type="text" name="moveTo" /><br />
						<input type="submit" value="Áthelyezés" />
						</p>
					</form>
					
                </div> 
				
				<div id='content'>	
            
<?php

	
	//BEJELENTKEZŐ OLDAL
	if(!isset($_SESSION['plogined']) || $_SESSION['plogined'] == 0){
		
		//Main oldal illetve js ellenőrzés
		if (!isset($_GET['page'])) {
			echo '
				Az oldal megtekintéséhez JavaScript szükséges!
			';
		} else if ($_GET['page'] == "main") {
		
			if (isset($_POST['ulogin'])) {
				cuser();
			}else if (isset($_POST['pselect']) && $_SESSION['ulogined'] == 1){
				cpub();
			}else{
				login_main();
			}
			
		}
		
		//feltöltés bug
		if (isset($_GET['page']) && $_GET['page'] == 5) {
			
			$_SESSION['tmpData'] = $_POST;
			
			echo "
				<b>Átlépted a maximális időkeretet így a program autómatikusan kijelentkeztetett!</b><br />
				A feltöltés befejezéséhez kérlek add meg a felhasználóneved és a jelszavad.<br /><br />
				
				<form action='index.php?page=upLogin' method='post'>
					Felhasznalonev: <input type='text' name='user' /><br />
					Jelszo: <input type='password' name='pass' /><br />
					<input type='submit' value='bejelentkezes' />
				</form>
			";
		
		}

		//bejelentkeztetés feltöltéshez
		if ($_GET['page'] == "upLogin") {
			
			include("conf/mysql.php");
			
			//MYSQL SERVER
			$kapcsolat = mysql_connect($szerver, $user, $pass);
			mysql_set_charset('utf8',$kapcsolat);
			if ( ! $kapcsolat )
			{
				die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
			}
			mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );
			
			//pub adatainak lekérése
			$query = mysql_query("SELECT * FROM `pub` WHERE `pub`.`ID` = ".$_SESSION['tmpData']['pid'].";");
			$pubValues = mysql_fetch_assoc($query);
			
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
					$_SESSION['pub'] = new pub($pubValues['ID'], $pubValues['name'], $pubValues['dfee'], $pubValues['isLotto']);
					$_SESSION['ulogined'] = 1;
					$_SESSION['plogined'] = 1;
				
					break 1;
				}
			
			}

			//ha sikeres a bejelentkezes iranyitsa tovabb a feltoltesre
			if ($_SESSION['ulogined'] == 1) {
				header("Location: index.php?page=5&convertSess=true");
			}
			
			mysql_close($kapcsolat);
		}
		
	//LOGINED
	} else {
		
		include("php/verzioFrissites.php");
		pubAuth($_SESSION['pub']->ID, $_SESSION['ID']);
		
		//PAGE
		if (!isset($_GET['page'])) {
			
			echo menu(3, $verzio['verzio']);
			
		//ÚJ ELSZÁMOLÁS
		} elseif ($_GET['page'] == 1) {
			
			include("php/standlapMegjelenito.php");
			setElszam();
		
		//ELSZÁM FELTÖLTÉS	
		} else if($_GET['page'] == 2) {
			
			include("php/standFeltolto.php");
			ElszamFeltolt();
			echo "<b>Feltöltés megtörtént!</b><br /><br />".menu(3);
		
		//ELSZAM MÓDOSÍTÁS
		} else if($_GET['page'] == 3) {
			
			include("php/standlapMegjelenito.php");
			ElszamMod($_GET['id']);
		
		//KERESŐ
		} else if($_GET['page'] == 4) {
			
			authority(1);
			
			include("php/standKereso.php");
			ElszamSearch();
		
		//ELSZÁMOLÁS MÓDOSÍTÁS
		} else if($_GET['page'] == 5) {
			
			include("php/standFeltolto.php");
			ElszamModFel();
		
		//ITALLAP MÓDOSÍTÁS
		} else if($_GET['page'] == 6) {
			
			authority(2);
			
			include("php/arukeszletKezelo.php");
			ItalMod();
		
		//Ital Módosítás feltöltés
		} else if ($_GET['page'] == 7) {
			
			authority(2);
			
			include("php/arukeszletKezelo.php");
			ItalModFel();
			
		//Hely Modosítás
		} else if ($_GET['page'] == 8) {
			
			authority(2);
			
			include("php/arukeszletKezelo.php");
			MoveDrink();
		
		//FELHASZNÁLÓ KEZELÉS
		} else if ($_GET['page'] == 9) {
			
			authority(2);
			
			include("php/felhasznaloKezelo.php");
			UserSet();
			
		//FELHASZNÁLÓ MODOSÍTÁS
		} else if ($_GET['page'] == 10) {
			
			include("php/felhasznaloKezelo.php");
			UserModify();
			
		//ÚJ FELHASZNÁLÓ FELVITELE
		} else if ($_GET['page'] == 11) {
			
			authority(2);
			
			include("php/felhasznaloKezelo.php");
			NewUser();
			
		//Felhasználó törlés
		} else if ($_GET['page'] == 12) {
			
			authority(2);
			
			include("php/felhasznaloKezelo.php");
			DeleteUser();
			
		//Kocsma kezelő
		} else if ($_GET['page'] == 13) {
			
			authority(3);
			
			include("php/kocsmaKezelo.php");
			pubHandler();
			
		//Kocsma módosítás
		} else if ($_GET['page'] == 14) {
			
			authority(3);
			
			include("php/kocsmaKezelo.php");
			PubModify();
			
		//új kocsma feltöltés
		} else if ($_GET['page'] == 15) {
			
			authority(3);
			
			include("php/kocsmaKezelo.php");
			NewPub();
			
		//Stat
		} else if ($_GET['page'] == 16) {
			
			authority(2);
			
			include("php/statModul.php");
			statisztika();
			
		//dinamikus itallap feltöltés
		} else if($_GET['page'] == 17) {
			
			authority(3);
			
			include("php/dinItalFelModul.php");
			
		//stand törlése
		} else if($_GET['page'] == 18) {
			
			authority(2);
			
			include("php/standFeltolto.php");
			
			
		//személyes beállítások
		} else if($_GET['page'] == 19) {
			
			authority(0);
			
			include("php/szemelyesBeallitasok.php");
			UserLoad();	
			
		//fizetés számoló
		} else if($_GET['page'] == 20) {
			
			authority(1);
			include("php/fizetesSzamolo.php");
			
		//kocsma váltás
		} else if($_GET['page'] == 21) {
			
			chgpub($_POST['pub']);
			
		//Napi Akció beállítás
		} else if ($_GET['page'] == 22) {
			authority(2);
			
			include("php/arukeszletKezelo.php");
			setAkcio();
		
		//Leárazás létrehozása
		} else if ($_GET['page'] == 23) {
			authority(2);
			include("php/arukeszletKezelo.php");
			
			newSeal();
		
		//Leárazás törlése
		} else if ($_GET['page'] == 24) {
			authority(2);
			include("php/arukeszletKezelo.php");
			
			deleteSeal();
		
		//Stand átadása
		} else if ($_GET['page'] == 25) {
			
			include("php/standFeltolto.php");
			standAtad();
			
		//stand törlése
		} else if ($_GET['page'] == 26) {
			authority(2);
			
			include("php/standFeltolto.php");
			standTorles();
		
		//fix kiadás beállítása
		} else if ($_GET['page'] == 27) {
			authority(1);
			
			include("php/kocsmaBeallitasok.php");
			dataActions();
			
		//kocsma beállítások megjelenítése
		} else if ($_GET['page'] == 28) {
			authority(1);
			
			include("php/kocsmaBeallitasok.php");
			kocsmaBeallitasok();
		
		//sorsjegy
		}  else if ($_GET['page'] == 29) {
			authority(2);
			
			include("php/sorsjegy.php");
			sorsjegyBeallitasok();
			
		//szülinapos meghívás
		} else if ($_GET['page'] == 30) {
			authority(1);
			
			include("php/dash-birthday.php");
			sendInvitation();
			
		//szülinapos ellenőrzése
		} else if ($_GET['page'] == 31) {
			authority(0);
			
			include("php/dash-birthday.php");
			checkInvitation();
		}
	}
?>
			</div>
			
			<div id='toTop'>
					
			</div>
        </body>
</html>

<?php ob_end_flush(); ?>
