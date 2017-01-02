//Oldalspecifikus függvények; INDEXEN NE FUSSON
if (document.URL.indexOf("?") != -1) {
	
	//newfocus
	function newFocus(ttype, ssor, ooszlop, ppline) {
		
		ppline = typeof ppline !== 'undefined' ? ppline : 0;
		
		//modul ellenőrzásek
		//akcios modul
		if (ttype == "akcios") {
			$("[name='aFogy"+ssor+"']").focus();
		}
		
		//standlap
		if (ttype == "standlap") {
			
			//HA NINCS TÖBB RAKTÁR
			if ($("#pluszRaktar").val() == 0) {
				
				if (ooszlop == 0) {
					$("#vetel"+ssor).focus();
				} else if (ooszlop == 1) {
					$("#maradvany"+ssor).focus();
				}
				
			//HA TÖBB RAKTÁR VAN
			} else {
				
				//VÉTELEZÉS
				if (ooszlop == 0) {
					
					//ELSŐ SOR
					if (ppline == 0) {
						
						$("#vetel"+ssor).focus();
					
					//PLUSZ SOR
					} else {
						$("#pvetel"+ssor).focus();
					}
				
				//MARADVÁNY
				} else if (ooszlop == 1) {
					
					$("#maradvany"+ssor+""+ppline).focus();
					
				}
				
			}
			
		}
		
		//módosítható sorok
		if (ttype == "modsor") {
			if (ooszlop == 0) {
				
				//ha kikapcsolt, ha nem
				if ($("#secureMnev"+ssor).length == 1) {
					
					oszlop = 1;
					$("[name='modSorVetel"+ssor+"']").focus();
					
				} else {
				
					$("[name='modSorNev"+ssor+"']").focus();
					
				}
			} else if (ooszlop == 1) {
				$("[name='modSorVetel"+ssor+"']").focus();
			} else if (ooszlop == 2) {
				$("[name='modSorMarad"+ssor+"']").focus();
			} else if (ooszlop == 3) {
				$("[name='modSorAr"+ssor+"']").focus();
			}
		}
		
		//lotto
		if (ttype == "lotto") {
			if (ooszlop == 0) {
				$("#lotNet"+ssor).focus();
			} else if (ooszlop == 1) {
				$("#lotAll"+ssor).focus();
			}
		}
		
		//bevételezés
		if (ttype == "bevetel") {
			if (ooszlop == 0) {
				$("#bevetelNev"+ssor).focus();
			} else if (ooszlop == 1) {
				$("#bevetelAr"+ssor).focus();
			}
		}
		
		//kiadasok
		if (ttype == "kiadas") {
			if (ooszlop == 0) {
				$("#kiadasNev"+ssor).focus();
			} else if (ooszlop == 1) {
				$("#kiadasAr"+ssor).focus();
			}
		}
		
		//saját fogyasztás
		if (ttype == "sajatfogy") {
			$("#sajatFogyasztas"+ssor).focus();
		}
		
		//egyéb forgalom
		if (ttype == "egyebforgalom") {
			$("#egyebForgalom"+ssor).focus();
		}
		
		//chatbox
		if (ttype == "chatbox") {
			$("#chatbox").focus();
		}
		
		//étel fogyás
		if (ttype == "etelfogyasztas") {
			$("[name='etelforgalom"+ssor+"']").focus();
		}
		
		//kártyaforgalom
		if (ttype == "kartyaforgalom") {
			$("[name='kartyaForgalom']").focus();
		}
		
		//borravalo
		if (ttype == "borravalo") {
			if (ssor == 0) {
				$("[name='kp']").focus();
			} else if (ssor == 1) {
				$("[name='borravalo']").focus();
			}
		}
		
	}
	
	//jelenlegi előtti modul utolsó sorának megtalálása
	function lastSor(ttype) {
		
		//type megállapítás
		if (ttype == "akcios") {
			
			//sorok számának megállapítása
			var m = 0;
			while ($("[name='aFogy"+m+"']").length == 1) {
				m++;
			}
			
			return m-1;
		
		//standlap
		} else if (ttype == "standlap") {
			
			return standSor.length-1;
			
		//módosítható sorok
		} else if (ttype == "modsor") {
			//sorok számának megállapítása
			var m = 0;
			while ($("[name='modSorNev"+m+"']").length == 1) {
				m++;
			}
			
			return m-1;
		
		//Lotto
		} else if (ttype == "lotto") {
			//sorok számának megállapítása
			var m = 0;
			while ($("#lotNet"+m).length == 1) {
				m++;
			}
			
			return m-1;
		
		//Bevétel
		} else if (ttype == "bevetel") {
			//sorok számának megállapítása
			var m = 0;
			while ($("#bevetelNev"+m).length == 1) {
				m++;
			}
			return m-1;
			
		//Kiadás
		} else if (ttype == "kiadas") {
			//sorok számának megállapítása
			var m = 0;
			while ($("#kiadasNev"+m).length == 1) {
				m++;
			}
			return m-1;
			
		//sajátfogyasztás
		} else if (ttype == "sajatfogy") {
			
			//megszámolja a sorokat
			var i = 0;
			while ($("[name='sajatFogyasztas"+i+"']").length == 1) {
				i++;
			}
			return --i;
			
		//egyéb forgalom
		} else if (ttype == "egyebforgalom") {
			
			//megszámolja a sorokat
			var i = 0;
			while ($("[name='egyebForgalom"+i+"']").length == 1) {
				i++;
			}
			return --i;
			
		//chatbox
		} else if (ttype == "chatbox") {
			if ($("#chatbox").length == 1) {
				return 0;
			} else {
				return -1;
			}
		
		//ételforgalom
		} else if (ttype=="etelfogyasztas") {
			
			//megszámolja a sorokat
			var i = 0;
			while ($("[name='etelforgalom"+i+"']").length == 1) {
				i++;
			}
			return --i;
			
		//kartyaforgalom
		} else if (ttype == "kartyaforgalom") {
			return 0;
			
		//borravalo
		} else if (ttype == "borravalo") {
			return 1;
		}
	}
	
	//Kocsma beállítások
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "28") {
		
		
		$(document).ready(function() {
			
			//switch buttonok bemozgatása a kívánt pozícióba attól függően hogy ki vagy be van kapcsolva
			if ($("#isLotto").val() == 0) {
				switchSetting("lottoSw", false);
			}
			if ($("#modosithatoStand").val() == 0) {
				switchSetting("modSorSw", false);
			}
			if ($("#chatbox").val() == 0) {
				switchSetting("chatboxSw", false);
			}
			if ($("#nyitoToMaradvany").val() == 0) {
				switchSetting("NyitoToMaradvanySw", false);
			}
			if ($("#sorsjegy").val() == 0) {
				switchSetting("sorsjegySw", false);
			}
			if ($("#ujkiadas").val() == 0) {
				switchSetting("ujkiadasSw", false);
			}
			if ($("#sajatfogyasztas").val() == 0) {
				switchSetting("sajatfogyasztasSw", false);
			}
			if ($("#standellenorzes").val() == 0) {
				switchSetting("standellenorzesSw", false);
			}
			if ($("#etelfogyasztas").val() == 0) {
				switchSetting("etelfogyasztasSw", false);
			}
			if ($("#pluszraktar").val() == 0) {
				switchSetting("pluszraktarSw", false);
			}
			if ($("#bankkartya").val() == 0) {
				switchSetting("bankkartyaSw", false);
			}
			if ($("#egyebforgalom").val() == 0) {
				switchSetting("egyebforgalomSw", false);
			}
			if ($("#jutalek").val() == 0) {
				switchSetting("jutalekSw", false);
			}
			if ($("#likemywifi").val() == 0) {
				switchSetting("likemywifiSw", false);
			}
			
			//switch buttonok kezelése
			$(".switchBt").click(function() {
				
				switchSetting(this.id, true);
				
			});
			
		});
		
	}
	
	//itallap kezeléskor move buttonok
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "6") {
		
		$(document).ready(function() {
			
			//ebbe a tombbe példányosít
			window.italok = new Array();
			
			//példányosítás
			var i = 0;
			while (typeof $("#italNev"+i).val() !== "undefined") {
				
				//példányosítás
				window.italok[i] = new itallap($("#ID"+i).val(), $("#csoport"+i).val(), $("#mertekegyseg"+i).val(), $("#listID"+i).val(), $("#italNev"+i).val(), $("#italAr"+i).val(), $("[name='forditott["+i+"]']").val(), $("#visibleHidden"+i).val());
				
				i++;
			}
			
			filtering();
			
			//gombnyomás beállítása
			$(".moveimg").click(function(e) {
				window.italok[this.id].moveDiv(e.pageX, e.pageY);
			});
			
			//visible gomb okosítás
			$(".visibleImg").click(function(e) {
				chgVis(this.id);
			});
			
			//Add button
			$("#itallapMenuAdd").click(function() {
				
				$("#itallapMenuDown").animate({top:'48px'}, 500);
				
			});
			
			//close Button
			$("#closeNew").click(function() {
				$("#itallapMenuDown").animate({top:'0px'}, 500);
			});
			
			//szűrő Button
			$("#itallapMenuFilter").click(function() {
				
				//checks that is it already visible or not 
				if ($("#filterDiv").css("visibility") == "hidden") {
					
					addFilterDiv();
				
				} else {
					
					removeFilterDiv();
					
				}
			});
			
			//switchedBt
			$(".switchBt").click(function() {
			
				switchChg(this.id);
			});
			
			//új itallap feltöltő
			$("#newFelBut").click(function() {
				ujItalFeltolto();
			});
			
			//kereső gombfigyelés
			$("#itallapSearch").keyup(function() {
				filtering();
			});
			
		});
		
	}
	
	//Felhasználó kezeléskor datepickerek & jelszavak változóba
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "9") {
		
		var passes = new Array();
		$(document).ready(function() {
			
			//datepicker
			$("[name='szulnap']").datepicker({
				changeMonth:true,
				changeYear:true,
				yearRange: "-100:+0"
			});
			
			//if one of the names is clicked
			$(".kezeloA").click(function(event) {
				
				//get the id
				var tmpID = event.target.id.split("i")[1];
				
				//set inputs and title
				if (tmpID != "new") {
					$("#felhForm").attr("action", "index.php?page=10");
					
					$("[name='id']").val($("[name='id["+tmpID+"]']").val());
					$("#felhTitle").html($("[name='uname["+tmpID+"]']").val());
					$("[name='uname']").val($("[name='uname["+tmpID+"]']").val());
					$("[name='fullname']").val($("[name='fullname["+tmpID+"]']").val());
					$("[name='lakcim']").val($("[name='lakcim["+tmpID+"]']").val());
					$("[name='tartozkodasi']").val($("[name='tartozkodasi["+tmpID+"]']").val());
					$("[name='szulhely']").val($("[name='szulhely["+tmpID+"]']").val());
					$("[name='szulnap']").val($("[name='szulnap["+tmpID+"]']").val());
					$("[name='vegzettseg']").val($("[name='vegzettseg["+tmpID+"]']").val());
					$("[name='anyjan']").val($("[name='anyjan["+tmpID+"]']").val());
					$("[name='maganNyugdij']").val($("[name='maganNyugdij["+tmpID+"]']").val());
					$("[name='lakcimKsz']").val($("[name='lakcimKsz["+tmpID+"]']").val());
					$("[name='szig']").val($("[name='szig["+tmpID+"]']").val());
					$("[name='adosz']").val($("[name='adosz["+tmpID+"]']").val());
					$("[name='taj']").val($("[name='taj["+tmpID+"]']").val());
					$("[name='pass']").val($("[name='pass["+tmpID+"]']").val());
					$("[name='phone']").val($("[name='phone["+tmpID+"]']").val());
					$("[name='tether']").val($("[name='tether["+tmpID+"]']").val());
				} else {
					$("#felhForm").attr("action", "index.php?page=11");
				
					$("#felhTitle").html("új felhasználó");
					$("[name='uname']").val("");
					$("[name='fullname']").val("");
					$("[name='lakcim']").val("");
					$("[name='tartozkodasi']").val("");
					$("[name='szulhely']").val("");
					$("[name='szulnap']").val("");
					$("[name='vegzettseg']").val("");
					$("[name='anyjan']").val("");
					$("[name='maganNyugdij']").val("");
					$("[name='lakcimKsz']").val("");
					$("[name='szig']").val("");
					$("[name='adosz']").val("");
					$("[name='taj']").val("");
					$("[name='pass']").val("");
					$("[name='phone']").val("");
					$("[name='tether']").val("");
				}
				
				
			});
		

		});
	}
	
	//kocsma kezeléskor jelszavak változóba
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "13") {
		var passes = new Array();
		$(document).ready(function() {
			var i = 0;
			while ($("[name='chg"+i+"']").length == 1) {
				passes[i] = $.trim($("[name='pass"+i+"']").val());
				i++;
			}
			
			passes[i] = $.trim($("[name='passz']").val());
		});
	}
	
	//keresés és új stand felvitel
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "4" || document.URL.split("?")[1].split("=")[1].split("&")[0] == "1") {
		
		//LOADING
		//$(".signo").append("<div id='loader'><center><br /><font class='anchor3'>LOADING...</font></center></div>");
		
		//SZERVERTŐL MAI NAP LEKÉRÉSE
		var today;
		$.ajax({
			url: "ajax.php?page=5",
			type: 'GET',
			async: false,
			success: function(msg) {
				today = msg;
			}
		});
		
		//DATEPICKER BEÁLLÍTÁSA, keresés naptár rajzolás, kattintás figyelés
		$(document).ready(function(){
			
			//datepickerek
			$('#dateset').datepicker({ defaultDate: today});
			$('#dateset2').datepicker({ defaultDate: today});
			
			//ha keresés van ajax komm
			if ($(".dayCalendar").length != 0) {
			
				makeLines();

				//Menü kezelés
				$(".upperLineStart").click(function(e) { menuHandler(this.id, e.pageX, e.pageY); });
				$(".upperLine").click(function(e) { menuHandler(this.id, e.pageX, e.pageY); });
				$(".downerLineStart").click(function(e) { menuHandler(this.id, e.pageX, e.pageY); });
				$(".downerLine").click(function(e) { menuHandler(this.id, e.pageX, e.pageY); });
				
			}
			
			$("#loader").remove();
		});
	}
	
	//statisztika
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "16") {
		$(document).ready(function() {
			
			//Datepickerek elhelyezése
			activateDatepicker();
			
			//statisztikák
			if ($("#type").length == 1) {
			
				//kocsma kiírás
				if ($("#type").val() == 1) {
					
					//properties
					//i: input hidden sorszáma; j kocsma száma a tömbben, k adott honap
					var pub = new Array();
					var data = new Array();
					var months = new Array();
					var i = 0;
					var j = 0;
					var k = 0;
					
					//Adatok begyűjtése
					var nowpub = 0;
					while ($("#pub"+i).length == 1) {
						
						//első futáskor nowpub megadása
						if (i == 0) {
							nowpub = $("#pub"+i).val();
						}
						
						//Ha egyezik a nowpub
						if (nowpub == $("#pub"+i).val()) {
							
							if (k == 0) {
								months[j] = new Array();
								data[j] = new Array();
							}
							
							pub[j] = {label: $("#pub"+i).val()};
							months[j][k] = $("#month"+i).val();
							data[j][k] = parseInt($("#leado"+i).val());
							
							
							k++;
							
						} else {
							
							k = 0;
							
							nowpub = $("#pub"+i).val();
							j++;
							
							months[j] = new Array();
							data[j] = new Array();
							
							
							pub[j] = {label: $("#pub"+i).val()};
							months[j][k] = $("#month"+i).val();
							data[j][k] = parseInt($("#leado"+i).val());
							
							k++;
							
						}
						
						i++;
					}
					
					// Can specify a custom tick Array.
					// Ticks should match up one for each y value (category) in the series.
					var ticks = months[0];
					 
					var plot1 = $.jqplot('chartdiv', data, {
						// The "seriesDefaults" option is an options object that will
						// be applied to all series in the chart.
						seriesDefaults:{
							renderer:$.jqplot.BarRenderer,
							rendererOptions: {fillToZero: true},
							pointLabels: { show: true, location: 'n', edgeTolerance: -15 }
						},
						// Custom labels for the series are specified with the "label"
						// option on the series option.  Here a series option object
						// is specified for each series.
						series:pub,
						// Show the legend and put it outside the grid, but inside the
						// plot container, shrinking the grid to accomodate the legend.
						// A value of "outside" would not shrink the grid and allow
						// the legend to overflow the container.
						legend: {
							show: true,
							placement: 'insideGrid'
						},
						axes: {
							// Use a category axis on the x axis and use our custom ticks.
							xaxis: {
								renderer: $.jqplot.CategoryAxisRenderer,
								ticks: ticks
							},
							// Pad the y axis just a little so bars can get close to, but
							// not touch, the grid boundaries.  1.2 is the default padding.
							yaxis: {
								pad: 1.05,
								tickOptions: {formatString: '%d Ft'}
							}
						}
					});
				}
				
				//ital kiírás
				if ($("#type").val() == 2) {
					
					var ujforgSzamol = $.trim($("[name='ujforgSzamol']").val());
					if (ujforgSzamol == 1) {

						//adatok domból változóba
						var names = $("[name='italnevek']").val();
						var fogyasok = $("[name='italfogyasok']").val();

						//változók
						names = names.split(";");
						fogyasok = fogyasok.split(";");

						var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

						var barChartData = {
							labels : ["January","February","March","April","May","June","July"],
							datasets : [
								{
									fillColor : "rgba(220,220,220,0.5)",
									strokeColor : "rgba(220,220,220,0.8)",
									highlightFill: "rgba(220,220,220,0.75)",
									highlightStroke: "rgba(220,220,220,1)",
									data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
								}
							]

						}
						window.onload = function(){
							var ctx = document.getElementById("chart-right").getContext("2d");
							window.myBar = new Chart(ctx).Bar(barChartData, {
								responsive : true
							});
						}
						
						/*//json for the chart plugin
						var chartAdatok = {
							labels : names,
							datasets : [
								{
									label: "Idén",
									fillColor : "rgba(250,164,58,0.2)",
									strokeColor : "rgba(250,164,58,1)",
									pointColor : "rgba(250,164,58,1)",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "rgba(250,164,58,1)",
									data : fogyasok
								}
							]
						};

						//dom canvas to variable
						window.onload = function() {
							
							var ctx = document.getElementById("chart-right").getContext("2d");
							window.myBar = new Chart(ctx, {
								type: 'horizontalBar',
								data: chartAdatok
							});

						}*/

					} else {

						//Dátumok begyűjtése
						var dates = new Array();
						var i = 0;
						while ($("#date"+i).length == 1) {
							dates[i] = $("#date"+i).val();
							i++;
						}
						
						//italnév és fogyas begyűjtése
						var italid = new Array();
						var italnev = new Array();
						var i = 0;
						while($("#italid"+i).length == 1) {
							italid[i] = $.trim($("#italid"+i).val())+"";
							italnev[i] = $.trim($("#italnev"+i).val())+"";
							i++;
						}
						
						//fogyasok
						var fogyas = new Array();
						for (var i = 0; i < dates.length; i++) {
							
							fogyas[i] = new Array();
							for (var j = 0; j < italid.length; j++) {
								fogyas[i][j] = parseInt($("#"+dates[i]+"_"+italid[j]+".fogyas").val());
							}
						}
						
						var data = new Array();
						for (var i = 0; i < fogyas[0].length; i++) {
							data[i] = new Array();
							data[i][1] = italnev[i];
							data[i][0] = fogyas[0][i];
						}

						$("#chartdiv").css("height", fogyas[0].length*25);
						
						//kirajzol
						$(document).ready(function(){
							// For horizontal bar charts, x an y values must will be "flipped"
							// from their vertical bar counterpart.
							var plot2 = $.jqplot('chartdiv', [
								data
								], {
								seriesDefaults: {
									renderer:$.jqplot.BarRenderer,
									// Show point labels to the right ('e'ast) of each bar.
									// edgeTolerance of -15 allows labels flow outside the grid
									// up to 15 pixels.  If they flow out more than that, they 
									// will be hidden.
									pointLabels: { show: true, location: 'e', edgeTolerance: -15 },
									// Rotate the bar shadow as if bar is lit from top right.
									shadowAngle: 135,
									// Here's where we tell the chart it is oriented horizontally.
									rendererOptions: {
										barDirection: 'horizontal'
									}
								},
								axes: {
									yaxis: {
										renderer: $.jqplot.CategoryAxisRenderer
									}
								}
							});
						});
					}
					
				}
			
				//Alkalmazott
				if ($("#type").val() == 3) {
					
					//properties
					var users = new Array();
					
					//adatok begyűjtése
					var i = 0;
					while ($("#user"+i).length == 1) {
					
						var tmp = $("#user"+i).val();
						tmp = tmp.split(";");
						users[i] = {value:tmp[1],label:tmp[0]};
						
						i++;
						
					}

					window.onload = function(){
						var ctx = document.getElementById("chart-area").getContext("2d");
						window.myDoughnut = new Chart(ctx).Doughnut(users, {responsive : true});
					};
				}
				
				//elmúlt 1 év
				if ($("#type").val() == 4) {
					
					//properties
					var dates = $("#dates").val().split(";");
					var values = $("#values").val().split(";");
					dates.splice(dates.length-1, 1);
					values.splice(values.length-1, 1);
					
					var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
					var lineChartData = {
						labels : dates,
						datasets : [
							{
								label: "My First dataset",
								fillColor : "rgba(151,187,205,0.2)",
								strokeColor : "rgba(151,187,205,1)",
								pointColor : "rgba(151,187,205,1)",
								pointStrokeColor : "#fff",
								pointHighlightFill : "#fff",
								pointHighlightStroke : "rgba(151,187,205,1)",
								data : values
							}
						]

					}

					window.onload = function(){
						var ctx = document.getElementById("chart-area").getContext("2d");
						window.myLine = new Chart(ctx).Line(lineChartData, {
							responsive: true
						});
					}
				}
			}
		});
	}
	
	//standSor példányosítás ha page == 3 && mod == 0
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "3") {
		
		$(".signo").append("<div id='loader'><center><br /><font class='anchor3'>LOADING...</font></center></div>");
		
		//properties
		var standSor = new Array();
		var autosaveon = true;
		
		//csak módosítható standon lefutó dolgok
		if (document.URL.split("?")[1].split("=")[3].split("&")[0] == "0") {
			
			//Arrow matrix kezelés
			var sor = 0;
			var oszlop = 0;
			var type = 0;
			var pline = 0;
			var modules = new Array();
			var modSorValues = new Array();
			
			//DOM betöltése után lefutó kódok
			$(document).ready(function() {
				
				//csak akkor fusson le ha elérhető a standlap!
				if ($("#standLezar").length == 1) {
					
					//elérhető modulok betöltése (arrowhoz)
					var i = 0;
					
					//akcios stand modul
					if ($("[name='aFogy0']").length == 1) {
						modules[i] = "akcios";
						i++;
					}
					
					//standlap
					if ($("#vetel0").length == 1) {
						modules[i] = "standlap";
						i++;
					}
					
					//módosítható sorok
					if ($("[name='modSorNev0']").length == 1) {
						modules[i] = "modsor";
						i++;
					}
					
					//ételforgalom
					if ($("[name='etelforgalom0']").length == 1) {
						modules[i] = "etelfogyasztas";
						i++;
					}
					
					//egyéb forgalom
					if ($("[name='egyebForgalom0']").length == 1) {
						modules[i] = "egyebforgalom";
						i++;
					}
					
					//lottó stand
					if ($("#lotNet0").length == 1) {
						modules[i] = "lotto";
						i++;
					}
					
					//saját fogyasztás
					if ($("#sajatFogyasztas0").length == 1) {
						modules[i] = "sajatfogy";
						i++;
					}
					
					//bankártyaforgalom
					if ($("[name='kartyaForgalom']").length == 1) {
						modules[i] = "kartyaforgalom";
						i++;
					}
					
					//bevételezés
					if ($("#bevetelNev0").length == 1) {
						modules[i] = "bevetel";
						i++;
					}
					
					//kiadások
					if ($("#kiadasNev0").length == 1) {
						modules[i] = "kiadas";
						i++;
					}
					
					//üzenőfal
					if ($("#chatbox").length == 1) {
						modules[i] = "chatbox";
						i++;
					}
					
					//borravaló
					if ($("[name='kp']").length == 1) {
						modules[i] = "borravalo";
						i++;
					}
					
					//modSor adatmentes
					var k = 0;
					while($("[name='modSorNev"+k+"']").length == 1) {
						
						modSorValues[k] = new Array();
						modSorValues[k][0] = $("[name='modSorNev"+k+"']").val();
						modSorValues[k][1] = $("[name='modSorNyito"+k+"']").val();
						
						k++;
					}
					
					//ha van nyitotomaradvány és többraktár
					if (($("#nyitoToMaradvany").length == 1 && $("#nyitoToMaradvany").val() == 1) && ($("#pluszRaktar").length == 1 && $("#pluszRaktar").val() == 1) && $('[name="standLezar"]').val() == 0) {
						nyitoToMaradvanyPR();
					}
					
					//cookiebol standlapra ha nincs lezárva
					if ($("#standLezar").val() == 0) {
						if (typeof localStorage.getItem("standSorHossz"+$.trim($("#sid").val())) == "string") {
							getFromCookies();
						}
					}
					
					//időzített folyamatok
					//autosave 10 másodpercenként!
					var uploadInterval = setInterval(function() {
						if ($("#standLezar").val() == 0) {
							saveToCookies();
						}
					}, 10000);
					
					//forceclosed
					if (document.URL.split("?")[1].split("=")[3].split("&")[1] == "forceclose" && document.URL.split("?")[1].split("=")[4].split("&")[0] == "1") {
						
						var kuldjel = true;
						
						//akcios
						var i = 0;
						while ($("[name='aFogy"+i+"']").length == 1) {
							
							if ($("[name='aFogy"+i+"']").val() == "") {
								kuldjel = false;
							}
							
							i++;
							
						}
						
						//standlap
						i = 0;
						while ($("#vetel"+i).length == 1) {
							
							if ($("#vetel"+i).val() == "" || $("#maradvany"+i).val() == "") {
								kuldjel = false;
							}
							
							i++;
						}
						
						//modosithato
						i = 0;
						while ($("[name='modSorNev"+i+"']").length == 1) {
							
							if ($("[name='modSorNev"+i+"']").val() != "" && ($("[name='modSorVetel"+i+"']").val() == "" || $("[name='modSorMarad"+i+"']").val() == "" || $("[name='modSorAr"+i+"']").val() == "") && $("[name='modSorDel"+i+"']").val() == 0) {
								kuldjel = false;
							}
							
							i++;
						}
						
						//borravalo
						if ($("[name='borravalo']").val() == "") {
							kuldjel = false;
						}
						
						if (kuldjel) {
							$("#frm").submit();
						} else {
							window.location = "index.php";
						}
					}
					
					newFocus(modules[type], sor, oszlop, pline);
					
					//gombfigyelő 39 -> 37 <- 38 /\ 40 \/
					$(document).keydown(function(e){
						
						//console.log(modules[type] + " - " + sor + " " + oszlop + " " + pline);
						
						if (e.keyCode == 38) {
							
							//type megállapítás
							if (modules[type] == "akcios") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									// akcios nem csinál semmit mert biztos az a legelso ha meg nincs nem jon be ide
								}
							
							//standlap
							} else if (modules[type] == "standlap") {
								
								//HA VAN PLUSZ RAKTÁR
								if ($("#pluszRaktar").val() == 1) {
									
									//ha első sorban vagyunk
									if (pline == 0) {
										
										//második sorig csak csökkentse egyel a sor számát
										if (sor > 0) {
											
											//maradvány oszlop
											if (oszlop == 1) {
												
												//raktárak lekérése
												var tmpraktarak = $("[name='pluszRaktarak']").val();
												tmpraktarak = tmpraktarak.split(";");
												pline = tmpraktarak[tmpraktarak.length-2];
												
											//vétel oszlop
											} else if (oszlop == 0) {
												
												//ha az előző sorban van plusz vételezés sor
												if ($("#pvetel"+(sor-1)).length == 1) {
													pline = 1;
												} else {
													pline = 0;
												}
												
											}
											
											sor--;
											newFocus(modules[type], sor, oszlop, pline);
										
										//első sorban a modult is csökkentse!
										} else {
											
											//ha van még felette modul!
											if (type != 0) {
												
												var last = lastSor(modules[type-1]);
												
												sor = last;
												oszlop = 0;
												type--;
												
												newFocus(modules[type], sor, oszlop, pline);
											}
										}
										
									//ha Nem az első sorban vagyunk
									} else {
										
										//ha vételezés oszlopban vagyunk
										if (oszlop == 0) {
											
											pline = 0;
											newFocus(modules[type], sor, oszlop, pline);
											
										//ha maradvány oszlopban vagyunk
										} else if (oszlop == 1) {
											
											//megkeressük van-e felette még plusz sor
											var tmp = $("[name='pluszRaktarak']").val();
											tmp = tmp.split(";");
											var holvagyok = false;
											for (var m = 0; m < tmp.length-1; m++) {
												if (tmp[m] == pline) {
													holvagyok = m;
													break;
												}
											}
											
											//ha van még felette sor
											if (holvagyok != 0) {
												pline = tmp[holvagyok-1];
												newFocus(modules[type], sor, oszlop, pline);
											} else {
												pline = 0;
												newFocus(modules[type], sor, oszlop, pline);
											}
										}
										
									}
									
									
								//NORMÁL RAKTÁRKÉSZLET
								} else {
									
									//második sorig csak csökkentse egyel a sor számát
									if (sor > 0) {
										sor--;
										newFocus(modules[type], sor, oszlop);
									
									//első sorban a modult is csökkentse!
									} else {
										
										//ha van még felette modul!
										if (type != 0) {
											
											var last = lastSor(modules[type-1]);
											
											sor = last;
											oszlop = 0;
											type--;
											
											newFocus(modules[type], sor, oszlop);
										}
									}
								}
								
							
							//módosítható sorok
							} else if (modules[type] == "modsor") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									var last = lastSor(modules[type-1]);
									
									sor = last;
									oszlop = 0;
									type--;
									
									newFocus(modules[type], sor, oszlop);
								}
							
							//lottó
							} else if (modules[type] == "lotto") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									var last = lastSor(modules[type-1]);
									
									sor = last;
									type--;
									
									newFocus(modules[type], sor, oszlop);
								}
							
							//bevetel
							} else if (modules[type] == "bevetel") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									var last = lastSor(modules[type-1]);
									
									sor = last;
									type--;
									
									newFocus(modules[type], sor, oszlop);
								}
							
							//kiadas
							} else if (modules[type] == "kiadas") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									var last = lastSor(modules[type-1]);
									
									sor = last;
									type--;
									
									newFocus(modules[type], sor, oszlop);
								}
								
							//saját fogyasztás
							} else if (modules[type] == "sajatfogy") {
								
								//ha nem az első sorban van
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
									
								//első sorban előző modulra megy
								} else {
									sor = lastSor(modules[type-1]);
									type--;
									newFocus(modules[type], sor, oszlop);
								}
								
							//Egyéb forgalom
							} else if (modules[type] == "egyebforgalom") {
								
								//ha nem az első sorban van
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
									
								//első sorban előző modulra megy
								} else {
									sor = lastSor(modules[type-1]);
									type--;
									newFocus(modules[type], sor, oszlop);
								}
								
							//chatbox
							} else if (modules[type] == "chatbox") {
								
								
								var last = lastSor(modules[type-1]);
								
								sor = last;
								type--;
								
								newFocus(modules[type], sor, oszlop);
								
								
							//ételforgalom
							} else if (modules[type] == "etelfogyasztas") {
								
								//ha létezik
								if ($("[name='etelforgalom"+(sor-1)+"']").length == 1) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									type--;
									sor = lastSor(modules[type]);
									newFocus(modules[type], sor, oszlop);
								}
								
								
							} else if (modules[type] == "kartyaforgalom") {
								
								sor = lastSor(modules[type-1]);
								type--;
								
								newFocus(modules[type], sor, oszlop);
								
								
							//borravalo
							} else if (modules[type] == "borravalo") {
								
								if (sor > 0) {
									sor--;
									newFocus(modules[type], sor, oszlop);
								} else {
									
									var last = lastSor(modules[type-1]);
									
									sor = last;
									type--;
									
									newFocus(modules[type], sor, oszlop);
									
								}
								
							}
							
						}
						
						if (e.keyCode == 40) {
						   
							//type megállapítás
							if (modules[type] == "akcios") {
								
								//sorok számának megállapítása
								var m = 0;
								while ($("[name='aFogy"+m+"']").length == 1) {
									m++;
								}
								var top = m;
								
								//lefelé nyíl belefér -e az akciós sorokba
								if (sor + 1 < m) {
									sor++;
									newFocus(modules[type], sor, oszlop);
								
								//következő modul
								} else {
									sor = 0;
									oszlop = 0;
									type++;
									newFocus(modules[type], sor, oszlop);
								}
							
							//standlap
							} else if (modules[type] == "standlap") {
								
								//HA PLUSZ RAKTAR VAN
								if ($("#pluszRaktar").val() == "1") {
									
									//lekéri milyen plusz raktárak vannak
									var tmpraktarak = $("[name='pluszRaktarak']").val();
									tmpraktarak = tmpraktarak.split(";");
									
									//ha vételezés oszlopban van
									if (oszlop == 0) {
										
										//ha felső sorban van
										if (pline == 0) {
											
											//ha van alsó sora
											if ($("#vetel"+sor).val().search("-") != -1 ) {
												pline = 1;
												newFocus(modules[type], sor, oszlop, pline);
											
											//ha nincs
											} else {
												
												//ha van még következő sor
												if (sor + 1 < standSor.length) {
													sor++;
													pline = 0;
													newFocus(modules[type], sor, oszlop, pline);
												
												//következő modul
												} else {
													sor = 0;
													pline = 0;
													type++;
													newFocus(modules[type], sor, oszlop, pline);
												}
											}
											
											
										//ha az alsó sorban van
										} else {
											
											//ha van még következő sor
											if (sor + 1 < standSor.length) {
												sor++;
												pline = 0;
												newFocus(modules[type], sor, oszlop, pline);
											
											//következő modul
											} else {
												sor = 0;
												pline = 0;
												type++;
												newFocus(modules[type], sor, oszlop, pline);
											}
										}
										
									//ha maradvány oszlopban van
									} else if (oszlop == 1) {
										
										//ha kissebb mint a legnagyobb 
										if (pline < tmpraktarak[tmpraktarak.length-2]) {
											
											//megkeresi hol van most a tömbben
											var holvagyok = 0;
											var bejott = false;
											for (var h = 0; h < tmpraktarak.length-1; h++) {
												if (pline == tmpraktarak[h]) {
													bejott = true;
													holvagyok = h;
													break;
												}
											}
											
											//hozzáad egyet
											if (bejott) {
												holvagyok++;
											}
											pline = tmpraktarak[holvagyok];
											newFocus(modules[type], sor, oszlop, pline);
										
										//ha utolsó plineban van akkor jön a kövi sor 0 plineal
										} else {
											
											//Van még következő sor a modulban
											if (sor + 1 < standSor.length) {
												sor++;
												pline = 0;
												newFocus(modules[type], sor, oszlop, pline);
											
											//következő modul
											} else {
												sor = 0;
												pline = 0;
												type++;
												newFocus(modules[type], sor, oszlop, pline);
											}
											
										}
										
									}
									
								
								//HA NINCS RAKTÁR
								} else {
									
									//lefelé nyíl belefér -e az akciós sorokba
									if (sor + 1 < standSor.length) {
										sor++;
										newFocus(modules[type], sor, oszlop);
									
									//következő modul
									} else {
										sor = 0;
										type++;
										newFocus(modules[type], sor, oszlop);
									}
									
								}
								
							
							//módosítható sorok
							} else if (modules[type] == "modsor") {
								
								//sorok számának megállapítása
								var m = 0;
								while ($("[name='modSorNev"+m+"']").length == 1) {
									m++;
								}
								var top = m;
								
								//lefelé nyíl belefér -e az akciós sorokba
								if (sor + 1 < m) {
									sor++;
									newFocus(modules[type], sor, oszlop);
								
								//következő modul
								} else {
									sor = 0;
									oszlop = 0;
									type++;
									newFocus(modules[type], sor, oszlop);
								}
							
							//lottó
							} else if (modules[type] == "lotto") {
								
								//sorok számának megállapítása
								var m = 0;
								while ($("#lotNet"+m).length == 1) {
									m++;
								}
								var top = m;
								
								//lefelé nyíl belefér -e az akciós sorokba
								if (sor + 1 < m) {
									sor++;
									newFocus(modules[type], sor, oszlop);
								
								//következő modul
								} else {
									sor = 0;
									type++;
									newFocus(modules[type], sor, oszlop);
								}
							
							//bevétel
							} else if (modules[type] == "bevetel") {
								
								//ha van kövi sor akkor ++ ha már nincs akkor kövi modul
								if ($("#bevetelNev"+(sor+1)).length == 1) {
									sor++;
								} else {
									type++;
									sor = 0;
								}
								newFocus(modules[type], sor, oszlop, pline);
								
							//kiadás
							} else if (modules[type] == "kiadas") {
								
								//sorok számának megállapítása
								var m = 0;
								while ($("#kiadasNev"+m).length == 1) {
									m++;
								}
								var top = m;
								
								//lefelé nyíl belefér -e az akciós sorokba
								if (sor + 1 < m) {
									sor++;
									newFocus(modules[type], sor, oszlop, pline);
								
								//következő modul
								} else {
									sor = 0;
									oszlop = 0;
									type++;
									newFocus(modules[type], sor, oszlop, pline);
								}
								
							//saját fogyasztásos doboz
							} else if (modules[type] == "sajatfogy") {
								
								//ha van még kövi sor
								if ($("#sajatFogyasztas"+(sor+1)).length == 1) {
									sor++;
								} else {
									sor = 0;
									type++;
								}
								newFocus(modules[type], sor, oszlop, pline);
								
							//egyéb forgalom
							} else if (modules[type] == "egyebforgalom") {
								
								//ha van még kövi sor
								if ($("#egyebForgalom"+(sor+1)).length == 1) {
									sor++;
								} else {
									sor = 0;
									type++;
								}
								newFocus(modules[type], sor, oszlop, pline);
								
							//chatbox
							} else if (modules[type] == "chatbox") {
								
								sor = 0;
								oszlop = 0;
								type++;
								newFocus(modules[type], sor, oszlop, pline);
								
							//etelfogyasztas
							} else if (modules[type] == "etelfogyasztas") {
								
								//ha van következő sor még
								if ($("[name='etelforgalom"+(sor+1)+"']").length) {
									
									sor++;
									newFocus(modules[type], sor, oszlop);
									
								//nincs már, ezért következő modul
								} else {
									type++;
									sor = 0;
									newFocus(modules[type], sor, oszlop);
								}
								
							//kártyaforgalom
							} else if (modules[type] == "kartyaforgalom") {
								
								sor = 0;
								oszlop = 0;
								type++;
								newFocus(modules[type], sor, oszlop, pline);
								
							//borravalo
							} else if (modules[type] == "borravalo") {
								
								if (sor < 1) {
									sor++;
									newFocus(modules[type], sor, oszlop, pline);
								}
								
							}
						   
						}
						
						if (e.keyCode == 39) { 
							
							//ha akcios, üzenőfal vagy borravalo akkor nem csdinál semmit mert azok 1 oszloposak
							if (modules[type] == "akcios" || modules[type] == "chatbox" || modules[type] == "borravalo" || modules[type] == "kartyaforgalom") {
								
							//2 oszloposok
							} else if (modules[type] == "standlap" || modules[type] == "lotto" || modules[type] == "kiadas" || modules[type] == "bevetel") {
								
								if (oszlop < 1) {
									oszlop++;
									newFocus(modules[type], sor, oszlop);
								}
							
							//4 oszlopos
							} else if (modules[type] == "modsor") {
								if (oszlop < 3) {
									oszlop++;
									newFocus(modules[type], sor, oszlop);
								}
							}
							
						}
						
						if (e.keyCode == 37) { 
						   
						   if (oszlop > 0) {
								oszlop--;
								newFocus(modules[type], sor, oszlop);
							}
						   
						}
						
						//adat törlés
						if (e.keyCode == 8) {
							
							//type megállapítás
							if (modules[type] == "akcios") {
								
								$("[name='aFogy"+sor+"']").val("");
							
							//standlap
							} else if (modules[type] == "standlap") {
								
								if (oszlop == 0) {
									if (pline == 0) {
										$("#vetel"+sor).val("");
									} else {
										$("#pvetel"+sor).val("");
									}
								} else {
									if ($("#pluszRaktar").val() == "1") {
										$("#maradvany"+sor+""+pline).val("");
										
									} else {
										$("#maradvany"+sor).val("");
										
									}
									
								}
								
							
							//módosítható sorok
							} else if (modules[type] == "modsor") {
								
								if (oszlop == 0) {
									$("[name='modSorNev"+sor+"']").val("");
								} else if (oszlop == 1) {
									$("[name='modSorVetel"+sor+"']").val("");
								} else if (oszlop == 2) {
									$("[name='modSorMarad"+sor+"']").val("");
								} else if (oszlop == 3) {
									$("[name='modSorAr"+sor+"']").val("");
								}
							
							//lottó
							} else if (modules[type] == "lotto") {
							
								if (oszlop == 0) {
									$("#lotNet"+sor).val("");
								} else {
									$("#lotNet"+sor).val("");
								}
							
							//bevételezés
							} else if (modules[type] == "bevetel") {
								
								if (oszlop == 0) {
									$("#bevetelNev"+sor).val("");
								} else {
									$("#bevetelAr"+sor).val("");
								}
								
							//kiadások
							} else if (modules[type] == "kiadas") {
								
								if (oszlop == 0) {
									$("#kiadasNev"+sor).val("");
								} else {
									$("#kiadasAr"+sor).val("");
								}
							
							//sajátfogyasztás
							} else if (modules[type] == "sajatfogy") {
								$("#sajatFogyasztas"+sor).val("");
								
							//ételforgalom
							} else if (modules[type] == "etelfogyasztas") {
								$("[name='etelforgalom"+sor+"']").val("");
								
							//kártyaforgalom
							} else if (modules[type] == "kartyaforgalom") {
								$("[name='kartyaForgalom']").val("");
								
							//chatbox
							} else if (modules[type] == "chatbox") {
								
							//borravalo
							} else if (modules[type] == "borravalo") {
								
							}
						}
						
						//enterre ne legyen form küldés
						if(event.keyCode == 13) {
						  event.preventDefault();
						  return false;
						}
						
					});
				}
				
			});
			
		}
		
		$(document).ready(function() {
			
			//csak akkor fusson le ha elérhető a standlap
			if ($("#standLezar").length == 1) {
			
				//egyéb műveletek
				var i = 0;
				var aktcsid = -1;
				
				//HA TÖBB RAKTÁR IS VAN
				if ($("#pluszRaktar").val() == 1) {
					
					//Leszedi milyen id-jű plusz raktárak vannak
					var raktarak = $("[name='pluszRaktarak']").val();
					raktarak = raktarak.split(";");
					
					//Példányosítás amíg van sor
					while (typeof $("#vetel"+i).val() !== "undefined") {
						
						//ideiglenes tömbök deklarálása a nyitóknak és maradványoknak
						var tmpNyitok = new Array();
						var tmpVetel = new Array();
						var tmpMaradvanyok = new Array();
						
						//pult készlet tömbbe
						tmpNyitok[0] = $("#newNyito"+i+"0").val();
						tmpVetel[0] = $("#vetel"+i).val();
						tmpMaradvanyok[0] = $("#maradvany"+i+"0").val();
						
						//ha van plusz vételezés sor
						if ($("#pvetel"+i).length == 1) {
							tmpVetel[1] = $("#pvetel"+i).val();
						}
						
						//többi készlet tömbbe
						for (var j = 0; j < raktarak.length-1; j++) {
							tmpNyitok[(j+1)] = $("#newNyito"+i+""+(raktarak[j])).val();
							tmpMaradvanyok[(j+1)] = $("#maradvany"+i+""+(raktarak[j])).val();
						}
						
						//id, did, ny, v, o, m, f, e, a, feltoltve, csid;
						if ($("#vetel"+i).val() != "" || $("#maradvany"+i+"0").val() != "") {
							standSor[i] = new stSor($("#sorId"+i).val(), $.trim($("#did"+i).val()), tmpNyitok, tmpVetel, $.trim($("#osszesen"+i).html()), tmpMaradvanyok, $.trim($("#fogyas"+i).html()), $.trim($("#ertek"+i).html()), $.trim($("#egysegar"+i).html()), $.trim($("#csid"+i).val()), $("#forditott"+i).val(), true);
							
						} else {
							standSor[i] = new stSor(0, $.trim($("#did"+i).val()), tmpNyitok, 0, $.trim($("#osszesen"+i).html()), tmpMaradvanyok, 0, 0, $.trim($("#egysegar"+i).html()), $.trim($("#csid"+i).val()), $("#forditott"+i).val(), true);
							
						}
						
						standSor[i].countAll(i, 0);
						
						//ELSŐ futáskor CSID beállítás
						if (i == 0) {
							aktcsid = standSor[i].csid;
						}
						
						//CSOPORT ÖSSZEADÁS
						if (aktcsid != standSor[i].csid) {
							countFinVal(aktcsid);
							aktcsid = standSor[i].csid;
						}
						
						i++;
					}
					
					
				//HA CSAK 1
				} else {
					
					//standsor példányosítás, csoport összeadás
					while (typeof $("#maradvany"+i).val() !== "undefined") {
						
						//id, did, ny, v, o, m, f, e, a, feltoltve, csid;
						if ($("#vetel"+i).val() != "" || $("#maradvany"+i).val() != "") {
							standSor[i] = new stSor($("#sorId"+i).val(), $.trim($("#did"+i).val()), $.trim($("#nyito"+i).html()), $("#vetel"+i).val(), $.trim($("#osszesen"+i).html()), $("#maradvany"+i).val(), $.trim($("#fogyas"+i).html()), $.trim($("#ertek"+i).html()), $.trim($("#egysegar"+i).html()), $.trim($("#csid"+i).val()), $("#forditott"+i).val(), false);
						} else {
							standSor[i] = new stSor(0, $.trim($("#did"+i).val()), $.trim($("#nyito"+i).html()), 0, $.trim($("#osszesen"+i).html()), 0, 0, 0, $.trim($("#egysegar"+i).html()), $.trim($("#csid"+i).val()), $("#forditott"+i).val(), false);
						}
						
						standSor[i].countAll(i, 0);
						
						//ELSŐ futáskor CSID beállítás
						if (i == 0) {
							aktcsid = standSor[i].csid;
						}
						
						//CSOPORT ÖSSZEADÁS
						if (aktcsid != standSor[i].csid) {
							countFinVal(aktcsid);
							aktcsid = standSor[i].csid;
						}
						
						i++;
					}
					
				}
				
				//számolások
				countFinVal(aktcsid);
				countLotAll();
				countLotPer();
				countKiadAll();
				
				//kp kiszámolása borravalübül ha szükséges
				if ($("[name='borravalo']").val() != "" && $("[name='kp']").val() == "") {
					$("[name='kp']").val(parseInt($("[name='leado']").val()) + parseInt($("[name='borravalo']").val()));
				}
			}	
			$("#loader").remove();
			
		});
		
		
	}
	
	//stand törlése
	if (document.URL.split("?")[1].split("=")[1].split("&")[0] == "26") {
		
		function standTorlesClient(id) {
			
			if(confirm('Biztos törölni akarod a standot?')) {
				$.ajax({
					type: 'POST',
					url: 'ajax.php?page=10',
					data: {SID: id},
					async: false,
					success: function(data) {
						if(data == 'success') window.location = "index.php?page=4";
					}
				});
			} else {
				window.location = "index.php?page=4";
			}
			
		}
		
	}

