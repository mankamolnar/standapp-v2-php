//StandSor class
function stSor(id, did, ny, v, o, m, f, e, a, csid, forditott, pluszRaktar) {
	
	//simple properties
	this.ID = id;
	this.DID = $.trim(did); //139 KV
	this.ossz = o;
	this.fogyas = f;
	this.ertek = e;
	this.ar = parseFloat(a);
	this.csid = csid;
	this.forditott = forditott
	this.pluszRaktar = pluszRaktar;
	
	//hard properties
	//nyito
	if ($.isArray(ny)) {
		this.nyito = ny;
		for (var i = 0; i < this.nyito.length; i++) {
			this.nyito[i] = parseFloat(this.nyito[i]);
		}
	} else {
		this.nyito = parseFloat(ny);
	}
	
	//maradvany
	if ($.isArray(m)) {
		this.maradvany = m;
		for (var i = 0; i < this.maradvany.length; i++) {
			this.maradvany[i] = parseFloat(this.maradvany[i]);
		}
		
	} else {
		this.maradvany = parseFloat(m);
	}
	
	//vételezés
	if (typeof v[1] !== "undefined") {
		this.vetel = v[0];
		this.pvetel = v[1];
		
	} else {
		this.vetel = v;
		this.pvetel = false;
	}
	
	//functions
	//CheckLine
	this.checkLine = function(i) {
		var ans = true;
		
		//ha nem tömb a vétel
		if (!$.isArray(this.vetel) && ( isNaN(this.vetel) || $("#vetel"+i).val() == "" || isNaN($("#vetel"+i).val()) ) ) {
			console.log(1);
			ans = false;
		}
		
		//ha pvetel
		if (this.pvetel != false && ( isNaN(this.pvetel) || $("#pvetel"+i).val() == "" || isNaN($("#pvetel"+i).val()) ) ) {
			console.log(2);
			ans = false;
		}
		
		//ha nem tömb a maradvány
		if (!$.isArray(this.maradvany) && ( isNaN(this.maradvany) || isNaN($("#maradvany"+i).val()) ) ) {
			console.log(3);
			ans = false;
		}
		
		//ha tömb a maradvány
		if ($.isArray(this.maradvany)) {
			var tmpraktarak = $("[name='pluszRaktarak']").val().split(";");
			for (var j = 0; j < this.maradvany.length; j++) {
				if (j == 0) {
					if (isNaN(this.maradvany[j]) && $("#maradvany"+i+"0").val() != "") {
						console.log(4);
						ans = false;
					}
				} else {
					
					if (isNaN(this.maradvany[j]) && $("#maradvany"+i+""+tmpraktarak[j-1]).val() != "") {
						console.log(4);
						ans = false;
					}
				}
				
			}
		}
		
		//Fogyás ellenőrzés
		if ((this.fogyas == "NULL" || this.fogyas >= 0) && parseInt($.trim($("#fogyas"+i).html())) >= 0) {
			
		} else {
			ans = false;
			console.log(5);
		}
		
		return ans;
	};
	
	//VESSZŐ PONTTÁ ALAKÍTÁSA
	this.convertVesszo = function(e, i) {
		//HA VESSZŐ
		if (e.which == 188) {
			$("#maradvany"+i).val($("#maradvany"+i).val().replace(',','.'));
		}
	};
	
	//VESSZŐ PONTTÁ ALAKÍTÁSA VÉTELBEN
	this.convertVesszoV = function(e, i) {
		//HA VESSZŐ
		if (e.which == 188) {
			$("#vetel"+i).val($("#vetel"+i).val().replace(',','.'));
		}
	};
	
	//SOROK SZÁMOLÁSA i = line's id, r is defines that are we need vetel filling with 0 or not
	this.countAll = function(i, r) {
		
		var doit = true;
		
		//Vétel nullára állítása mező elhagyás után
		if ($("#vetel"+i).val() == "" && r == 1) {
			$("#vetel"+i).val("0");
		}
		
		//HA NEM FORDITOTT KEPLET
		if (standSor[i].forditott == 0) {
			
			//Maradvany módosításra kerüljön - e
			var cM = true;
			
			//ideiglenes vételezés és maradvany értékek
			if (this.pluszRaktar == true) {
				var tM = new Array();
				var tV = new Array();
			} else {
				var tM = 0;
				var tV = 0;
			}
			
			//vétel ellenőrzés
			if (this.pluszRaktar == true) {
				
				//sima vétel
				if (!isNaN($("#vetel"+i).val()) && $("#vetel"+i).val() != "") {
					
					tV[0] = Math.round( (parseFloat($("#vetel"+i).val()) * 100) );
					
				} else {
				
					doit = false;
					
				}
				
				//pluszvétel
				if (!isNaN($("#pvetel"+i).val()) && $("#pvetel"+i).val() != "") {
					
					tV[1] = Math.round( (parseFloat($("#pvetel"+i).val()) * 100) );
					
				} else {
				
					tV[1] = 0;
					
				}
				
			} else {
				
				if (!isNaN($("#vetel"+i).val()) && $("#vetel"+i).val() != "") {
					
					tV = Math.round( (parseFloat($("#vetel"+i).val()) * 100) );
					
				} else {
				
					doit = false;
					
				}
			}
			
			
			//maradvány ellenőrzés
			if (this.pluszRaktar == true) {
				
				var tmpraktarak = $("[name='pluszRaktarak']").val();
				tmpraktarak = tmpraktarak.split(";");
				for (var fm = 0; fm < tmpraktarak.length-1; fm++) {
					
					if ((!isNaN($("#maradvany"+i+""+tmpraktarak[fm]).val()) && $("#maradvany"+i+""+tmpraktarak[fm]).val() != "")) {
						
						
						tM[fm+1] = Math.round( (parseFloat($("#maradvany"+i+""+tmpraktarak[fm]).val()) * 100) );
						
					} else if ($("#maradvany"+i+""+tmpraktarak[fm]).val() == "") {
					
						//cM = false;
						tM[fm+1] = null;
						
					} else {
					
						doit = false;
						
					}
					
				}
				
				//PULT
				if (!isNaN($("#maradvany"+i+"0").val()) && $("#maradvany"+i+"0").val() != "") {
				
					tM[0] = Math.round( (parseFloat($("#maradvany"+i+"0").val()) * 100) );
					
				} else if ($("#maradvany"+i+"0").val() == "") {
				
					cM = false;
					tM[0] = null;
					
				} else {
				
					doit = false;
					
				}
				
			} else {
				
				if (!isNaN($("#maradvany"+i).val()) && $("#maradvany"+i).val() != "") {
				
					tM = Math.round( (parseFloat($("#maradvany"+i).val()) * 100) );
					
				} else if ($("#maradvany"+i).val() == "") {
				
					cM = false;
					
				} else {
				
					doit = false;
					
				}
			}
			
			//tmp Nyito és összesen
			if (this.pluszRaktar == true) {
				var tO = tV[0] + tV[1];
				var tNy = this.nyito;
				for (var s = 0; s < tNy.length; s++) {
					tO += Math.round(this.nyito[s] * 100);
				}
				
			} else {
				var tNy = Math.round(this.nyito * 100);
				var tO = tV + tNy;
			}
			
			//MARADVANY, fogyas MODOSÍTÁSA CLASSBAN
			if (cM == true) {
				this.maradvany = tM;
				if (this.pluszRaktar == true) {
					var taM = 0;
					for (var o = 0; o < tM.length; o++) {
						taM += tM[o];
					}
					
					this.fogyas = (tO - taM) / 100;
				} else {
					this.fogyas = (tO - tM) / 100;
				}
				
				this.ertek = Math.round(this.fogyas * this.ar);
			
			} else {
				$("#fogyas"+i).val("-");
				$("#ertek"+i).val("-");
				
				this.fogyas = "NULL";
				this.ertek = 0;
			}
			
			//VÁLTOZOK MODOSÍTÁSA A CLASSBAN ÉS SZÁMOLÁSOK
			if (doit == true) {
				
				if (this.pluszRaktar == 1) {
					this.vetel = tV[0];
					this.pvetel = tV[1];
				} else {
					this.vetel = tV / 100;
				}
				this.ossz = tO / 100;
			}
			
			//html()-ek
			if (!isNaN(this.ossz)) {
				$("#osszesen"+i).html(this.ossz);
			}
			if (!isNaN(this.fogyas) && cM == true) {
				$("#fogyas"+i).html(this.fogyas);
			}
			if (!isNaN(this.ertek) && cM == true) {
				$("#ertek"+i).html(this.ertek);
			}
		
		//HA FORDITOTT KEPLET
		} else if (standSor[i].forditott == 1) {
		
			//Maradvany módosításra kerüljön - e
			var cM = true;
			
			//ideiglenes vételezés és maradvany értékek
			if (this.pluszRaktar == true) {
				var tM = new Array();
				var tV = new Array();
			} else {
				var tM = 0;
				var tV = 0;
			}
			
			//vétel ellenőrzés
			if (this.pluszRaktar == true) {
				
				//sima vétel
				if (!isNaN($("#vetel"+i).val()) && $("#vetel"+i).val() != "") {
					
					tV[0] = Math.round( (parseFloat($("#vetel"+i).val()) * 100) );
					
				} else {
				
					doit = false;
					
				}
				
				//pluszvétel
				if (!isNaN($("#pvetel"+i).val()) && $("#pvetel"+i).val() != "") {
					
					tV[1] = Math.round( (parseFloat($("#pvetel"+i).val()) * 100) );
					
				} else {
				
					tV[1] = 0;
					
				}
				
			} else {
				
				if (!isNaN($("#vetel"+i).val()) && $("#vetel"+i).val() != "") {
					
					tV = Math.round( (parseFloat($("#vetel"+i).val()) * 100) );
					
				} else {
				
					doit = false;
					
				}
			}
			
			//maradvány ellenőrzés
			if (this.pluszRaktar == true) {
				
				var tmpraktarak = $("[name='pluszRaktarak']").val();
				tmpraktarak = tmpraktarak.split(";");
				for (var fm = 0; fm < tmpraktarak.length-1; fm++) {
					
					if ((!isNaN($("#maradvany"+i+""+tmpraktarak[fm]).val()) && $("#maradvany"+i+""+tmpraktarak[fm]).val() != "")) {
						
						
						tM[fm+1] = Math.round( (parseFloat($("#maradvany"+i+""+tmpraktarak[fm]).val()) * 100) );
						
					} else if ($("#maradvany"+i+""+tmpraktarak[fm]).val() == "") {
					
						//cM = false;
						tM[fm+1] = null;
						
					} else {
					
						doit = false;
						
					}
					
				}
				
				//PULT
				if (!isNaN($("#maradvany"+i+"0").val()) && $("#maradvany"+i+"0").val() != "") {
				
					tM[0] = Math.round( (parseFloat($("#maradvany"+i+"0").val()) * 100) );
					
				} else if ($("#maradvany"+i+"0").val() == "") {
				
					cM = false;
					tM[0] = null;
					
				} else {
				
					doit = false;
					
				}
				
			} else {
				
				if (!isNaN($("#maradvany"+i).val()) && $("#maradvany"+i).val() != "") {
				
					tM = Math.round( (parseFloat($("#maradvany"+i).val()) * 100) );
					
				} else if ($("#maradvany"+i).val() == "") {
				
					cM = false;
					
				} else {
				
					doit = false;
					
				}
			}
			
			//NYITÓ ÉS ÖSSZESEN
			if (this.pluszRaktar == true) {
				var tO = tV[0] + tV[1];
				var tNy = this.nyito;
				for (var s = 0; s < tNy.length; s++) {
					tO += Math.round(this.nyito[s] * 100);
				}
				
			} else {
				var tNy = Math.round(this.nyito * 100);
				var tO = tV + tNy;
			}
			
			//MARADVANY, fogyas MODOSÍTÁSA CLASSBAN
			if (cM == true) {
				this.maradvany = tM;
				this.fogyas = (tM - tO) / 100;
				this.ertek = Math.round(this.fogyas * this.ar);
				
			} else {
				
				$("#fogyas"+i).val("-");
				$("#ertek"+i).val("-");
				
				this.fogyas = "NULL";
				this.ertek = 0;
				
			}
			
			if (cM == true) {
				this.maradvany = tM;
				if (this.pluszRaktar == true) {
					var taM = 0;
					for (var o = 0; o < tM.length; o++) {
						taM += tM[o];
					}
					
					this.fogyas = (taM - tO) / 100;
				} else {
					this.fogyas = (tM - tO) / 100;
				}
				
				this.ertek = Math.round(this.fogyas * this.ar);
			
			} else {
				$("#fogyas"+i).val("-");
				$("#ertek"+i).val("-");
				
				this.fogyas = "NULL";
				this.ertek = 0;
			}
			
			//VÁLTOZOK MODOSÍTÁSA A CLASSBAN ÉS SZÁMOLÁSOK
			if (doit == true) {
				
				if (this.pluszRaktar == 1) {
					this.vetel = tV[0];
					this.pvetel = tV[1];
				} else {
					this.vetel = tV / 100;
				}
				this.ossz = tO / 100;
			}
			
			//html()-ek
			if (!isNaN(this.ossz)) {
				$("#osszesen"+i).html(this.ossz);
			}
			if (!isNaN(this.fogyas) && cM == true) {
				$("#fogyas"+i).html(this.fogyas);
			}
			if (!isNaN(this.ertek) && cM == true) {
				$("#ertek"+i).html(this.ertek);
			}
			
			
		}
		
		//ha fogyás helyes visszaszínez alapra
		if (standSor[i].checkLine(i) && doit) {
			
			$("#stl"+i).attr("class", "sor"+(i%2));
			
		//sor pirosra színezése
		} else {
			
			if (r == 1) {
				$("#stl"+i).attr("class", "sorerr");
			}
		}
		
	};
	
	//NYITO -> MARADVÁNY COPY
	this.CopyNyito = function(i) {
		
		
		if ($("#vetel"+i).val() == "") {
			$("#vetel"+i).val("0");
		}
		
		
		//MÁSOLÁS
		$("#maradvany"+i).val((Math.round(this.nyito * 100) + Math.round( (parseFloat($("#vetel"+i).val()) * 100) ) ) / 100);

	};
	
	//C BETŰ LETÖRLÉS
	this.deleteC = function(i, r) {
		if (r == 1) {
			
			$("#vetel"+i).val($("#vetel"+i).val().replace("c", ""));
			$("#vetel"+i).val($("#vetel"+i).val().replace("C", ""));
			
		} else if (r == 2) {
			
			$("#maradvany"+i).val($("#maradvany"+i).val().replace("c", ""));
			$("#maradvany"+i).val($("#maradvany"+i).val().replace("C", ""));
			
		}
		
	};
	
}

