//Jelszó MD5 Kódolása és beillesztése az inputba
function setPass(i) {
	
	$("[name='pass']").val(calcMD5($("[name='pass']").val()));
	
}

function modifyPass() {
	var def =  $("[name='pass']").val();
}

//FELHASZNÁLÓ MÓDOSÍTÁSKOR
function saveumodify() {

	var i = 0;
	while ($("[name='chg"+i+"']").length == 1) {
		if ($("[name='chg"+i+"']").val() == 0) {
		
			$("[name='uname"+i+"']").attr("disabled", "disabled");
			$("[name='fullname"+i+"']").attr("disabled", "disabled");
			$("[name='lakcim"+i+"']").attr("disabled", "disabled");
			$("[name='tartozkodasi"+i+"']").attr("disabled", "disabled");
			$("[name='szulhely"+i+"']").attr("disabled", "disabled");
			$("[name='szulnap"+i+"']").attr("disabled", "disabled");
			$("[name='vegzettseg"+i+"']").attr("disabled", "disabled");
			$("[name='anyjan"+i+"']").attr("disabled", "disabled");
			$("[name='maganNyugdij"+i+"']").attr("disabled", "disabled");
			$("[name='lakcimKsz"+i+"']").attr("disabled", "disabled");
			$("[name='szig"+i+"']").attr("disabled", "disabled");
			$("[name='adosz"+i+"']").attr("disabled", "disabled");
			$("[name='taj"+i+"']").attr("disabled", "disabled");
			$("[name='pass"+i+"']").attr("disabled", "disabled");
			$("[name='phone"+i+"']").attr("disabled", "disabled");
			$("[name='tether"+i+"']").attr("disabled", "disabled");
			
		}
		i++;
	}
	
	$("#frm1").submit();

}

//USER CHANGE BEÁLLÍTÁS
function setuchg(i) {
	$("[name='chg"+i+"']").val("1");
}

//PUB MÓDOSÍTÁS
function sendPubMod() {
	
	var i = 0;
	while ($("[name='chg"+i+"']").length == 1) {
		
		if ($("[name='chg"+i+"']").val() == 0) {
			
			$("[name='name"+i+"']").attr("disabled", "disabled");
			$("[name='pass"+i+"']").attr("disabled", "disabled");
			$("[name='dfee"+i+"']").attr("disabled", "disabled");
			$("[name='isLotto"+i+"']").attr("disabled", "disabled");
			
		}
		
		i++;
	}
	
	$("#frm").submit();
}

//show properties
function showProp(id) {
	
	//belerakandostuff
	var html = "<form action='index.php' method='post'>wow<input type='text' name='fullname' value='' /></form>";
	
	$("#jobboldal").html(html);
	
}
