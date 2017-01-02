//Itallap osztály
function itallap(ID, CSID, MID, list_id, name, price, forditott, visible) {
	
	//properties
	this.ID = ID;
	this.CSID = CSID;
	this.MID = MID;
	this.list_id = list_id;
	this.name = name;
	this.price = price;
	this.forditott = forditott;
	this.visible = visible;
	
	this.moveDiv = function(x, y) {
		
		//Div beállítások
		var positionStr = 'margin-left:'+x+'px; margin-top:'+(y-159)+'px; position:absolute;';
		var DivStyle = 'border: 1px solid #000; background-color: #fff; width: 200px; height: 150px; z-index:2;';
		
		//div elhelyezés
		document.getElementById('MoveDiv').setAttribute('style', positionStr + '' + DivStyle);
		
		//AKTUÁLIS SOR IDjának FORMBA TÖLTÉSE
		$("#moveID").val(this.ID);
		
	};
}

//ITAL SOR LÁTHATÓSÁG MÓDOSÍTÁS
function chgVis(i) {
	
	//idből sorszám
	i = i.split("vi")[1];
	
	//HA LÁTHATÓ VOLT -> LÁTHATATLAN
	if (document.getElementById('visibleHidden'+i).value == 1) {
		
		document.getElementById('italNev'+i).setAttribute('disabled', 'disabled');
		document.getElementById('italAr'+i).setAttribute('disabled', 'disabled');
		document.getElementById('italBar'+i).setAttribute('disabled', 'disabled');
		document.getElementById('visibleHidden'+i).value = '0';
		document.getElementById('chg'+i).value = '1';
		$("#vi"+i+".visibleImg").attr("src", "img/eye_off.png");
		
	//LÁTHATATLAN -> LÁTHATÓ
	} else if (document.getElementById('visibleHidden'+i).value == 0) {
		
		document.getElementById('italNev'+i).removeAttribute('disabled');
		document.getElementById('italAr'+i).removeAttribute('disabled');
		document.getElementById('italBar'+i).removeAttribute('disabled');
		document.getElementById('visibleHidden'+i).value = 1;
		document.getElementById('chg'+i).value = 1;
		$("#vi"+i+".visibleImg").attr("src", "img/eye_on.png");
		
	}
}

//ITAL CHG ÁLLAPOT VÁLTOZTATÁS BEKATTINTÁSRA
function setChg(i) {
	document.getElementById('chg'+i).value = '1';
}

//ITALLAP MENTÉSE
function saveItal(length) {
	
	//VÉGIGHALADÁS AZ ÖSSZES SORON ÉS VIZSGÁLAT HOGY TÖRTÉNT E VÁLTOZTATÁS AZ ADOTT SORBAN
	for (var i = 0; i < length; i++) {
		if (document.getElementById('chg'+i).value == 0) {
			
			document.getElementById('italNev'+i).setAttribute('disabled', 'disabled');
			document.getElementById('italAr'+i).setAttribute('disabled', 'disabled');
			document.getElementById('italBar'+i).setAttribute('disabled', 'disabled');
			document.getElementById('ID'+i).setAttribute('disabled', 'disabled');
			document.getElementById('visibleHidden'+i).setAttribute('disabled', 'disabled');
			$("#csoport"+i).attr("disabled", "disabled");
			$("#mertekegyseg"+i).attr("disabled", "disabled");
			
		}
	}
	
	document.getElementById('drFrm').submit();
}

//FLOPPY ICON BEÁLLÍTÁS
function saveBut(length) {
	//document.getElementById("mentGomb").innerHTML = "<a onclick='saveItal("+length+")'><img src='img/floppy.png' width='30' height='30' alt='Mentés' /></a>";
	$("#itallapMenuSave").click(function() {
		saveItal(length);
	});
}

//ÁTHELYEZŐ DOBOZ BEZÁRÁSA
function closeMove() {
	document.getElementById('MoveDiv').setAttribute('style', 'visibility:hidden; position:absolute;');
}

//Forditott ertek allitas
function setFord(i) {
	setChg(i);
	
	if ($("[name='forditott["+i+"]']").val() == 0) {
	
		$("[name='forditott["+i+"]']").val(1);
		
	} else {
	
		$("[name='forditott["+i+"]']").val(0);
		
	}
}

function addFilterDiv() {
	$("#filterDiv").css("visibility", "visible");
}

//Szűrő div törlése
function removeFilterDiv() {
	$("#filterDiv").css("visibility", "hidden");
}

//
function switchChg(id) {
	
	//kikapcsol
	if ($("#"+$.trim(id)).css("left") != "27px") {
	
		$("#"+$.trim(id)).animate({left:"27px"}, 500);
		$("[name='"+id+"']").val(0);
		
	} else {
		$("#"+$.trim(id)).animate({left:"0px"}, 500);
		$("[name='"+id+"']").val(1);
	}
	
	filtering();
}

//szűkítés
function filtering() {
	
	//feltételek
	var chkVisible = $("[name='filterVis']").val();
	var searchStr = $("#itallapSearch").val().toLowerCase();
	
	//bejárás és feltételeknek megfeleltetés
	for (var i = 0; i < italok.length; i++) {
		
		//ellenőrzés és ha nem egyezik akkor sor eltűntetés
		if (chkVisible == 1 && italok[i].visible == 0) {
		
			$("#italSor"+italok[i].ID).css("visibility","hidden");
			$("#italSor"+italok[i].ID).css("position","absolute");
		
		} else if (chkVisible == 0 && italok[i].visible == 0) {
		
			$("#italSor"+italok[i].ID).css("visibility","visible");
			$("#italSor"+italok[i].ID).css("position","relative");
		
		}
		
		//keresés
		if ($("#italSor"+italok[i].ID).css("visibility") != "hidden" && $("#italNev"+i).val().toLowerCase().indexOf(searchStr) == -1) {
		
			$("#italSor"+italok[i].ID).css("visibility","hidden");
			$("#italSor"+italok[i].ID).css("position","absolute");
		
		} else if (italok[i].visible == 1 && $("#italNev"+i).val().toLowerCase().indexOf(searchStr) != -1) {
			
			$("#italSor"+italok[i].ID).css("visibility","visible");
			$("#italSor"+italok[i].ID).css("position","relative");
			
		}
	}
}

//új termék feltöltése
function ujItalFeltolto() {
	
	//Fordított value determining by checked attribute
	var forditott=0;
	if ($('#forditottNew').is(':checked')) {
		forditott = 1;
	}
	
	//`ID`, `PID`, `CSID`, `MID`, `List.ID`, `name`, `price`, `forditott`, `visible`
	$.ajax({
		url: "ajax.php?page=7&pid=" +$("#pid").val()+ "&csid=" +$("#newCsop").val()+ "&mid=" +$("#newMert").val()+ "&list.id=1&name=" +$("#newName").val()+ "&price=" +$("#newAr").val()+ "&pprice=" +$("#newBar").val()+ "&forditott="+forditott,
		type: 'GET',
		async: false,
		success: function(msg) {
			window.location = "index.php?page=6";
		}
	});
}

//change sale price
function changePrice(i) {
	var tmpID = $("[name='saleID"+i+"']").val();
	var tmpPrice = $("[name='sealPrice"+i+"']").val();
	
	//send data to ajax.php
	$.ajax({
		url: "ajax.php?page=14&price="+tmpPrice+"&id="+tmpID,
		type: 'GET',
		async: true,
		error: function() {
			alert("Probléma az internetkapcsolattal! Nem sikerült frissíteni a termék árát.");
		}, success: function() {
			alert("Sikeresen módosítva lett a termék ára.");
		}
	});
}