//billentyű leütések ellenőrzése, c betű és pont alakítás
function checkKeyPressed(i) {
	
	//normál stand
	if (modules[type] == "standlap") {
		
		//propertik változóba
		var tmpVetel = $("#vetel"+i).val();
		if ($("#pluszRaktar").val() == "1") {
			var tmpMaradvany = new Array();
			var tmpraktarak = $("[name='pluszRaktarak']").val();
			tmpraktarak = tmpraktarak.split(";");
			tmpMaradvany[0] = $("#maradvany"+i+"0").val();
			for (var j = 0; j < tmpraktarak.length-1; j++) {
				tmpMaradvany[j+1] = $("#maradvany"+i+""+tmpraktarak[j]).val();
			}
		} else {
			var tmpMaradvany = $("#maradvany"+i).val();
		}
		
		//PLUSZRAKTÁR NÉLKÜLI MŰVELETEK
		if ($("#pluszRaktar").val() == 0) {
			
			//c betű ellenőrzés
			if (tmpVetel.search("c") != -1 || tmpVetel.search("C") != -1) {
				standSor[i].CopyNyito(i);
				standSor[i].deleteC(i, 1);
			}
			if (tmpMaradvany.search("c") != -1 || tmpMaradvany.search("C") != -1) {
				standSor[i].CopyNyito(i);
				standSor[i].deleteC(i, 2);
			}
			
			//vessző csekk
			if (tmpVetel.search(",") != -1) {
				$("#vetel"+i).val($("#vetel"+i).val().replace(",", "."));
			}
			if (tmpMaradvany.search(",") != -1) {
				$("#maradvany"+i).val($("#maradvany"+i).val().replace(",", "."));
			}
			
		//PLUSZRAKTÁROS MŰVELETEK
		} else if ($("#pluszRaktar").val() == 1) {
			
			//vétel maradvány "-"-ra új bevételezés doboz
			if (tmpVetel.search("-") != -1 && $("#pvetel"+i).length == 0) {
				
				//modulszám meghatározása. Ha van akciós fogyás akkor 1 egyébként nulla
				var tmpmodul = 0;
				if ($("[name='aFogy0']").length == 1) {
					tmpmodul = 1;
				}
				
				var tmpfinval = $("#vetel1").attr("onblur").split(";")[1].split("(")[1].split(")")[0];
				$("#vetel"+i).after('<br /><input autocomplete="off" class="inputTextSmall" type="text" name="pstandVetel['+i+']" id="pvetel'+i+'" onfocus="setCo('+tmpmodul+', '+i+', 0, 1);" onblur="standSor['+i+'].countAll('+i+', 1); countFinVal('+tmpfinval+');" onkeyup="checkKeyPressed('+i+');" value="">');
			}
			
			//ha nincs benne minusz és van plusz rublika akkor azt törölje ki, ha volt benne adat másolja át
			if (tmpVetel.search("-") == -1 && $("#pvetel"+i).length == 1) {
				if ($("#pvetel"+i).val() != "") {
					$("#vetel"+i).val($("#pvetel"+i).val());
				}
				$("#pvetel"+i).remove();
			}
		
			//vessző csekk
			if (tmpVetel.search(",") != -1) {
				$("#vetel"+i).val($("#vetel"+i).val().replace(",", "."));
			}
			
			//plusz vétel vessző csekk
			if ($("#pvetel"+i).length == 1) {
				if ($("#pvetel"+i).val().search(",") != -1) {
					$("#pvetel"+i).val($("#pvetel"+i).val().replace(",", "."));
				}
			}
			
			//maradványban vessző csekk
			for (var j = 0; j < tmpMaradvany.length; j++) {
				if (tmpMaradvany[j].search(",") != -1) {
					if (j == 0) {
						$("#maradvany"+i+"0").val($("#maradvany"+i+"0").val().replace(",", "."));
						
					} else {
						$("#maradvany"+i+""+tmpraktarak[j-1]).val($("#maradvany"+i+""+tmpraktarak[j-1]).val().replace(",", "."));
						
					}
					
				}
			}
			
		}
		
	} else if (modules[type] == "akcios") {
		
		//vessző csekk
		if ($("[name='aFogy"+i+"']").val().search(",") != -1) {
			$("[name='aFogy"+i+"']").val($("[name='aFogy"+i+"']").val().replace(",", "."));
		}
		
	}
	
}

//Koordináta beállítás léptetéshez
function setCo(ttype, i, o, pl) {
	pl = typeof pl !== 'undefined' ? pl : 0;
	
	type = ttype;
	sor = i;
	oszlop = o;
	pline = pl;
	
	//sor hoveresre színezése
	if (modules[type] == "standlap") {
		$("#stl"+sor).attr("class", "sorhover");
	} else if (modules[type] == "modsor") {
		$("#modsorline"+sor).attr("class", "sorhover");
	}
}

//ALKOHOLOS ITALOK SZÁMOLÁSA
function countFinVal(type) {
	var all = 0;
	for (var i = 0; i < standSor.length; i++) {
		if (!isNaN(standSor[i].ertek) && standSor[i].csid == type && standSor[i].ertek != -1) {
			all += parseInt(standSor[i].ertek);
		}
	}
	$("#osszInp"+type).val(all);
	countFinalForgalom();
}