//INDEXEN FUSSON	
} else {
	$(document).ready(function() {
		
		/* DASHSTAT */
		if ($("#chart-left").length == 1) {
			
			//properties
			var users = new Array();
			
			//adatok begyűjtése
			var i = 0;
			while ($("#user"+i).length == 1) {
			
				var tmp = $("#user"+i).val();
				tmp = tmp.split(";");
				users[i] = {value:tmp[1],label:tmp[0]};
				
				i++;
				
			}
			
			//properties
			var dates = $("#dates").val().split(";");
			var values1 = $("#values1").val().split(";");
			var values2 = $("#values2").val().split(";");
			dates.splice(dates.length-1, 1);
			values1.splice(values1.length-1, 1);
			values2.splice(values2.length-1, 1);
			
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
						data : values2
					},
					{
						label: "Idén",
						fillColor : "rgba(250,164,58,0.2)",
						strokeColor : "rgba(250,164,58,1)",
						pointColor : "rgba(250,164,58,1)",
						pointStrokeColor : "#fff",
						pointHighlightFill : "#fff",
						pointHighlightStroke : "rgba(250,164,58,1)",
						data : values1
					}
				]

			}
			
			//HA VAN SORSJEGY FOGYÁS!
			if ($("#sorsjegy-canvas").length == 1) {
				
				//SORSJEGY FOGYÁS
				var shonapok = $("#sdates").val().split(";");
				var sadatok = $("#svalues").val().split(";");
				shonapok.splice(shonapok.length-1, 1);
				sadatok.splice(sadatok.length-1, 1);
				
				var slineChartData = {
					labels : shonapok,
					datasets : [
						{
							label: "My First dataset",
							fillColor : "rgba(151,187,205,0.2)",
							strokeColor : "rgba(151,187,205,1)",
							pointColor : "rgba(151,187,205,1)",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "rgba(151,187,205,1)",
							data : sadatok
						}
					]

				}
				
				//KIADÁS ÁRAK
				var splabels = $("#splabels").val().split(";");
				var spadatok = $("#spvalues").val().split(";");
				splabels.splice(splabels.length-1, 1);
				spadatok.splice(spadatok.length-1, 1);
				
				var splineChartData = {
					labels : splabels,
					datasets : [
						{
							label: "My First dataset",
							fillColor : "rgba(151,187,205,0.2)",
							strokeColor : "rgba(151,187,205,1)",
							pointColor : "rgba(151,187,205,1)",
							pointStrokeColor : "#fff",
							pointHighlightFill : "#fff",
							pointHighlightStroke : "rgba(151,187,205,1)",
							data : spadatok
						}
					]

				}
			}

			window.onload = function(){
				
				var ctx = document.getElementById("chart-right").getContext("2d");
				window.myDoughnut = new Chart(ctx).Doughnut(users, {responsive : true});
				
				var ctx2 = document.getElementById("chart-left").getContext("2d");
				window.myLine = new Chart(ctx2).Line(lineChartData, {
					responsive: true
				});
				
				if ($("#sorsjegy-canvas").length == 1) {
					var ctx3 = document.getElementById("sorsjegy-canvas").getContext("2d");
					window.myLine = new Chart(ctx3).Line(slineChartData, {
						responsive: true
					});
					
					var ctx4 = document.getElementById("sorsjegyny-canvas").getContext("2d");
					window.myLine = new Chart(ctx4).Line(splineChartData, {
						responsive: true
					});
				}
			}
		}
	});
}

