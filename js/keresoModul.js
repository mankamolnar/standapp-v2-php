//modul properties
var nowmenu = "";
var library = new Array();
var tmpid = 0;

//date2Ymd metódus
function date2Ymd(adate) {
	return adate.getFullYear() +""+ (adate.getMonth()+1)+""+ adate.getDate();
}

//Vonalak megrajzolása
function makeLines() {
	
	//tol ig értékek beszedése
	var tol = $("[name='tol']").val();
	var ig = $("[name='ig']").val();
	var pid = $("[name='pid']").val();
	
	//ajax kérés küldése
	var serial;
	$.ajax({
		url: "ajax.php?page=6&tol="+tol+"&ig="+ig+"&pid="+pid,
		type: 'GET',
		async: false,
		success: function(msg) {
			serial = msg;
		}
	});
	
	//találatok széttördelése
	var serials = serial.split(';');
	
	//kezdő dátumok megállapítása
	var startDate = new Array();
	for (var i = 0; i < serials.length-1; i++) {
		
		var hossz = serials[i].split(",")[2];
		var tmp = serials[i].split(",")[1];
		tmp = tmp.split(" ")[0];
		tmp = tmp.split("-");
		startDate[startDate.length] = new Date(tmp[0], parseInt(tmp[1])-1, tmp[2]-hossz+1);
	}
	
	
	//csíkozás
	var k = 0;
	for (var i = 0; i < serials.length - 1; i++) {
		
		//van - e már csík a kezdő vagy végpozícióban
		var vancsik = false;
		if ($("#ul"+date2Ymd(startDate[i])).length != 0 || $("#ul"+date2Ymd(new Date(serials[i].split(',')[1]))).length != 0) {
			vancsik = true;
		}
		
		//kezdő csík névvel attól függöen hogy van - e csík már a dobozban
		if (vancsik == false) {
			
			var tick = "";
			if (serials[i].split(',')[4] == "1") {
				tick = "<img src='img/tick.png' class='standHeadIcon' />";
			}
			
			$("#d"+date2Ymd(startDate[i])).append("<div class='upperLineStart' id='ul"+date2Ymd(startDate[i])+"'>&nbsp;"+ serials[i].split(',')[3] +" "+tick+"</a></div>");
			library[k] = new Array("ul"+date2Ymd(startDate[i]), serials[i].split(",")[0]);
			
		} else {
			
			$("#d"+date2Ymd(startDate[i])).append("<div class='downerLineStart' id='dl"+date2Ymd(startDate[i])+"'>&nbsp;"+ serials[i].split(',')[3] +" "+tick+"</div>");
			library[k] = new Array("dl"+date2Ymd(startDate[i]), serials[i].split(",")[0]);
			
		}
		k++;
		
		//többi csík
		for ( var j = 1; j < serials[i].split(',')[2]; j++) {
			
			var tmp = new Date(startDate[i].getFullYear(), startDate[i].getMonth(), startDate[i].getDate() + j);
			
			if (vancsik == false) {
				
				$("#d"+date2Ymd(tmp)).append("<div class='upperLine' id='ul"+date2Ymd(tmp)+"'></div>");
				
				library[k] = new Array("ul"+date2Ymd(tmp), serials[i].split(",")[0]);
				
			} else {
			
				$("#d"+date2Ymd(tmp)).append("<div class='downerLine' id='dl"+date2Ymd(tmp)+"'></div>");
				
				library[k] = new Array("dl"+date2Ymd(tmp), serials[i].split(",")[0]);
				
			}
			
			k++;
			
			
		}
		
	}
	
}

//menü kezelő
function menuHandler(id, x, y) {
	
	//ha még nics menu akkor elhelyezés
	if ($(".searchMenu").length == 0) {
		
		nowmenu = id;
		
		//id kikeresése
		for (var i = 0; i < library.length; i++) {
		
			if (library[i][0] == nowmenu) {
				tmpid = library[i][1];
				break;
			}
			
		}
		
		//divek elhelyezése
		$(".signo").append("<div class='searchMenu' style='position:absolute; left:"+x+"px; top:"+y+"px; display:none;'></div>");
		$(".searchMenu").fadeIn();
		$(".searchMenu").append("<div id='eyeMenu'></div>");
		$(".searchMenu").append("<div class='separator'></div>");
		$(".searchMenu").append("<div id='penMenu'></div>");
		$(".searchMenu").append("<div class='separator'></div>");
		$(".searchMenu").append("<div id='trashMenu'></div>");
		
		//gombok elhelyezése
		$("#eyeMenu").click(function(e) {
			window.location = "index.php?page=3&id="+tmpid+"&mod=1";
		});
		
		//gombok elhelyezése
		$("#penMenu").click(function(e) {
			window.location = "index.php?page=3&id="+tmpid+"&mod=0";
		});
		
		//gombok elhelyezése
		$("#trashMenu").click(function(e) {
			window.location = "index.php?page=26&id="+tmpid;
		});
		
	//hamár van menü törlés v áthelyezés
	} else {
		
		//ellenőrzés h ugyanarra csikra nyomott-e
		if (nowmenu == id) {
			
			$(".searchMenu").fadeOut(400, function(){ $(".searchMenu").remove(); });
		
		//áthelyezés
		} else {
			
			nowmenu = id;
			
			//id kikeresése
			for (var i = 0; i < library.length; i++) {
			
				if (library[i][0] == nowmenu) {
					tmpid = library[i][1];
					break;
				}
				
			}
			
			$(".searchMenu").css({"left": x+"px", "top": y+"px"});
		
		}
		
	}
	
}