//KIADAS TÁBLÁHOZ SOR HOZZÁADÁS
function addRowToStand() {
	
	//utolsó sor megkeresése
	var i = 0;
	while (typeof $("#kiadasNev"+i).val() !== "undefined") {
		i++;
	}
	
	//kiadás megkeresése a modulokban. hivatkozási pont meghatározás
	var j = 0;
	while (modules[j] != "kiadas") {
		j++;
	}
	
	//inputok
	var inp1 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 0);' onblur='kiadasFocusEvent("+i+");' type='text' name='kiadasNev["+i+"]' id='kiadasNev"+i+"' value=''><input type='hidden' name='kid["+i+"]' value='-1'>";
	var inp2 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 1);' onblur='countKiadAll();kiadasFocusEvent("+i+");' type='text' name='kiadasAr["+i+"]' id='kiadasAr"+i+"'  value='' />";
	
	//ha nincs lotto kiadás sor akkor a végére amugy a lotto sor elé
	if ($("#lottoPerc").length != 1) {

		$("#KiadasOsszTr").before("<tr align='center'><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
		
	} else {
		
		$("#lottoPercTr").before("<tr align='center'><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
	}

}

//FACEBOOK TERMINAL KÜLÖNBSÉG ÉS ÉRTÉK SZÁMOLÁS
function countTermKul(i) {
	$("#termKul"+i).html($("#internetBe"+i).val()-$("#internetKi"+i).val());
	$("#termErt"+i).html($("#termKul"+i).html()-($("#ZarIbe"+i).html()-$("#ZarIki"+i).html()));
	
}

//LOTTO SOR OHZZÁADÁS
function addTrToLot() {
	
	//utolso sor
	var lasts = lastSor("lotto");
	lasts++;
	
	//lotto megkeresése a modulokban. hivatkozási pont meghatározás
	var j = 0;
	while (modules[j] != "lotto") {
		if (j == modules.length-1) { break; }
		j++;
	}
	
	var inp1 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+lasts+", 0);' type='text' name='LotNet["+lasts+"]' id='lotNet"+lasts+"' value='' onblur='countLotPer();'>";
	var inp2 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+lasts+", 1);' type='text' name='LotAll["+lasts+"]' id='lotAll"+lasts+"' value='' onblur='countLotAll();'>";
	
	$(".lottoSor"+(lasts-1)).after("<tr align='center' class='lottoSor"+lasts+"' id='sor"+(lasts%2)+"'><td class='td1'>"+(lasts+1)+".</td><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
}																																		

//Lotto értékek összegzése
function countLotAll() {
	var i=0;
	var all=0;
	while ($('.lottoSor'+i).length == 1) {
		
		if (!isNaN($('#lotAll'+i).val()) && $.trim($('#lotAll'+i).val()) != "") {
			all += parseInt($('#lotAll'+i).val());
		}
		
		i++;
	}
	$('#lottoAllI').val(all);
	countFinalForgalom();
}

//lotto 1%
function countLotPer() {
	var i=0;
	var all=0;
	while ($('.lottoSor'+i).length == 1) {
		
		if (!isNaN($('#lotNet'+i).val()) && $('#lotNet'+i).val() != "") {
			all += parseInt($('#lotNet'+i).val());
		}
		
		i++;
	}
	all = parseFloat(all)/100;
	$('#lottoPerc').val(all*-1);
	countKiadAll();
}

//Form küldéskor ellenőrzés
function sendElszamCheck(tmpInterval, saveit) {
	
	saveit = saveit || 2;
	
	//interval leállítása
	if (saveit == 2) {
		clearInterval(tmpInterval);
	}
	
	//ellenőrzés hogy van-e lottó
	var lotto = 0;
	for (var i = 0; i < modules.length; i++) {
		if (modules[i] == "lotto") {
			lotto = 1;
		}
	}
	
	//internet kapcsolat tesztelése
	var vannet = false;
	$.ajax({
		url: "ajax.php?page=4",
		type: 'GET',
		async: false,
		success: function(msg) {
			if (msg == 1) {
				vannet = true;
			}
		},
		error: function() {
			vannet = false;
		}
	});
	
	//akkor fusson le a zárás ha van internet
	if (vannet) {
	
		//properties
		var doit = true;
		var err = "";
		var j = 0;
		var elsohiba = new Array();
		elsohiba[0] = -1;
		
		//FORMAI ELLENŐRZÉS
		//STANDSOROK ELLENŐRZÉSE
		for (var i = 0; i < standSor.length; i++) {
			
			//ha van pluszRaktár
			if ($("#pluszRaktar").val() == 1) {
				
				//NEM KÜLDHETI EL A FORMOT, HIBAÜZENET ÍRÁSA, J max 6 hiba, pirosra csíkozás
				var tmpraktarak = $("[name='pluszRaktarak']").val();
				tmpraktarak = tmpraktarak.split(";");
				var sorhiba = false;
				for (var r = 0; r < tmpraktarak.length-1; r++) {
					if ($("#maradvany"+i+""+tmpraktarak[r]).val() == "") {
						sorhiba = true;
					}
				}
				
				//HA FORMAI HIBA VAN A SORBAN
				if (standSor[i].checkLine(i) == false || sorhiba) {
					
					//NEM KÜLDHETI EL A FORMOT, HIBAÜZENET ÍRÁSA, J max 6 hiba, pirosra csíkozás
					doit = false;
					err += "\n"+(i+1)+".";
					j++;
					$("#stl"+i).attr("class", "sorerr");
					
					if (elsohiba[0] == -1) {
						elsohiba[0] = i;
						elsohiba[1] = $("#stl"+i).offset().top;
					}
					continue;
					
				}
				
			//alap küldés
			} else {
				
				//HA FORMAI HIBA VAN A SORBAN
				if (standSor[i].checkLine(i) == false || $("#maradvany"+i).val() == "") {
					
					//NEM KÜLDHETI EL A FORMOT, HIBAÜZENET ÍRÁSA, J max 6 hiba, pirosra csíkozás
					doit = false;
					err += "\n"+(i+1)+".";
					j++;
					$("#stl"+i).attr("class", "sorerr");
					
					if (elsohiba[0] == -1) {
						elsohiba[0] = i;
						elsohiba[1] = $("#stl"+i).offset().top;
					}
					continue;
					
				}
				
			}
			
		}
		
		//módosítható sorok ellenőrzése
		if ($("[name='modSorNev0']").length == 1) {
			
			var i = 0;
			while ($("[name='modSorNev"+i+"']").length == 1) {
				
				//ha az ID nem -1 akkor ellenőrzendő sor
				if ($("[name='modSorNev"+i+"']").val() != "" && $("[name='modSorDel"+i+"']").val() != 1 ) {
					
					//ellenőrzi h van-e secure input, ha van hidden inputot kitölti az osztályban tárolt adattal
					if ($("#secureMnev"+i).length == 1) {
						$("[name='modSorNev"+i+"']").val(modSorValues[i][0]);
						$("[name='modSorNyito"+i+"']").val(modSorValues[i][1]);
					}
					
					//ellenőrzi hogy minden oszlop ki lett e töltve
					if (($("[name='modSorNev"+i+"']").val() == "" || $("[name='modSorVetel"+i+"']").val() == "" || isNaN($("[name='modSorVetel"+i+"']").val()) || $("[name='modSorMarad"+i+"']").val() == "" || isNaN($("[name='modSorMarad"+i+"']").val()) || $("[name='modSorAr"+i+"']").val() == "" || isNaN($("[name='modSorAr"+i+"']").val()) || parseInt($("#modSorErtek"+i).html()) < 0)) {
						
						//nem engedi elküldeni a formot, pirosra színez
						doit = false;
						$("#modsorline"+i).attr("class", "sorerr");
						
						//elsőhiba koordinátáit beállítja
						if (elsohiba[0] == -1) {
							elsohiba[0] = i;
							elsohiba[1] = $("#modsorline"+i).offset().top;
							err += "\n A módosítható sorok "+(i+1)+". sorában hiba van!";
						}
					
					}
				}
				
				i++;
				
			}
			
		}
		
		//bevételezések ellenőrzése
		if ($("#bevetelNev0").length == 1) {
			
			var b = 0;
			while ($("#bevetelNev"+b).length == 1) {
				if (($("#bevetelNev"+b).val() == "" && $("#bevetelAr"+b).val() != "") || ($("#bevetelNev"+b).val() != "" && $("#bevetelAr"+b).val() == "")) {
					doit = false;
					err += "\n A bevételezésben félig üres sort hagytál";
				}
				
				b++;
			}
			
		}
		
		//KIADÁSOK
		var i = 0;
		while ($("#kiadasNev"+i).length == "1") {
			
			//üres sor
			if ($("#kiadasNev"+i).val() == "" && $("#kiadasAr"+i).val() == "" && saveit == 2) {
				
				$("#kiadasNev"+i).attr("disabled", "disabled");
				$("#kiadasAr"+i).attr("disabled", "disabled");
				
			}
			
			//hibás sor
			if (($("#kiadasNev"+i).val() == "" && $("#kiadasAr"+i).val() != "") || ($("#kiadasNev"+i).val() != "" && $("#kiadasAr"+i).val() == "")) {
				doit = false;
				err += "\n A kiadások "+(i+1)+". sorát félig töltötted csak ki!";
			}
			
			i++;
		}
		
		//Saját fogyasztás
		if ($("#sajatFogyasztas0").length == 1) {
			
			var s = 0;
			while ($("#sajatFogyasztas"+s).length == 1) {
				if ($("#sajatFogyasztas"+s).val() == "" || isNaN($("#sajatFogyasztas"+s).val())) {
					doit = false;
					err += "\n A saját fogyasztás "+(s+1)+". sorát nem töltötted ki!";
				}
				
				s++;
			}
		
		}
		
		//Ételforgalom
		if ($("[name='etelforgalom0']").length == 1) {
			
			var e = 0;
			while ($("[name='etelforgalom"+e+"']").length == 1) {
				
				if ($("[name='etelforgalom"+e+"']").val() == "" || isNaN($("[name='etelforgalom"+e+"']").val()) ) {
					doit = false;
					err += "\n Az ételforgalom "+(e+1)+". sorát nem töltötted ki!";
				}
				
				e++;
			}
		}
		
		//Egyéb Forgalom
		if ($("[name='etelforgalom0']").length == 1) {
			
			var e = 0;
			while ($("[name='egyebForgalom"+e+"']").length == 1) {
				
				if ($("[name='egyebForgalom"+e+"']").val() == "" || isNaN($("[name='egyebForgalom"+e+"']").val())) {
					doit = false;
					err += "\n Az ételforgalom "+(e+1)+". sorát nem töltötted ki!";
				}
				
				e++;
			}
		}
		
		//kártyaforgalom
		if ($("[name='kartyaForgalom']").length == 1) {
			if ($("[name='kartyaForgalom']").val() == "" || isNaN($("[name='kartyaForgalom']").val())) {
				doit = false;
				err += "\n A kártyaforgalom mezőt nem töltötted ki!";
			}
		}
		
		//LOTTO STAND
		if (lotto == 1) {
			
			//Lotto sorok ELLENŐRZÉSE
			var i = 0;
			var j = 0;
			while ($("#lotNet"+i).length == "1") {
				
				if (($("#lotNet"+i).val() == "" && $("#lotAll"+i).val() != "") || ($("#lotNet"+i).val() != "" && $("#lotAll"+i).val() == "")) {
					
					err += "\nLottó: "+(i+1)+".";
					doit = false;
					
				} else if ($("#lotNet"+i).val() == "" && $("#lotAll"+i).val() == "") {
				
					$("#lotNet"+i).attr("disabled", "disabled");
					$("#lotAll"+i).attr("disabled", "disabled");
					
				} else {
					
					j++;
					
				}
				
				i++;
				
			}
			
			$("#wtime").val(j);
		}
		
		//BORRAVALO
		if ($("[name='borravalo']").val() == "") {
			doit = false;
			err += "\n Borravaló mező nincs kitöltve";
		} else if ($("[name='kp']").val() != "") {
			borravaloSzamolas(0);
		}
		
		//HA TALÁLT HIBÁT
		if (!doit) {
			
			//alert és loader div törlése
			alert("Ellenőrizd az alábbi sorokat:"+err);
			$("#loader").remove();
			
			//mozgatás ha van hiba
			if (elsohiba[0] != -1) {
				$('html, body').animate({scrollTop: elsohiba[1]-30}, 1000);
			}
			
			//ha csak ellenőrzésre fut le az oldal akkor visszadobja hogy volt-e hiba
			if (saveit == 1) {
				return false;
			}
			
		} else {
			
			//ha mentésre fut le a függvény
			if (saveit == 2) {
				makeStat();
				$("#frm").submit();
				//$("#loader").remove();
				
			//ha csak ellenőrzésre fut le visszadobja volt-e hiba
			} else {
				return true;
			}
		}
	} else {
		alert("nincs internetkapcsolat vagy bizonytalan! Így nem lehet elmenteni.");
		$("#loader").remove();
	}
}

//FORM ELSZÁMOLÁS ELKÜLDÉS
function sendElszam(lotto) {
	
	//properties
	var tmpInterval = null;
	autosaveon = false;
	$(".signo").prepend("<div id='loader'><center><br /><font class='anchor3'>LOADING...</font></center></div>");
	
	tmpInterval = setInterval(function() {
		sendElszamCheck(tmpInterval);
	},1000);
	
}
	
//Végső forgalom számítás
function countFinalForgalom() {
	
	var all1 = 0; //italok
	var all2 = 0; //kiadások
	var all3 = 0; //lottó
	var all4 = 0; //akciok
	var all5 = 0; //módosítható sorok
	var all6 = 0; //jutalek
	var all7 = 0; //bevételezések
	var all8 = 0; //saját fogyasztás
	var all9 = 0; //ételforgalom
	var all10 = 0; //bankártya forgalom
	var all11 = 0; //egyéb forgalom
	
	//italok
	for (var i = 0; i < standSor.length; i++) {
		if (!isNaN(standSor[i].ertek) && standSor[i].ertek != -1) {
			all1 += parseInt(standSor[i].ertek);
		}
	}
	
	//kiadások
	if (!isNaN($("[name='KiadasOssz']").val()) && $.trim($("[name='KiadasOssz']").val()) != "") {
		all2 = parseInt($("[name='KiadasOssz']").val());
	}
	
	//lottó
	if ($("#lottoAllI").length == 1) {
	
		if (!isNaN($("#lottoAllI").val()) && $.trim($("#lottoAllI").val()) != "") {
		
			all3 = parseInt($("#lottoAllI").val());
			$("[name='finalLotto']").val(all3);
			
		}
	}
	
	//akciok 
	var curA = 0;
	while ($("#asor"+curA).length == 1) {
		all4 += parseInt($.trim($("#aErtek"+curA).html()));
		
		curA++;
	}
	
	//módosítható sorok
	var g = 0;
	while ($("[name='modSorNev"+g+"']").length) {
		modosithatoKeyEvent(g, false);
		
		all5 += parseInt($.trim($("#modSorErtek"+g).html()));
		g++;
	}
	
	//jutalék számolás ha kell
	if ($("[name='finalJutalek']").length == 1) {
		var tmpjutalek = $("#hiddenJutalek").val();
		all6 = Math.round(((all1) / 100) * tmpjutalek) * -1;
	}
	
	//bevételezések
	if ($("[name='bevetelOssz']").length == 1) {
		countBevetAll(false);
		all7 = parseInt($("[name='bevetelOssz']").val());
	}
	
	//saját fogyasztás
	if ($("[name='sajatFogyOssz']").length == 1) {
		countSajatFogyAll(false);
		all8 = parseInt($("[name='sajatFogyOssz']").val());
	}
	
	//étel fogyasztás
	if ($("[name='etelFogyOssz']").length == 1) {
		countEtelFogyAll(false);
		all9 = parseInt($("[name='etelFogyOssz']").val());
	}
	
	//kártyaforgalom
	if ($("[name='kartyaForgalom']").length == 1 && $("[name='kartyaForgalom']").val() != "") {
		all10 = parseInt($("[name='kartyaForgalom']").val());
	}
	
	//saját fogyasztás
	if ($("[name='egyebForgalomOssz']").length == 1) {
		countEgyebForgalomAll(false);
		all11 = parseInt($("[name='egyebForgalomOssz']").val());
	}
	
	//VÉGSŐ BEÍRÁSOK
	//ha van saját fogyasztás doboz akkor vendég forgalom, saját forgalom
	if ($("[name='sajatFogyasztas0']").length == 1) {
		$("[name='forgalom2save']").val(all1+all4+all5+all9-all8+all11);
		$("[name='finalForg']").val(all1+all4+all5-all8);
		$("[name='finalOsszForg']").val(all1+all4+all5+all9-all8+all11);
		
	} else {
		$("[name='forgalom2save']").val(all1+all4+all5);
		$("[name='finalForg']").val(all1+all4+all5);
		
	}
	
	//ha van étel fogyasztás
	if ($("[name='finalEtelForg']").length == 1) {
		$("[name='finalEtelForg']").val(all9);
	}
	
	//ha van egyéb forgalom
	if ($("[name='finalEgyebForgalom']").length == 1) {
		$("[name='finalEgyebForgalom']").val(all11);
	}
	
	//Új kiadás rendszer
	if ($("[name='finalBevet']").length == 1) {
		$("[name='finalBevet']").val(all7);
		$("[name='finalKiad']").val(all2);
		$("[name='leado']").val(all1-all2+all3+all4+all5-all6+all7-all8+all9-all10+all11);
		
	} else {
		$("[name='finalKiad']").val(all2);
		$("[name='leado']").val(all1-all2+all3+all4+all5-all6+all7-all8+all9);
	}
	
	//van bankártyás leadó
	if ($("[name='kartyaLeado']").length == 1) {
		$("[name='kartyaLeado']").val(all1-all2+all3+all4+all5-all6+all7-all8+all9+all11);
	}
	
	if ($("[name='finalJutalek']").length == 1) {
		$("[name='finalJutalek']").val(all6);
	}
	
	//kézpénz változtatás ha kell
	if ($("[name='kp']").val() != "") {
		borravaloSzamolas(0);
	}
}

/* AKCIOS termékek methodjai */

//aktuális sor számolás
function countAkLine(i) {
	
	//ha nem üres az input számolás ha üres akkor csak nullával kitölti
	if ($("[name='aFogy"+i+"']").val() != "") {
		var tmpFogyas = $("[name='aFogy"+i+"']").val();
		var tmpPrice = parseInt($.trim($("#aPrice"+i).html()));
		
		$("#aErtek"+i).html(Math.round(tmpFogyas*tmpPrice));
		
	} else {
		$("[name='aFogy"+i+"']").val(0);
	}
	
	countFinalForgalom();
}

/* Mósosítható sorok */

//módosítható sorok eventje
function modosithatoKeyEvent(i, ajax) {
	ajax = typeof ajax !== 'undefined' ? ajax : true;
	
	modVesszo(i);
	
	//Összesen számolás ha vételezés ki van töltve
	if (!isNaN($("[name='modSorVetel"+i+"']").val()) && $("[name='modSorVetel"+i+"']").val() != "") {
		$("#modSorOssz"+i).html( Math.round( ( parseFloat($("#secureMnyito"+i).val()) + parseFloat($("[name='modSorVetel"+i+"']").val()) )*100 ) / 100 );
	}
	
	//fogyás számolás ha a vétel és a maradvány is ki van töltve
	if (!isNaN($("[name='modSorVetel"+i+"']").val()) && $("[name='modSorVetel"+i+"']").val() != "" && $("[name='modSorMarad"+i+"']").val() != "" && !isNaN($("[name='modSorMarad"+i+"']").val()) && $("[name='modSorAr"+i+"']").val() != "" && !isNaN($("[name='modSorAr"+i+"']").val())) {
		$("#modSorFogy"+i).html( Math.round( ( parseFloat($("#modSorOssz"+i).html()) - parseFloat($("[name='modSorMarad"+i+"']").val()) )*100 ) / 100 );
		$("#modSorErtek"+i).html( Math.round( parseFloat($("#modSorFogy"+i).html()) * parseFloat($("[name='modSorAr"+i+"']").val()) ) );
	}
	
	if (ajax == true) {
		countFinalForgalom();
		modNevEgyezesEll(i, "");
	}
}

//modvetelnull
function modvetelnull(i) {
	
	//ha vetelezes üres javítsa nullával
	if ($("[name='modSorVetel"+i+"']").val() == "" && $("[name='modSorNev"+i+"']").val() != "") {
		$("[name='modSorVetel"+i+"']").val(0);
	}
	
	modKijDel(i);
}

//vessző átírás a módosítható sorokon
function modVesszo(i) {
	$("[name='modSorVetel"+i+"']").val($("[name='modSorVetel"+i+"']").val().replace(',','.'));
	$("[name='modSorMarad"+i+"']").val($("[name='modSorMarad"+i+"']").val().replace(',','.'));
	$("[name='modSorAr"+i+"']").val($("[name='modSorAr"+i+"']").val().replace(',','.'));
}

//delkijelol
function modKijDel(i) {
	$("#modsorline"+i).attr("class", "sor"+(i%2));
}

//modsor kijelölés feltevése
function modSorKijeloles(i) {
	
	var j = 0;
	while ($("#modsorline"+j).length == 1) {
		if (i != j) {
			$("#modsorline"+j).attr("class", "sor"+(j%2));
		}
		
		if ($("[name='modSorNev"+j+"']").val() != "" && 
		   (parseInt($("#modSorErtek"+j).html()) < 0 || 
		   (isNaN($("[name='modSorMarad"+j+"']").val()) && $("[name='modSorMarad"+j+"']").val() != "") || 
		   (isNaN($("[name='modSorVetel"+j+"']").val()) && $("[name='modSorVetel"+j+"']").val() != "") )) {
			
			$("#modsorline"+j).attr("class", "sorerr");
		}
		j++;
	}
}

//modosithato sorok törlése
function deleteModSor(i){
	
	if ($("[name='modSorID"+i+"']").val() != -1) {
		$.ajax({
			url: "ajax.php?page=20&id="+$("[name='modSorID"+i+"']").val(),
			type: 'GET',
			async: true,
			success: function() {
				
				//törölt sor
				$("[name='modSorDel"+i+"']").val(1);
				
				$("[name='modSorNev"+i+"']").attr("disabled", "disabled");
				$("[name='modSorVetel"+i+"']").attr("disabled", "disabled");
				$("[name='modSorMarad"+i+"']").attr("disabled", "disabled");
				$("[name='modSorAr"+i+"']").attr("disabled", "disabled");
				
				if ($("#standLezar").val() == 0) {
					saveToCookies();
				}
				window.location = window.location;
			},
			error: function() {
				alert("Törlés sikertelen. próbáld meg újra!");
			}
		});
	}
}

//modosithato név egyezés ellenőrzése
function modNevEgyezesEll(i, toldalek) {
	
	//eredeti nev változóba
	var checkMe = $("[name='modSorNev"+i+"']").val();
	
	//az egész akkor fut le ha a név nem üres
	if (checkMe != "") {
	
		//végigmegy az összesen és összehasonlítja
		var j = 0;
		var egyezes = false;
		while ($("[name='modSorNev"+j+"']").length == 1 || $("#secureMnev"+j).length == 1) {
			
			//a forrás sorral nem egyezteti
			if (j != i) {
				
				//összehasonlítás ha szürke dobozba van
				if ($("#secureMnev"+j).length == 1) {
					
					if ($("#secureMnev"+j).val() == checkMe+toldalek) {
						egyezes = true;
					}
				
				//ha sima dobozban van
				} else if ($("[name='modSorNev"+j+"']").length == 1) {
					
					if ($("[name='modSorNev"+j+"']").val() == checkMe+toldalek) {
						egyezes = true;
					}
					
				}
			}
			
			j++;
		}
		
		//ha van egyezes
		if (egyezes) {
			
			//ha a toldalék üres
			if (toldalek == "") {
				toldalek = " 2";
			
			//ha a toldalekban már van valami
			} else {
				toldalek = parseInt(toldalek);
				toldalek++;
				toldalek = " "+toldalek;
			}
			
			//új toldalekkal ellenőrzése
			modNevEgyezesEll(i, toldalek);
		
		//ha nincs egyezes
		} else {
			
			$("[name='modSorNev"+i+"']").val(checkMe+toldalek);
			
		}
	}
	
}

/* Chatbox */

//chatbox elhagyó eventje
function chatboxKeyEvent() {
	
}

/* Borravaló */

//borravalo szamolás
function borravaloSzamolas(input) {
	
	var szam = parseInt($("[name='kp']").val());
	var borravalo = parseInt($("[name='borravalo']").val());
	
	//input 0 = kp tól jött a hívás, input 1 = borravalo
	if (input == 1 && !isNaN(borravalo) && $("[name='borravalo']").val() != "") {
		
		//számolás és ajax
		$("[name='kp']").val(borravalo + parseInt($("[name='leado']").val()));
		
	} else if (input == 0 && !isNaN(szam) && $("[name='kp']").val() != "") {
		
		//számolás és ajax
		$("[name='borravalo']").val(szam - parseInt($("[name='leado']").val())); 
		
	} else if ($("[name='kp']").val() == "" && $("[name='borravalo']").val() == "") {
	
	} else {
		alert("helytelen számot adtál meg adtál meg");
	}
	
}

/* Bevétel */

//összes bevétel számolása
function countBevetAll(finalforg) {
	finalforg = typeof finalforg !== 'undefined' ? finalforg : 0;
	
	var i = 0;
	var all = 0;
	while ($("[name='bevetelAr["+i+"]']").length == 1) {
		if (!isNaN($("[name='bevetelAr["+i+"]']").val()) && $("[name='bevetelAr["+i+"]']").val() != "") {
			all += parseInt($("[name='bevetelAr["+i+"]']").val());
		}
		i++;
	}
	
	$("[name='bevetelOssz']").val(all);
	if (finalforg) {
		countFinalForgalom();
	}
}

//Focus event
function bevetelFocusEvent() {
	
}

//új bevétel sor
function newBevetLine() {
	
	//feltérképezzük hány soros
	var i = 0;
	while ($("#bevetelNev"+i).length == 1) {
		i++;
	}
	
	//kiadás megkeresése a modulokban. hivatkozási pont meghatározás
	var j = 0;
	while (modules[j] != "bevetel") {
		j++;
	}
	
	//inputok
	var inp1 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 0);' onblur='bevetelFocusEvent("+i+");' type='text' name='bevetelNev["+i+"]' id='bevetelNev"+i+"'><input type='hidden' name='bid["+i+"]' value='-1'>";
	var inp2 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 1);' type='text' name='bevetelAr["+i+"]' id='bevetelAr"+i+"' onblur='countBevetAll();bevetelFocusEvent("+i+");'>";
	
	$("#BevetelOsszTr").before("<tr align='center'><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
	
}

/* Kiadasok */

//kiadas blur event
function kiadasFocusEvent(i) {
	kiadNevEgyezesEll(i, "")
}

//Kiadás összesen
function countKiadAll() {
	
	var i = 0;
	var all = 0;
	while ($("#kiadasAr"+i).length == 1) {
		
		if (!isNaN($("#kiadasAr"+i).val()) && $.trim($("#kiadasAr"+i).val()) != "") {
			all += parseFloat($("#kiadasAr"+i).val());
		}
		
		i++;
	}
	
	if ($("#lottoPerc").length == 1 && !isNaN($("#lottoPerc").val()) && $.trim($("#lottoPerc").val()) != "") {
		all += parseFloat($("#lottoPerc").val());
	}
	
	$("[name='KiadasOssz']").val(all);
	countFinalForgalom();
	
}

//modosithato név egyezés ellenőrzése
function kiadNevEgyezesEll(i, toldalek) {
	
	//eredeti nev változóba
	var checkMe = $("#kiadasNev"+i).val();
	
	//az egész akkor fut le ha a név nem üres
	if (checkMe != "") {
	
		//végigmegy az összesen és összehasonlítja
		var j = 0;
		var egyezes = false;
		while ($("#kiadasNev"+j).length == 1) {
			
			//a forrás sorral nem egyezteti
			if (j != i) {
				
				//összehasonlítás ha szürke dobozba van
				if ($("#kiadasNev"+j).length == 1) {
					
					if ($("#kiadasNev"+j).val() == checkMe+toldalek) {
						egyezes = true;
					}
				
				}
			}
			
			j++;
		}
		
		//ha van egyezes
		if (egyezes) {
			
			//ha a toldalék üres
			if (toldalek == "") {
				toldalek = " 2";
			
			//ha a toldalekban már van valami
			} else {
				toldalek = parseInt(toldalek);
				toldalek++;
				toldalek = " "+toldalek;
			}
			
			//új toldalekkal ellenőrzése
			kiadNevEgyezesEll(i, toldalek);
		
		//ha nincs egyezes
		} else {
			
			$("#kiadasNev"+i).val(checkMe+toldalek);
			
		}
	}
	
}

//új kiadás sor
function newKiadLine() {
	
	//utolsó sor megkeresése
	var i = 0;
	while (typeof $("#kiadasNev"+i).val() !== "undefined") {
		i++;
	}
	
	//kiadás megkeresése a modulokban. hivatkozási pont meghatározás
	var j = 0;
	while (modules[j] != "kiadas") {
		j++;
	}
	
	//inputok
	var inp1 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 0);' onblur='kiadasFocusEvent("+i+");' type='text' name='kiadasNev["+i+"]' id='kiadasNev"+i+"' value=''><input type='hidden' name='kid["+i+"]' value='-1'>";
	var inp2 = "<input class='inputText' autocomplete='off' onfocus='setCo("+j+", "+i+", 1);' onblur='countKiadAll();kiadasFocusEvent("+i+");' type='text' name='kiadasAr["+i+"]' id='kiadasAr"+i+"'  value='' />";
	
	//ha nincs lotto kiadás sor akkor a végére amugy a lotto sor elé
	if ($("#lottoPerc").length != 1) {

		$("#KiadasOsszTr").before("<tr align='center'><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
		
	} else {
		
		$("#lottoPercTr").before("<tr align='center'><td class='td1'>"+inp1+"</td><td class='td1'>"+inp2+"</td><td class='td3'></td></tr>");
	}
	
}

/* Saját fogyasztás */

//Összesítés elvégzése
function countSajatFogyAll(finalforg) {
	finalforg = typeof finalforg !== 'undefined' ? finalforg : 0;
	
	var i = 0;
	var all = 0;
	while ($("#sajatFogyasztas"+i).length == 1) {
		if (!isNaN($("#sajatFogyasztas"+i).val()) && $("#sajatFogyasztas"+i).val() != "") {
			all += parseInt($("#sajatFogyasztas"+i).val());
		}
		
		i++;
	}
	
	$("[name='sajatFogyOssz']").val(all);
	if (finalforg) {
		countFinalForgalom();
	}
}

//Doboz elhagyásakor lefutó action
function sajatFogyFocus() { }

/* Étel fogyás */

//összesítés
function countEtelFogyAll(finalforg) {
	finalforg = typeof finalforg !== 'undefined' ? finalforg : 0;
	
	var i = 0;
	var all = 0;
	while ($("[name='etelforgalom"+i+"']").length == 1) {
		if ($("[name='etelforgalom"+i+"']").val() != "" && !isNaN($("[name='etelforgalom"+i+"']").val())) {
			all += parseInt($("[name='etelforgalom"+i+"']").val());
		}
		i++;
	}
	
	$("[name='etelFogyOssz']").val(all);
	if (finalforg) {
		countFinalForgalom();
	}
	
}

/* Egyéb forgalom all */
function countEgyebForgalomAll(finalforg) {
	finalforg = typeof finalforg !== 'undefined' ? finalforg : 0;
	
	var i = 0;
	var all = 0;
	while ($("[name='egyebForgalom"+i+"']").length == 1) {
		if ($("[name='egyebForgalom"+i+"']").val() != "" && !isNaN($("[name='egyebForgalom"+i+"']").val())) {
			all += parseInt($("[name='egyebForgalom"+i+"']").val());
		}
		i++;
	}
	
	$("[name='egyebForgalomOssz']").val(all);
	if (finalforg) {
		countFinalForgalom();
	}
}

/* egyebek */

//cookieba mentés
function saveToCookies() {
	
	//sorhosszok megállapítása és mentése
	var sorhossz = lastSor("akcios")+";"+lastSor("standlap")+";"+lastSor("modsor")+";"+lastSor("lotto")+";"+lastSor("kiadas")+";"+lastSor("chatbox")+";"+lastSor("borravalo");
	var sorhosszok = sorhossz.split(";");
	
	localStorage.setItem("standSorHossz"+$.trim($("#sid").val()), $.trim(sorhossz));
	
	//sorhossz alapján adatok kinyerése
	for (var i = 0; i < sorhosszok.length; i++) {
		
		//menjen végig az adott modul összes során ha az létező modul
		if (sorhosszok[i] != -1) {
			var tmpdatas = "";
			var ptmpdatas = "";
			var pvtmpdatas = "";
			var pointer = 0;
			for (var j = 0; j <= sorhosszok[i]; j++) {
				
				//akcios
				if (i == 0) {
					
					//legyen - e pontos vessző a végén
					if (j < sorhosszok[i]) {
						tmpdatas += $.trim($("[name='aFogy"+j+"']").val())+";";
					} else if (j == sorhosszok[i]) {
						tmpdatas += $.trim($("[name='aFogy"+j+"']").val());
						localStorage.setItem("standSor"+$.trim($("#sid").val())+"a", $.trim(tmpdatas));
					}
					
				//standlap
				} else if (i == 1) {
					
					
					//NINCS PLUSZ RAKTÁR
					if ($("#pluszRaktar").val() == 0) {
						
						//legyen - e pontos vessző a végén
						if (j < sorhosszok[i]) {
							tmpdatas += $.trim($("#vetel"+j).val())+"a"+$.trim($("#maradvany"+j).val())+"a";
						} else if (j == sorhosszok[i]) {
							tmpdatas += $.trim($("#vetel"+j).val())+"a"+$.trim($("#maradvany"+j).val());
							localStorage.setItem("standSor"+$.trim($("#sid").val())+"s", $.trim(tmpdatas));
						}
						
					//VAN PLUSZ RAKTÁR
					} else {
						
						//legyen - e pontos vessző a végén
						if (j < sorhosszok[i]) {
							
							//pult
							tmpdatas += $.trim($("#vetel"+j).val())+"a"+$.trim($("#maradvany"+j+"0").val())+"a";
							
							//többi raktárok
							var tmpraktarak = $("[name='pluszRaktarak']").val();
							tmpraktarak = tmpraktarak.split(";");
							var tmpstr = "";
							for (var r = 0; r < tmpraktarak.length-1; r++) {
								
								//ha utolsó 
								if (r == tmpraktarak.length-2) {
									tmpstr += $("#maradvany"+j+""+tmpraktarak[r]).val()+";";
								} else {
									tmpstr += $("#maradvany"+j+""+tmpraktarak[r]).val()+"a";
								}
								
							}
							ptmpdatas += tmpstr;
							
							//plusz vételezés mentése
							if ($("#pvetel"+j).length == 1) {
								pvtmpdatas += $("#pvetel"+j).val()+";";
							} else {
								pvtmpdatas += ";";
							}
							
						} else if (j == sorhosszok[i]) {
							
							//pult
							tmpdatas += $.trim($("#vetel"+j).val())+"a"+$.trim($("#maradvany"+j+"0").val());
							
							//többi raktárok
							var tmpraktarak = $("[name='pluszRaktarak']").val();
							tmpraktarak = tmpraktarak.split(";");
							var tmpstr = "";
							for (var r = 0; r < tmpraktarak.length-1; r++) {
								
								//ha utolsó 
								if (r == tmpraktarak.length-2) {
									tmpstr += $("#maradvany"+j+""+tmpraktarak[r]).val();
								} else {
									tmpstr += $("#maradvany"+j+""+tmpraktarak[r]).val()+"a";
								}
								
							}
							ptmpdatas += tmpstr;
							
							//plusz vételezés mentése
							if ($("#pvetel"+j).length == 1) {
								pvtmpdatas += $("#pvetel"+j).val();
							} else {
								
							}
							
							//PULT: standSor[sid]s
							localStorage.setItem("standSor"+$.trim($("#sid").val())+"s", $.trim(tmpdatas));
							
							//EXTRA MARADVÁNYOK: standSor[sid]em
							localStorage.setItem("standSor"+$.trim($("#sid").val())+"em", $.trim(ptmpdatas));
							
							//EXTRA VÉTELEZÉS: standSor[sid]ev
							localStorage.setItem("standSor"+$.trim($("#sid").val())+"ev", $.trim(pvtmpdatas));
						}
						
					}
					
					
				//modsor
				} else if (i == 2) {
					
					if (j < sorhosszok[i]) {
						if ($.trim($("[name='modSorDel"+j+"']").val()) == 0) {
							tmpdatas += modSorValues[j][0]+";"+$.trim($("[name='modSorVetel"+j+"']").val())+";"+$.trim($("[name='modSorMarad"+j+"']").val())+";"+$.trim($("[name='modSorAr"+j+"']").val())+";";
						}
					} else if (j == sorhosszok[i]) {
						if ($.trim($("[name='modSorDel"+j+"']").val()) == 0) {
							tmpdatas += modSorValues[j][0]+";"+$.trim($("[name='modSorVetel"+j+"']").val())+";"+$.trim($("[name='modSorMarad"+j+"']").val())+";"+$.trim($("[name='modSorAr"+j+"']").val());
						}
						localStorage.setItem("standSor"+$.trim($("#sid").val())+"m", $.trim(tmpdatas));
					}
				
				} else if (i == 3) {
					
					if (j < sorhosszok[i]) {
						tmpdatas += $.trim($("[name='LotNet["+j+"]']").val())+";"+$.trim($("[name='LotAll["+j+"]']").val())+";";
					} else if (j == sorhosszok[i]) {
						tmpdatas += $.trim($("[name='LotNet["+j+"]']").val())+";"+$.trim($("[name='LotAll["+j+"]']").val());
						localStorage.setItem("standSor"+$.trim($("#sid").val())+"l", $.trim(tmpdatas));
					}
					
				} else if (i == 4) {
					
					if (j < sorhosszok[i]) {
						tmpdatas += $.trim($("[name='kiadasNev["+j+"]']").val())+";"+$.trim($("[name='kiadasAr["+j+"]']").val())+";";
					} else if (j == sorhosszok[i]) {
						tmpdatas += $.trim($("[name='kiadasNev["+j+"]']").val())+";"+$.trim($("[name='kiadasAr["+j+"]']").val());
						localStorage.setItem("standSor"+$.trim($("#sid").val())+"k", $.trim(tmpdatas));
					}
					
				} else if (i == 5) {
					localStorage.setItem("standSor"+$.trim($("#sid").val())+"c", $.trim($("#chatbox").val()));
				} else if (i == 6) {
					localStorage.setItem("standSor"+$.trim($("#sid").val())+"b", $.trim($("[name='kp']").val())+";"+$.trim($("[name='borravalo']").val()));
					break;
				}
				
			}
		}
		
	}
	
	//bevételezések
	var toSt = "";
	if ($("#bevetelNev0").length == 1) {
		var i = 0;
		while ($("#bevetelNev"+i).length == 1) {
			toSt += $("#bevetelNev"+i).val()+"|"+$("#bevetelAr"+i).val()+";";
			i++;
		}
	} else {
		toSt = "nincs";
	}
	localStorage.setItem("standSor"+$.trim($("#sid").val())+"bevet", $.trim(toSt));
	
	//saját fogyasztás
	toSt = "";
	if ($("#sajatFogyasztas0").length == 1) {
		var i = 0;
		while ($("#sajatFogyasztas"+i).length == 1) {
			toSt += $("#sajatFogyasztas"+i).val()+";";
			i++;
		}
	} else {
		toSt = "nincs";
	}
	localStorage.setItem("standSor"+$.trim($("#sid").val())+"sajatfogy", $.trim(toSt));
	
	//ételforgalom
	toSt = "";
	if ($("[name='etelforgalom0']").length == 1) {
		var i = 0;
		while ($("[name='etelforgalom"+i+"']").length == 1) {
			toSt += $("[name='etelforgalom"+i+"']").val()+";";
			i++;
		}
	} else {
		toSt = "nincs";
	}
	localStorage.setItem("standSor"+$.trim($("#sid").val())+"etelforg", $.trim(toSt));
	
	//kártyaforgalom
	toSt = "";
	if ($("[name='kartyaForgalom']").length == 1) {
		toSt += $("[name='kartyaForgalom']").val();
	} else {
		toSt = "nincs";
	}
	localStorage.setItem("standSor"+$.trim($("#sid").val())+"kartyaforg", $.trim(toSt));
	
	//egyéb forgalom
	toSt = "";
	if ($("[name='egyebForgalom0']").length == 1) {
		var i = 0;
		while ($("[name='egyebForgalom"+i+"']").length == 1) {
			toSt += $("[name='egyebForgalom"+i+"']").val()+";";
			i++;
		}
	} else {
		toSt = "nincs";
	}
	localStorage.setItem("standSor"+$.trim($("#sid").val())+"egyebforg", $.trim(toSt));
}

//cookieból elővétel
function getFromCookies() {
	
	//nyers adatok cookiból betöltése
	var sorhossz = localStorage.getItem("standSorHossz"+$.trim($("#sid").val()));
	
	//nyers adatok formázása
	sorhossz = sorhossz.split(";");
	
	//sorhosszok ellenőrzése
	//lotto
	if (sorhossz[3] != lastSor("lotto")) {
		for (var i = lastSor("lotto"); i < sorhossz[3]; i++) {
			addTrToLot();
		}
	}
	//kiadás
	if (sorhossz[4] != lastSor("kiadas")) {
		for (var i = lastSor("kiadas"); i < sorhossz[4]; i++) {
			addRowToStand();
		}
	}
	
	//adatok betöltése inputok a változókból
	var pointer = 0;
	for (var i = 0; i < sorhossz.length; i++) {
		
		if (sorhossz[i] != -1) {
			pointer = 0;
			for (var j = 0; j <= sorhossz[i]; j++) {
				
				if (i == 0) {
					
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"a");
					tmp = tmp.split(";");
					$("[name='aFogy"+j+"']").val(tmp[pointer]);
					pointer++;
					
				} else if (i == 1) {
					
					//GET SIMA STANDSOR
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"s");
					tmp = tmp.split("a");
					
					//HA VANNAK PLUSZ RAKTÁRAK
					if ($("#pluszRaktar").val() == 1) {
						
						//GET RAKTÁRAK
						var tmpraktarak = $("[name='pluszRaktarak']").val();
						tmpraktarak = tmpraktarak.split(";");
						
						//GET EXTRA VÉTELEZÉSEK & MARADVÁNY
						var evtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"ev");
						evtmp = evtmp.split(";");
						var emtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"em");
						emtmp = emtmp.split(";");
						
						//Vétel0-ba és maradvány0ba elhelyezés
						$("#vetel"+j).val(tmp[pointer]);
						pointer++;
						$("#maradvany"+j+"0").val(tmp[pointer]);
						pointer++;
						
						//plusz maradványok
						var finalM = emtmp[j].split("a");
						for (var m = 0; m < tmpraktarak.length-1; m++) {
							$("#maradvany"+j+""+tmpraktarak[m]).val(finalM[m]);
						}
						
						//plusz vétel
						if (evtmp[j] != "") {
							
							//ha van akciós fogyás akkor a typeot módosítja ideiglenesen 1-re mert csak így tud lefutni a checkKeyPressed
							if ($("[name='aFogy0']").length == 1) {
								type = 1;
							}
							checkKeyPressed(j);
							if ($("[name='aFogy0']").length == 1) {
								type = 0;
							}
							$("#pvetel"+j).val(evtmp[j]);
							
						}
						
					//HA NINCSENEK PLUSZ RAKTÁRAK
					} else {
						
						//vételezések és maradványok betöltése
						$("#vetel"+j).val(tmp[pointer]);
						pointer++;
						$("#maradvany"+j).val(tmp[pointer]);
						pointer++;
						
					}
					
					
				} else if (i == 2) {
					
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"m");
					tmp = tmp.split(";");
					
					$("[name='modSorNev"+j+"']").val(tmp[pointer]);
					pointer++;
					$("[name='modSorVetel"+j+"']").val(tmp[pointer]);
					pointer++;
					$("[name='modSorMarad"+j+"']").val(tmp[pointer]);
					pointer++;
					$("[name='modSorAr"+j+"']").val(tmp[pointer]);
					pointer++;
					
				} else if (i == 3) {
					
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"l");
					tmp = tmp.split(";");
					
					$("[name='LotNet["+j+"]']").val(tmp[pointer]);
					pointer++;
					$("[name='LotAll["+j+"]']").val(tmp[pointer]);
					pointer++;
					
				} else if (i == 4) {
					
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"k");
					tmp = tmp.split(";");
					
					$("[name='kiadasNev["+j+"]']").val(tmp[pointer]);
					pointer++;
					$("[name='kiadasAr["+j+"]']").val(tmp[pointer]);
					pointer++;
					
				} else if (i == 5) {
					
					$("#chatbox").val(localStorage.getItem("standSor"+$.trim($("#sid").val())+"c"));
					
				} else if (i == 6) {
					
					var tmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"b");
					tmp = tmp.split(";");
					
					$("[name='kp']").val(tmp[0]);
					$("[name='borravalo']").val(tmp[1]);
					break;
				}
				
			}
		}
		
	}

	//bevételezések
	var newtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"bevet");
	if (newtmp && newtmp != "nincs") {
		newtmp = newtmp.split(";");
		for (var i = 0; i < newtmp.length-1; i++) {
			var tmp2 = newtmp[i].split("|");
			$("#bevetelNev"+i).val(tmp2[0]);
			$("#bevetelAr"+i).val(tmp2[1]);
		}
	}
	
	//saját fogyasztás
	newtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"sajatfogy");
	if (newtmp && newtmp != "nincs") {
		newtmp = newtmp.split(";");
		for (var i = 0; i < newtmp.length-1; i++) {
			$("#sajatFogyasztas"+i).val(newtmp[i]);
		}
	}
	
	//ételforgalom
	newtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"etelforg");
	if (newtmp && newtmp != "nincs") {
		newtmp = newtmp.split(";");
		for (var i = 0; i < newtmp.length-1; i++) {
			$("[name='etelforgalom"+i+"']").val(newtmp[i]);
		}
	}
	
	//kártyaforgalom kartyaforg
	newtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"kartyaforg");
	if (newtmp && newtmp != "nincs") {
		$("[name='kartyaForgalom']").val(newtmp);
	}
	
	//egyéb forgalom
	newtmp = localStorage.getItem("standSor"+$.trim($("#sid").val())+"egyebforg");
	if (newtmp && newtmp != "nincs") {
		newtmp = newtmp.split(";");
		for (var i = 0; i < newtmp.length-1; i++) {
			$("[name='egyebForgalom"+i+"']").val(newtmp[i]);
		}
	}
	
}

