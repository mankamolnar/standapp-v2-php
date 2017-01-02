function sorsjegypidment() {
	
	$.ajax({
		url: "ajax.php?page=30&action=sorsjegypidment&pid=" +$("#pubid").val()+ "&spubid="+$.trim($("[name='sorsjegyPubId']").val()),
		type: 'GET',
		async: true,
		success: function() {
			window.location.replace("index.php?page=29");
		},
		error: function() {
			alert("valami hiba történt!");
		}
	});
	
}

function addnyeremeny() {
	
	$.ajax({
		url: "ajax.php?page=30&action=newnyeremeny&pid=" +$("#pubid").val()+ "&nyid=" +$("[name='newNyeremeny'] option:selected").val()+"&did="+$("[name='italselect'] option:selected").val()+"&fogy="+$("[name='fogy']").val(),
		type: 'GET',
		async: true,
		success: function(msg) {
			window.location.replace("index.php?page=29");
		},
		error: function() {
			alert("valami hiba történt!");
		}
	});
	
}

function jutalekment() {
	
	$.ajax({
		url: "ajax.php?page=30&action=jutalekment&pid=" +$("#pubid").val()+ "&jutalek=" +$("[name='jutalek']").val(),
		type: 'GET',
		async: true,
		success: function(msg) {
			window.location.replace("index.php?page=29");
		},
		error: function() {
			alert("valami hiba történt!");
		}
	});
	
}

function nyeremenytorol(id) {
	
	$.ajax({
		url: "ajax.php?page=30&action=nyeremenytorol&id=" +id,
		type: 'GET',
		async: true,
		success: function(msg) {
			window.location.replace("index.php?page=29");
		},
		error: function() {
			alert("valami hiba történt!");
		}
	});
	
}