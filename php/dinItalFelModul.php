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
	
	//kocsmák selectbe
	$select = "";
	$query = mysql_query("SELECT `pub`.* FROM `pub`;");
	while($result = mysql_fetch_assoc($query)) {
		$select = $select."<option value='".$result['ID']."'>".$result['name']."</option>";
	}
	
	//FORM
	echo "
		<p class='anchor2'>Dinamikus arukeszlet feltöltés</p>
		<form action='index.php?page=17' method='post' enctype='multipart/form-data'>
			<p>
			csv file: <input type='file' name='csvf' /><br /><br />
			<select name='pubid'>".$select."</select><br /><br />
			<input type='submit' value='feltolt' />
			</p>
		</form>
	";
	
	//FELTÖLTÉS
	if (isset($_POST['pubid'])) {
		
		if ($_FILES["csvf"]["error"] > 0) {
		
			echo "Hibakód: " . $_FILES["csvf"]["error"] . "<br>";
			
		} else {

			move_uploaded_file($_FILES["csvf"]["tmp_name"], $_FILES["csvf"]["name"]);
			
			$handle = fopen($_FILES["csvf"]["name"], "r");
			$i = 1;
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				
				mysql_query("INSERT INTO `".$database."`.`drinks` (`ID`, `PID`, `CSID`, `MID`, `List.ID`, `name`, `price`, `visible`) VALUES (NULL, '".$_POST['pubid']."', '0', '0', '$i', '".$data[0]."', '".$data[1]."', '1');");
				
				$i++;
			}
			
			unlink($_FILES["csvf"]["name"]);
			
			echo "<b>FELTÖLTÉS SIKERES</b>";
			
		}
	}
	
	mysql_close($kapcsolat);
	
?>