//statisztika elküldése emailben
function makeStat() {
	var string = "";
	
	//akcios sorok
	string += "*** Akcios sorok[/n]";
	for (var i = 0; i <= lastSor("akcios"); i++) {
		string += $.trim(escape($("#akciosNeve"+i).html()))+" | "+$("[name='aFogy"+i+"']").val()+"[/n]";
	}
	
	//standlap
	string += "[/n]*** Standlap[/n]";
	for ( var i = 0; i < standSor.length; i++) {
		string += $.trim(escape($("#standlapNeve"+i).html()))+" - "+$("#vetel"+i).val()+" - "+$("#maradvany"+i).val()+"[/n]";
	}
	
	//módosítható sorok
	string += "[/n]*** Módosítható sorok[/n]";
	for ( var i = 0; i <= lastSor("modsor"); i++) {
		string += escape($("[name='modSorNev"+i+"']").val())+" - "+$("[name='modSorVetel"+i+"']").val()+" - "+$("[name='modSorMarad"+i+"']").val()+" - "+$("[name='modSorAr"+i+"']").val()+"[/n]";
	}
	
	//kiadások
	string += "[/n]*** Kiadások[/n]";
	for (var i = 0; i <= lastSor("kiadas"); i++) {
		string += escape($("[name='kiadasNev["+i+"]']").val())+" | "+$("[name='kiadasAr["+i+"]']").val()+"[/n]";
	}
	
	//feltöltés
	$.ajax({
		type: 'GET',
		url: "ajax.php",
		async: false,
		data: "page=28&sid="+$("#sid").val()+"&string="+string,
		success: function(msg) {
			
		},
		error: function() {
			
			
		}
	});
}

