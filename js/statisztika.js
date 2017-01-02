//DATEPICKEREK ELHELYEZÉSE
function activateDatepicker() {
	
	//ha van kezdődátum
	if ($("[name='startDate']").length == 1) {
		$("[name='startDate']").datepicker({
			onClose: function(selectedDate) {
				checkIdoszak();
			}
		});
	}
	
	//ha van végdátum
	if ($("[name='endDate']").length == 1) {
		$("[name='endDate']").datepicker({
			onClose: function(selectedDate) {
				checkIdoszak();
			}
		});
	}
}

//IDŐSZAK ELLENŐRZÉSE (MIND2 DATEPICKER KI LETT E TÖLTVE)
function checkIdoszak() {
	if ($("[name='startDate']").val().match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/) != null && $("[name='endDate']").val().match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/) != null) {
		$.ajax({
			url: "ajax.php?page=32&pid="+$("#pid").val()+"&sd="+$("[name='startDate']").val()+"&ed="+$("[name='endDate']").val(),
			type: 'GET',
			async: true,
			success: function(msg) {
				
				$("#formTags").append("<p id='step2'></p>");
				$("#step2").hide();
				var html = "<span class='anchor2'><u>2. LÉPÉS:</u> Termék kiválasztása</span><br />";
				html += "<select name='termek'>";
				
				var tmp1 = msg.split(";");
				var tmp2 = new Array();
				for (var i = 0; i < tmp1.length-1; i++) {
					
					tmp2 = tmp1[i].split("-");
					html += "<option value="+tmp2[0]+">"+tmp2[1]+"</option>";
					
				}
				html += "</select><br /><br />";
				html += "<input type='submit' value='Lekérés!' />";
				
				$("#step2").html(html);
				$("#step2").fadeIn();
				
			}
		});
	}
}

//FORM INPUTJAINAK A BETÖLTÉSE
function chooseStatForm(stat) {
	
	//ADOTT STATISZTIKA KIVÁLASZTÁSAKOR TEENDŐK (pl.: HTML FORM)
	var html = "";
	if (stat == "kocsmaOsszehasonlitas") {
		
		html += "<b>Kezdő dátum:</b> <input type='text' name='startDate' /><br /><br />";
		html += "<b>Hány hónapon keresztül?</b> <input type='text' name='dateLength' /><br />";
		html += "<input type='submit' value='Statisztika elkészítése!' />";
		
	} else if (stat == "italforgalomAdottHonapban") {
		
		html += "<b>Hónap kiválasztása:</b> <input type='text' name='startDate' /><br /><br />";
		html += "<input type='submit' value='Statisztika elkészítése!' />";
		
	} else if (stat == "pultosForgalomAdottHonapban") {
		
		html += "<b>Hónap kiválasztása:</b> <input type='text' name='startDate' /><br /><br />";
		html += "<input type='submit' value='Statisztika elkészítése!' />";
		
	} else if (stat == "elmult1evforgalom") {
		
		$("#statForm").submit();
		
	} else if (stat == "bevetelezesLekeres") {
		
		html += "<span class='anchor2'><u>1. LÉPÉS:</u> Idöszak kiválasztása</span><br /><br />";
		html += "<input type='text' name='startDate' onblur='checkIdoszak();' />-tól <input type='text' name='endDate' onblur='checkIdoszak();' />-ig<br /><br />"
		
	} else if (stat == "korrigalasLekeres") {
		
		var evselect = "<select name='ev'>";
		var startDate = new Date();
		startDate = parseInt(startDate.getFullYear());
		for (var i = startDate; i >= 2016; i--) {
			evselect += "<option value='"+i+"'>"+i+"</option>";
		}
		evselect += "</select>";
		
		var monthselect = "<select name='honap'><option value='01'>01</option><option value='02'>02</option><option value='03'>03</option><option value='04'>04</option><option value='05'>05</option><option value='06'>06</option><option value='07'>07</option><option value='08'>08</option><option value='09'>09</option><option value='10'>10</option><option value='11'>11</option><option value='12'>12</option></select>";
		html += "<span class='anchor2'>Válaszd ki az évet és a hónapot!</span><br /><br />";
		html += evselect+" "+monthselect+"<br /><br />";
		html += "<input type='submit' value='Lekérés!' />";
	}
	
	//HTML TAGEK
	if (html != "") {
		
		//tagek beillesztése
		$("#formTags").fadeOut("fast", function() {
			$("#formTags").html(html);
			$("#formTags").fadeIn("fast", function() {
				activateDatepicker();
			});
		});
		
	}
	
}