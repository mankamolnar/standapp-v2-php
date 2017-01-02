<?php

//MySQL
include("conf/mysql.php");
		
//MYSQL SERVER
$kapcsolat = mysql_connect($szerver, $user, $pass);
mysql_set_charset('utf8',$kapcsolat);
if ( ! $kapcsolat )
{
	die( "Nem lehet csatlakozni a MySQL kiszolgalohoz!" ); 
}
mysql_select_db( $database) or die  ("Nem lehet megnyitni a köv. adatbázist: $database" .mysql_error()  );

echo "<hr/><p class='anchor2'>Fizetések</p><br/>";
echo "
		<form action='index.php?page=20' method='post'>
			<input type='hidden' name='search' value='TRUE' />";
if(!empty($_POST['year']) && isset($_POST['year']) && isset($_POST['month']) && !empty($_POST['month'])) 
	echo "Év: <input type='text' id='dateset' name='year' value='".$_POST['year']."' /> Hónap:<input type='text' id='dateset2' name='month' value='".$_POST['month']."' /><br />";
else echo "Év: <input type='text' id='dateset' name='year' value='".date('Y')."' /> Hónap:<input type='text' id='dateset2' name='month' value='".date('m')."' /><br />";
echo"
			<input type='submit' value='Keresés!' />
		</form>";

//dátumok beállítása
$tail = '-01 00:00:00';
if(!empty($_POST['year']) && isset($_POST['year']) && isset($_POST['month']) && !empty($_POST['month'])) {
	$startdate = $_POST['year'].'-'.$_POST['month'].$tail;
	$year = substr($startdate, 0,4);
	$month = substr($startdate, 5,2);
	$endmonth = $month+1;
	if (strlen($endmonth) == 1) { $endmonth = '0'.$endmonth; }
	$enddate = $year.'-'.$endmonth.$tail;
}
else {
	$startdate = date('Y-m').$tail;
	$endmonth = date('m')+1;
	if (strlen($endmonth) == 1) { $endmonth = '0'.$endmonth; }
	$enddate = date('Y').'-'.$endmonth.$tail;
}
//standok betöltése
echo "
	<table id='payments'>
		<tr><th>Név</th><th>Fizetés</th></tr>
";
$query = 'SELECT `standok`.*, sum(`standok`.`wtime`) napok, `user`.`fullname`  FROM `standok` INNER JOIN `user` on  `user`.`ID`=`standok`.`UID` WHERE `PID`="'.$_SESSION["pub"]->ID.'" AND (`date`>="'.$startdate.'" AND `date`<"'.$enddate.'") GROUP BY `UID`';
$result = mysql_query($query);
if(mysql_num_rows($result)>0) {
	while($row = mysql_fetch_assoc($result)) {
		echo "<tr><td>".$row['fullname']."</td><td>".$row['napok']*$_SESSION['pub']->dfee." Ft.</td></tr>";
	}
}
else {
	echo "Ebben a hónapban nem voltak fizetések!";
}

//SELECT `standok`.*, sum(`standok`.`wtime`) napok, `user`.`fullname`  FROM `standok` INNER JOIN `user` on  `user`.`ID`=`standok`.`UID` WHERE `PID`="3" AND (`date`>"2013-08-01 00:00:00" AND `date`<"2013-9-01 00:00:00")   GROUP BY `UID`
?>