/* SORSJEGY */

//Összesítés sorsjeggyel!
function sorsjegyOsszesit() {
	
	//HTML TÁBLÁZAT ÉS FORM
	var html = "<span class='sorsjegyH1'>Összesítés sorsjegyekkel</span><br /><br />";
	
	html += "<table class='sorsjegyTable'>";
	html += "<tr>";
	html += "<td><span class='sorsjegyTH'>Nyeremény</span></td><td><span class='sorsjegyTH'>Fogyás</span></td><td><span class='sorsjegyTH'>Összérték</span></td>";
	html += "</tr>";
	html += "<tr id='sorsjegyloadline'><td colspan='3'><span class='sorsjegyTxt'>Töltés...</span></td></tr>";
	html += "</table><br /><br />";
	
	html += "<table class='sorsjegyTable'>";
	html += "<tr>";
	html += "<td colspan='2'><span class='sorsjegyTH'>Forgalom</span></td>";
	html += "</tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Forgalom</span></td><td><span class='sorsjegyTxt' id='sorsjegyForgalom'>Töltés...</span></td></tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Kiadás</span></td><td><span class='sorsjegyTxt' id='sorsjegyKiadas'>Töltés...</span></td></tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Jutalék</span></td><td><span class='sorsjegyTxt' id='sorsjegyJutalek'>Töltés...</span></td></tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Leadó</span></td><td><span class='sorsjegyTxt' id='sorsjegyLeado'>Töltés...</span></td></tr>";
	html += "</table><br /><br />";
	
	html += "<table class='sorsjegyTable'>";
	html += "<tr>";
	html += "<td colspan='2'><span class='sorsjegyTH'>Borravaló</span></td>";
	html += "</tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Kézpénz</span></td><td><span class='sorsjegyTxt' id='sorsjegyKp'>Töltés...</span></td></tr>";
	html += "<tr><td><span class='sorsjegyTxt'>Borravaló</span></td><td><span class='sorsjegyTxt' id='sorsjegyBorravalo'>Töltés...</span></td></tr>";
	html += "</table><br /><br />";
	
	html += "<button onclick=\"$('#sorsjegyOsszesitoDiv').remove();\">Összesítés bezárása</button> <button onclick='sorsjegyMent();'>Mentés</button>";
	
	//HTML MEGJELENÍTÉSE
	$(".signo").append("<div id='sorsjegyOsszesitoDiv'><center><br />" +html+ "</center></div>");
	
	//AJAX LEKÉRÉS
	$.ajax({
		type: 'GET',
		url: "ajax.php",
		async: true,
		data: "page=31&pid="+$("#pid").val()+"&sid="+$("#sid").val(),
		success: function(msg) {
			sorsjegy2HTML(msg);
		},
		error: function() {
			
			
		}
	});
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
		j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

//Sorsjegy összesítés beillesztése a formba
var nyeremenyek = new Array();
var tmpjutalek = 0;
function sorsjegy2HTML(msg) {
	
	//szükséges változók
	tmpjutalek = $("#hiddenJutalek").val();
	
	//Nyeremények szétbontása
	nyeremenyek = msg.split(";");
	
	//nyeremény adatok szétbontása
	for (var i = 0; i < nyeremenyek.length; i++) {
		nyeremenyek[i] = nyeremenyek[i].split(",");
	}
	
	//betöltése htmlbe
	$("#sorsjegyloadline").remove();
	for (var i = 0; i < nyeremenyek.length; i++) {
		if (nyeremenyek[i][3] != 0 && typeof nyeremenyek[i][3] !== "undefined") {
			$(".sorsjegyTable:first").append("<tr><td><span class='sorsjegyTxt'>"+nyeremenyek[i][1]+"</span></td><td><span class='sorsjegyTxt'>"+nyeremenyek[i][2]+"</span></td><td><span class='sorsjegyTxt'>"+Math.round(nyeremenyek[i][3]).formatMoney(0, "", " ")+" Ft</span></td></tr>");
		}
	}
	
	//italforgalom számolása
	var newItalforg = 0;
	for (var i = 0; i < standSor.length; i++) {
		if (!isNaN(standSor[i].ertek) && standSor[i].ertek != -1) {
			newItalforg += parseInt(standSor[i].ertek);
		}
	}
	
	//új forgalom számolása
	var oldForg = $("[name='finalForg']").val();
	var osszNyeremeny = 0;
	for (var i = 0; i < nyeremenyek.length; i++) {
		if (!isNaN(nyeremenyek[i][3])) {
			osszNyeremeny += Math.round(nyeremenyek[i][3]);
		}
	}
	var newForgalom = oldForg - osszNyeremeny + Math.round((osszNyeremeny / 100) * tmpjutalek);
	$("#sorsjegyForgalom").html((newForgalom).formatMoney(0, "", " ")+" FT");
	
	//kiadás átmásolása
	var tmpkiadas = parseInt($("[name='finalKiad']").val());
	$("#sorsjegyKiadas").html(tmpkiadas.formatMoney(0, "", " ")+" FT");
	
	//lottó változóba ha van
	var tmplotto = 0;
	if ($("[name='finalLotto']").length != 0) {
		tmplotto = parseInt($("[name='finalLotto']").val());
	}
	
	//jutalék kiszámolása
	var tmpjutalek2 = Math.round((newItalforg / 100) * tmpjutalek );
	$("#sorsjegyJutalek").html(tmpjutalek2+" FT");
	
	//leadó számolása
	var newLeado = parseInt(newForgalom + tmpkiadas + tmplotto - tmpjutalek2);
	$("#sorsjegyLeado").html(newLeado.formatMoney(0, "", " ")+" FT");
	
	//új borravaló kiszámolása
	var newKp = $("[name='kp']").val();
	if (isNaN(newKp) || newKp == "") {
		newKp = 0;
	}
	var newBorravalo = newKp - newLeado;
	$("#sorsjegyKp").html(newKp);
	$("#sorsjegyBorravalo").html(newBorravalo);
}

//Mentés sorsjegy összesítéssel
function sorsjegyMent() {
	
	//ellenőrzi van-e hiba az oldalon, ha nincs true
	var mentes = sendElszamCheck("", 1);
	
	//ha hibás akk
	if (!mentes) {
		$('#sorsjegyOsszesitoDiv').remove();
		
	//ha minden adat jó
	} else {
		
		//első üres kiadás doboz megkeresése
		var i = 0;
		while ($("#kiadasNev"+i).length == 1 && $("#kiadasNev"+i).val() != "") {
			
			//Ha a következő sor nem létezik hozzáadja
			if ($("#kiadasNev"+(i+1)).length == 0) {
				addRowToStand();
			}
			i++;
		}
		
		//nyeremények betöltése kiadásba
		var osszesites = 0;
		var szamlalo = 0;
		for (var j = 0; j < nyeremenyek.length; j++) {
			
			//ha használható adat van benne
			if (typeof nyeremenyek[j][3] !== "undefined" && nyeremenyek[j][3] != 0) {

				//ha nincs sor hozzáad
				if ($("#kiadasNev"+(i+szamlalo)).length == 0) {
					addRowToStand();
				}
				
				//megnézi az adott sorban van-e szöveg. ha van megkeresi a következő üreset
				if ($("#kiadasNev"+(i+szamlalo)).val() != "") {
					while ($("#kiadasNev"+(i+szamlalo)).val() != "") {
						szamlalo++;
						
						//ha nincs sor hozzáad
						if ($("#kiadasNev"+(i+szamlalo)).length == 0) {
							addRowToStand();
						}
					}
				}
				
				//beilleszt
				$("#kiadasNev"+(i+szamlalo)).val("Nyeremény "+nyeremenyek[j][1]);
				$("#kiadasAr"+(i+szamlalo)).val("-"+nyeremenyek[j][3]);
				osszesites += parseInt(nyeremenyek[j][3]*-1);
				
				szamlalo++;
				
			}
			
		}
		
		//jutaleknak ha nincs elég sor hozzáad még
		if ($("#kiadasNev"+(i+szamlalo)).length == 0) {
			addRowToStand();
		}
		
		//megnézi az adott sorban van-e szöveg. ha van megkeresi a következő üreset
		if ($("#kiadasNev"+(i+szamlalo)).val() != "") {
			while ($("#kiadasNev"+(i+szamlalo)).val() != "") {
				szamlalo++;
				
				//ha nincs sor hozzáad
				if ($("#kiadasNev"+(i+szamlalo)).length == 0) {
					addRowToStand();
				}
			}
		}
		
		//jutalék beillesztése
		$("#kiadasNev"+(i+szamlalo)).val("Sorsjegy jutalék");
		$("#kiadasAr"+(i+szamlalo)).val(Math.round((osszesites/100)*tmpjutalek) * -1);
		$('#sorsjegyOsszesitoDiv').remove();
		
		//ellenőrzi van-e lottó és annak megfelelően elküldi a formot!
		sendElszam($("[name='finalLotto']").length);
	}
}

function standKorToHtml(i, ar) {
	
	//különbség
	var kulonbseg = (parseInt($("#vetel"+i).val())*ar)*-1;
	
	//előjel
	var tmpelojel = "+";
	if (kulonbseg < 0) {
		tmpelojel = "";
	}
	$("#korabbiLeado").html(tmpelojel+""+kulonbseg);
}

//nyitó korrigálása és előző stand korrigálása
function standKorrigalas(i) {
	if ($("#vetel"+i).val() != 0) {
		
		//get pluszraktarak
		var raktarok = $("[name='pluszRaktarak']").val().split(";");
		
		//HTML TÁBLÁZAT ÉS FORM
		var html = "<span class='sorsjegyH1'>Nyitó és korábbi maradvány korrigálása</span><br /><br />";
		
		html += "<table class='sorsjegyTable'>";
		html += "<tr>";
		html += "<td><span class='sorsjegyTH'>Leadó változás az elözö standlapon</span></td>";
		html += "</tr>";
		html += "<tr id='sorsjegyloadline'><td><span class='sorsjegyTxt' id='korabbiLeado'>Számol...</span></td></tr>";
		html += "</table><br /><br />";
		
		html += "<table class='sorsjegyTable'>";
		html += "<tr>";
		html += "<td colspan='2'><span class='sorsjegyTH'>Válaszd ki melyik raktárból vonja le!</span></td>";
		html += "</tr>";
		html += "<tr><td><span class='sorsjegyTxt'>Pult:</span></td><td><input type='radio' name='choosedStorage' value='0' checked='checked' /></td></tr>";
		for (var j = 0; j < raktarok.length-1; j++) {
			html += "<tr><td><span class='sorsjegyTxt'>"+$.trim($("#nyitonev0"+raktarok[j]).html())+"</span></td><td><input type='radio' name='choosedStorage' value='"+raktarok[j]+"' /></td></tr>";
		}
		html += "</table><br /><br />";
		
		html += "<span class='sorsjegyTH'>Miért korrigálsz?</span><br />";
		html += "<textarea id='korrigalMsg' name='korrigalMsg' style='width:300px;height:100px;'></textarea><br /><br />";
		
		html += "<button onclick=\"$('#sorsjegyOsszesitoDiv').remove();\">Bezárás</button> <button onclick='standKorrigal("+i+");'>Korrigálás</button>";
		
		//HTML MEGJELENÍTÉSE
		$(".signo").append("<div id='sorsjegyOsszesitoDiv'><center><br />" +html+ "</center></div>");
		
		//AJAX LEKÉRÉS
		$.ajax({
			type: 'GET',
			url: "ajax.php",
			async: true,
			data: "page=35&pid="+$("#pid").val()+"&sid="+$("#sid").val()+"&did="+standSor[i].DID,
			success: function(msg) {
				standKorToHtml(i, msg);
			},
			error: function() {
				alert("valami hiba történt!");
				
			}
		});
		
	}
}

//korrigálás végrehajtása
function standKorrigal(i) {
	var choosedStorage = $("[name='choosedStorage']:checked").val();
	var message = $("#korrigalMsg").val();
	var valtozas = $.trim($("#korabbiLeado").html());
	
	//AJAX LEKÉRÉS
	$.ajax({
		type: 'GET',
		url: "ajax.php",
		async: true,
		data: "page=36&pid="+$("#pid").val()+"&sid="+$("#sid").val()+"&choosedStorage="+choosedStorage+"&sorid="+standSor[i].ID+"&sordid="+standSor[i].DID+"&message="+$("#korrigalMsg").val()+"&valtozas="+valtozas,
		success: function(msg) {
			//alert(msg);
			//location.reload();
			console.log(msg);
			
		},
		error: function() {
			alert("valami hiba történt!");
			
		}
	});
}

/* Nyito másolása maradványhoz több raktár esetén */
function nyitoToMaradvanyPR() {
	var i = 0;
	var raktarok = $("[name='pluszRaktarak']").val();
	raktarok = raktarok.split(";");
	while ($("#maradvany"+i+"0").length == 1) {
		$("#maradvany"+i+"0").val($("#newNyito"+i+"0").val());
		for (var j = 0; j < raktarok.length-1; j++) {
			$("#maradvany"+i+""+raktarok[j]).val($("#newNyito"+i+""+raktarok[j]).val());
		}
		i++;
	}
}