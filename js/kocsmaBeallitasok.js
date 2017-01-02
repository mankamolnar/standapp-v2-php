//switch buttonok kezelése
function switchSetting(id, ajax) {
	
	//elküldendő adatok
	var setting = "";
	var value = "";
	var pid = $("#pubid").val();
	
	//kikapcsol
	if ($("#"+$.trim(id)).css("left") != "27px") {
	
		//animálás elvégzése
		$("#"+$.trim(id)).animate({left:"27px"}, 500);
		
		//value meghatározás
		value = 0;
	
	//Bekapcsol
	} else {
		
		//animálás elvégzése
		$("#"+$.trim(id)).animate({left:"0px"}, 500);
		
		//value meghatározás
		value = 1;
		
	}
	
	//input átírás és setting meghatározás
	if (id == "lottoSw") {
		$("#isLotto").val(value);
		setting = "isLotto";
		
	} else if (id == "modSorSw") {
		$("#modosithatoStand").val(value);
		setting = "modosithatoStand";
		
	} else if (id == "chatboxSw") {
		$("#chatbox").val(value);
		setting = "chatbox";
		
	} else if (id == "NyitoToMaradvanySw") {
		$("#nyitoToMaradvany").val(value);
		setting = "nyitoToMaradvany";
		
	} else if (id == "sorsjegySw") {
		$("#sorsjegy").val(value);
		setting = "sorsjegy";
		
	} else if (id == "ujkiadasSw") {
		$("#ujkiadas").val(value);
		setting = "ujKiadas";
		
	} else if (id == "sajatfogyasztasSw") {
		$("#sajatfogyasztas").val(value);
		setting = "sajatFogyasztas";
		
	} else if (id == "standellenorzesSw") {
		$("#standellenorzes").val(value);
		setting = "standEllenorzes";
		
	} else if (id == "etelfogyasztasSw") {
		$("#etelfogyasztas").val(value);
		setting = "etelFogyasztas";
		
	} else if (id == "pluszraktarSw") {
		$("#pluszraktar").val(value);
		setting = "pluszRaktar";
		
	} else if (id == "bankkartyaSw") {
		$("#jutalek").val(value);
		setting = "bankkartya";
		
	} else if (id == "egyebforgalomSw") {
		$("#egyebforgalom").val(value);
		setting = "egyebForgalom";
		
	} else if (id == "jutalekSw") {
		$("#jutalek").val(value);
		setting = "jutalek";
		
	} else if (id == "likemywifiSw") {
		$("#likemywifi").val(value);
		setting = "likemywifi";
		
	}
	
	//ajax ha szükséges
	if (ajax) {
		
		//AJAX ELVÉGZÉSE
		$.ajax({
			url: "ajax.php?page=29&setting="+setting+"&value=" +value+ "&pid=" +pid,
			type: 'GET',
			async: true,
			success: function() {
			
			},
			error: function() {
				setTimeout(function() { switchSetting(id, false);}, 2000);
			}
		});
	}
}

//kiadás törlése
function kiadTorles(id) {
	
	$("[name='action']").val("sorTorles");
	$("[name='fixKiadId']").val($("#fixKiadId"+id).val());
	$("[name='fixKiadValue']").val($("#fixKiadNev"+id).val());
	$("#fixSorFrm1").submit();
	
}

//kiadás frissítése
function kiadMentes(id) {
	$("[name='action']").val("sorFrissites");
	$("[name='fixKiadId']").val($("#fixKiadId"+id).val());
	$("[name='fixKiadValue']").val($("#fixKiadNev"+id).val());
	$("#fixSorFrm1").submit();
}

//kiadás törlése
function sajatfogytorol(id) {
	
	$("[name='action']").val("sajatfogyT");
	$("[name='sajatkiadid']").val($("#sajatfogyid"+id).val());
	$("[name='sajatkiadvalue']").val($("#sajatfogyvalue"+id).val());
	$("#sajatfogyaFrm").submit();
	
}

//kiadás törlése
function sajatfogymentes(id) {
	
	$("[name='action']").val("sajatfogyF");
	$("[name='sajatkiadid']").val($("#sajatfogyid"+id).val());
	$("[name='sajatkiadvalue']").val($("#sajatfogyvalue"+id).val());
	$("#sajatfogyaFrm").submit();
	
}

//egyéb forgalom törlése
function egyebforgtorol(id) {
	
	$("#egyebforgFrm [name='action']").val("egyebforgdelete");
	$("[name='egyebforgid']").val($("#egyebforgid"+id).val());
	$("[name='egyebforgvalue']").val($("#egyebforgvalue"+id).val());
	$("#egyebforgFrm").submit();
	
}

//egyéb forgalom módosítása
function egyebforgmentes(id) {
	
	$("#egyebforgFrm [name='action']").val("egyebforgmentes");
	$("[name='egyebforgid']").val($("#egyebforgid"+id).val());
	$("[name='egyebforgvalue']").val($("#egyebforgvalue"+id).val());
	$("#egyebforgFrm").submit();
	
}

//kiadás törlése
function prmentes(id) {
	
	$("[name='action']").val("prfriss");
	$("[name='prid']").val($("#prid"+id).val());
	$("[name='prname']").val($("#prvalue"+id).val());
	$("#praFrm").submit();
	
}