//buborékokhoz menü baloldalának megállapítása
function getMenuLeft() {
	
	//set menucontainer default positions
	var menuCont = $("#menuContainer").width();
	var scrWidth = $(document).width();
	var menuLeft = (scrWidth - menuCont) / 2
	
	return menuLeft;
	
}


//ikonok
$(document).ready(function() {
	
	//főoldal
	if ($("#MenuFooldal").length == 1) {

		$("#MenuFooldal").click(function () {
			window.location = "index.php";
		});
		
	}
	
	//új stand felvitele
	if ($("#MenuStand").length == 1) {

		$("#MenuStand").click(function () {
			window.location = "index.php?page=1";
		});
		
	}
	
	//szülinaposok menü
	if ($("#MenuBirthday").length == 1) {

		$("#MenuBirthday").click(function () {
			window.location = "index.php?page=31";
		});
		
	}
	
	//Kereső
	if ($("#MenuSearch").length == 1) {

		$("#MenuSearch").click(function () {
			window.location = "index.php?page=4";
		});
		
	}
	
	//felhasználó kezelő
	if ($("#MenuFelh").length == 1) {

		$("#MenuFelh").click(function () {
			window.location = "index.php?page=9";
		});
		
	}
	
	//Kocsma beállítások
	if ($("#MenuKocsmaBeall").length == 1) {

		$("#MenuKocsmaBeall").click(function () {
			window.location = "index.php?page=28";
		});
		
	}
	
	//Sorsjegy menüpont
	if ($("#MenuSorsjegy").length == 1) {

		$("#MenuSorsjegy").click(function () {
			window.location = "index.php?page=29";
		});
		
	}
	
	//itallap
	if ($("#MenuItal").length == 1) {

		$("#MenuItal").click(function () {
			window.location = "index.php?page=6";
		});
		
	}
	
	//kocsma kezelő
	if ($("#MenuKocsma").length == 1) {

		$("#MenuKocsma").click(function () {
			window.location = "index.php?page=13";
		});
		
	}
	
	//statisztika
	if ($("#MenuStat").length == 1) {

		$("#MenuStat").click(function () {
			window.location = "index.php?page=16";
		});
		
	}
	
	//dinamikus árukészlet feltöltő
	if ($("#MenuUp").length == 1) {

		$("#MenuUp").click(function () {
			window.location = "index.php?page=17";
		});
		
	}
	
	//személyes beállítások
	if ($("#MenuSettings").length == 1) {

		$("#MenuSettings").click(function () {
			window.location = "index.php?page=19";
		});
		
	}
	
	//kijelentkezés
	if ($("#MenuLogout").length == 1) {

		$("#MenuLogout").click(function () {
			window.location = "index.php?page=logout";
		});
		
	}
	
	//lap tetejére
	if ($("#toTop").length == 1) {

		$("#toTop").click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
		});
		
	}
	
	// ** BUBORÉKOK ** //
	
	//Főoldal
	if ($("#MenuFooldal").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuFooldal").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_fooldal.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuFooldal").position().left - 50 + 17)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
				
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuFooldal").mouseleave(function(e) {
			$("#label").remove();
		});
		
	}
	
	//Új stand
	if ($("#MenuStand").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuStand").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_ujstand.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuStand").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuStand").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//Új stand
	if ($("#MenuBirthday").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuBirthday").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_birthday.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuBirthday").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuBirthday").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuSearch
	if ($("#MenuSearch").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuSearch").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_kereses.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuSearch").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuSearch").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuFelh
	if ($("#MenuFelh").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuFelh").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_felhaszn.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuFelh").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuFelh").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuStat
	if ($("#MenuStat").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuStat").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_statisztika.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuStat").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuStat").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuItal
	if ($("#MenuItal").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuItal").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_itallap.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuItal").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuItal").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuKocsma
	if ($("#MenuKocsma").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuKocsma").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_kocsma.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuKocsma").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuKocsma").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuKocsma beállítások
	if ($("#MenuKocsmaBeall").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuKocsmaBeall").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_kocsmaBeall.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuKocsmaBeall").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuKocsmaBeall").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuUp
	if ($("#MenuUp").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuUp").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_import.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuUp").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuUp").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuSettings
	if ($("#MenuSettings").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuSettings").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_szemelyes.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuSettings").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuSettings").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//Sorsjegy
	if ($("#MenuSorsjegy").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuSorsjegy").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_sorsjegy.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuSorsjegy").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuSorsjegy").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
	//MenuLogout
	if ($("#MenuLogout").length == 1) {
		
		//Buborék elhelyezése
		$("#MenuLogout").mouseover(function(e) {
			
			//only if it doesnt exist yet
			if ($("#label").length == 0) {
				
				var menuLeft = getMenuLeft();
				
				var style = "position:absolute;background-image:url(img/label_kijelentkezes.png);width:100px;height:50px;top:40px;left:"+(menuLeft + $("#MenuLogout").position().left - 50 + 27)+"px;";
				$(".signo").append("<div style='"+style+"' id='label'></div>");
			
			}
			
		});
		
		//buborék kinyírása
		$("#MenuLogout").mouseleave(function(e) {
			$("#label").remove();
		});
		
		
	}
	
});