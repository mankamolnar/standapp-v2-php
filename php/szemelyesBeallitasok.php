<?php
include 'felhasznaloKezelo.php';

function UserLoad() {
	include("conf/mysql.php");
		
	//MYSQL SERVER
	$kapcsolat = mysql_connect($szerver, $user, $pass);
	mysql_set_charset('utf8',$kapcsolat);
	if ( ! $kapcsolat )
	{
		die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
	}
	mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );

	//User adatainek kiszedése DB-ből
	$query = 'SELECT * FROM `user` WHERE `ID`='.$_SESSION['ID'];
	$result = mysql_query($query);
	$row =  mysql_fetch_assoc($result);

	echo "
			<div id='kezeloContainer'>
				<div id='baloldal'>
					<p class='anchor2'>".$_SESSION['name']." adatainak szerkesztése</p>
				</div>
				<div id='jobboldal'>
					
					<form action='index.php?page=10' method='post' id='felhForm'>
						<input type='hidden' name='id' value='".$_SESSION['ID']."' />

						<p class='anchor2'>felhasználónév</p>
						<input type='hidden' name='uname' value='".$row['uname']."' />
						
						<p class='anchor2'>teljes név</p>
						<input type='text' name='fullname' value='".$row['fullname']."' />
						
						<p class='anchor2'>lakcím</p>
						<input type='text' name='lakcim' value='".$row['lakcim']."' />
						
						<p class='anchor2'>tartozkodási hely</p>
						<input type='text' name='tartozkodasi' value='".$row['tartozkodasi']."' />
						
						<p class='anchor2'>születési hely</p>
						<input type='text' name='szulhely' value='".$row['szulhely']."' />
						
						<p class='anchor2'>Születési idö</p>
						<input type='text' name='szulnap' value='".$row['szulnap']."' />
						
						<p class='anchor2'>Végzettség</p>
						<input type='text' name='vegzettseg' value='".$row['vegzettseg']."' />
						
						<p class='anchor2'>Anyja neve</p>
						<input type='text' name='anyjan' value='".$row['anyjan']."' />
					
						<p class='anchor2'>Van-e magánnyugdíj pénztára</p>
						<input type='text' name='maganNyugdij' value='".$row['maganNyugdij']."' />
						
						<p class='anchor2'>lakcím kártya szám</p>
						<input type='text' name='lakcimKsz' value='".$row['lakcimKsz']."' />
					
						<p class='anchor2'>személyi igazolvány száma</p>
						<input type='text' name='szig' value='".$row['szig']."' />
					
						<p class='anchor2'>adószám</p>
						<input type='text' name='adosz' value='".$row['adosz']."' />
					
						<p class='anchor2'>Taj szám</p>
						<input type='text' name='taj' value='".$row['taj']."' />
					
						<p class='anchor2'>Jelszó</p>
						<input type='text' name='pass' value='' onblur='setPass();' />(Ha üres, nem változik)
					
						<p class='anchor2'>telefonszám</p>
						<input type='text' name='phone' value='".$row['phone']."' />
					
						<input type='hidden' name='tether' value='".$row['tether']."'/><br /><br />
						
						<input type='submit' value='Mentés!' />
					</form>
					
				</div>
				
			</div>
		";
		
		//KAPCSOLAT BONTÁS
		mysql_close($kapcsolat);

}

?>