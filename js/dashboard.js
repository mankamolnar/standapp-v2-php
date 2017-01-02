function numberFormat(number, decimals, dec_point, thousands_sep) {
    // http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_number_format/
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec)
        {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3)
    {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec)
    {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

//dashboardclick
function dashboardClick() {
	var pid = $("#dashboardPID").val();
	var year = $("#dashboardDate").val();
	var month = $("#dashboardMonth").val();
	
	$.ajax({
		url: "ajax.php?page=19&pid="+pid+"&year="+year+"&month="+month,
		type: 'GET',
		async: true,
		success: function(msg) {
			var valtozok = msg.split(";");
			
			$("#standszamspan").html(valtozok[0]);
			$("#haviforgalomspan").html(numberFormat(parseInt(valtozok[1]), 0, ".", " "));
			$("#havileadospan").html(numberFormat(parseInt(valtozok[3]), 0, ".", " "));
			$("#havikiadasspan").html(numberFormat(parseInt(valtozok[2]), 0, ".", " "));
			$("#havifizetesspan").html(numberFormat(parseInt(valtozok[4]), 0, ".", " "));
			//$("#dashboardTitle").html(year+" "+month+". hó");
			
		},
		error: function() {
			alert("valami hiba történt, próbáld meg újra!");
		}
	});
}

//két évet összehasonlító statisztika beállításai
function statOneSettings() {
	
	//select input generálás
	var select1 = "<select id='statOneLastYear'>";
	var select2 = "<select id='statOneThisYear'>";
	for (var d = new Date().getFullYear()-1; d >= 2013; d--) {
		select1 += "<option value='"+d+"'>"+d+"</option>";
	}
	for (var d = new Date().getFullYear(); d >= 2013; d--) {
		select2 += "<option value='"+d+"'>"+d+"</option>";
	}
	select1 += "</select>";
	select2 += "</select>";
	
	//beillesztendő html
	var html = "<div id='statOneContainer'>";
	html += "<div class='statBox' id='LastYearBox'></div> "+select1+"<br /><br />";
	html += "<div class='statBox' id='ThisYearBox'></div> "+select2+"<br /><br />";	
	html += "<button onclick='statOneCreate();'>Statisztika elkészítése</button>";
	html += "</div>";
	
	//chart eltüntetése
	$("#chart-left").fadeOut("fast", function() {
		$(".leftCanvasC:first").append(html);
		$("#statOneContainer").fadeIn();
	});
}

//Napi pultos átlag forgalmom
function statTwoSettings() {
	
	//beillesztendő html
	var html = "<div id='statTwoContainer'>";
	html += "<input type='text' id='statTol' />-tól <input type='text' id='statIg' />-ig<br /><br />";
	html += "<button onclick='statTwoCreate();'>Statisztika elkészítése</button>";
	html += "</div>";
	
	//chart eltüntetése
	$("#chart-right").fadeOut("fast", function() {
		$(".rightCanvasC:first").append(html);
		$("#statTwoContainer").fadeIn();
		
		$("#statTol").datepicker();
		$("#statIg").datepicker();
	});
}

//két évet összehasonlító statisztika elkészítése
function statOneCreate() {
	
	//Alap properties
	var lastyearlabel = $("#statOneLastYear").val();
	var thisyearlabel = $("#statOneThisYear").val();
	var tmpurl = "ajax.php?page=33&pid="+$("#dashboardPID").val()+"&lyear="+$("#statOneLastYear").val()+"&tyear="+$("#statOneThisYear").val();
	
	//Form eltüntetése és töltés gomb beállítása
	$("#statOneContainer").fadeOut("fast", function() {
		$("#statOneContainer").remove();
		$(".leftCanvasC:first").append("<img src='img/loading-green-2.gif' class='statLoader' />");
		
		//ajax lekérés az adatokkal
		$.ajax({
			url: tmpurl,
			type: 'GET',
			async: true,
			success: function(msg) {
				
				//berakja az éveket a h1-be (2015 vs 2016)
				$("#tavalylabel1").html(lastyearlabel);
				$("#ideilabel1").html(thisyearlabel);
				
				//Előző chart eltüntetése végleg, loader gif eltüntetése, új canvas az új statinak
				$("#chart-left").remove();
				$(".statLoader").remove();
				$(".leftCanvasC:first").append('<canvas id="chart-left" width="400" height="300" />');
				
				//Adatok feldolgozása
				var dates = $("#dates").val().split(";");
				var tmp = msg.split("|");
				tmp[0] = tmp[0].split(";");
				tmp[1] = tmp[1].split(";");
				dates.splice(dates.length-1, 1);
				tmp[0].splice(tmp[0].length-1, 1);
				tmp[1].splice(tmp[1].length-1, 1);
				
				//JSON chartnak
				var lineChartData = {
					labels : dates,
					datasets : [
						{
							label: "Tavaly",
							fillColor : "rgba(93,165,218,0.2)",
							strokeColor : "rgba(151,187,205,1)",
							pointColor : "rgba(151,187,205,1)",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "rgba(151,187,205,1)",
							data : tmp[0]
						},
						{
							label: "Idén",
							fillColor : "rgba(250,164,58,0.2)",
							strokeColor : "rgba(250,164,58,1)",
							pointColor : "rgba(250,164,58,1)",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "rgba(250,164,58,1)",
							data : tmp[1]
						}
					]

				}
				
				//canvas megrajzolása
				var tmpctx = document.getElementById("chart-left").getContext("2d");
				window.myLine = new Chart(tmpctx).Line(lineChartData, {
					responsive: true
				});
				
			},
			error: function() {
				alert("valami hiba történt, próbáld meg újra!");
			}
		});
	});
}

//Pultosok átlagforgalma
function statTwoCreate() {
	
	//Alap properties
	var tol = $("#statTol").val();
	var ig = $("#statIg").val();
	var tmpurl = "ajax.php?page=34&pid="+$("#dashboardPID").val()+"&tol="+tol+"&ig="+ig;
	
	if (tol.match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/) && tol.match(/[0-9]{4}-[0-9]{2}-[0-9]{2}/)) {
		
		//Form eltüntetése és töltés gomb beállítása
		$("#statTwoContainer").fadeOut("fast", function() {
			$("#statTwoContainer").remove();
			$(".rightCanvasC:first").append("<img src='img/loading-green-2.gif' class='statLoader' />");
			
			//ajax lekérés az adatokkal
			$.ajax({
				url: tmpurl,
				type: 'GET',
				async: true,
				success: function(msg) {
					
					//Előző chart eltüntetése végleg, loader gif eltüntetése, új canvas az új statinak
					$("#chart-right").remove();
					$(".statLoader").remove();
					$(".rightCanvasC:first").append('<canvas id="chart-right" width="400" height="300" />');
					
					//Adatok feldolgozása
					var tmpdata = msg.split("|");
					tmpdata.splice(tmpdata.length-1, 1);
					
					//properties
					var tmpusers = new Array();
					
					//adatok begyűjtése
					for (var k = 0; k < tmpdata.length; k++) {
						tmpdata[k] = tmpdata[k].split(";");
						tmpusers[k] = {value:tmpdata[k][1],label:tmpdata[k][0]};
					}
					
					//canvas megrajzolása
					var tmpctx = document.getElementById("chart-right").getContext("2d");
					window.myDoughnut = new Chart(tmpctx).Doughnut(tmpusers, {responsive : true});

				},
				error: function() {
					alert("valami hiba történt, próbáld meg újra!");
				}
			});
		});
		
	} else {
		alert("Nem lett megadva kezdő és vég dátum!")
	}
}