$(function() {
	//pubID-k kinyerése
	var json = $('#pubjson').val();
	var pubs = JSON.parse(json);
	var pubCount = 0;
	
	$('#kapcsoloSelect').change(function(){
		pubCount = 0;
		var userid = $('#kapcsoloSelect option:selected').val();
		$('#kapcsolotable').html('');
		for(var i=0;i<pubs.length;i++) {
			var temp = i;
			//alert(temp);
			$('#kapcsolotable').append('<div class="pubrow"><span class="pubname" pub="'+pubs[i].ID+'">'+pubs[i].name+'&ensp;</span><span class="operation"></span></div>');
			$.ajax({
				type: 'POST',
				url: 'ajax.php?page=8',
				data: {user: userid, pub: pubs[i].ID},
				async: false,
				success: function(data){
					if(data == 'van') {
						$('.pubname :last').next().html('&ensp;<button class="kapcsaction" action="remove" uid="'+userid+'" pid="'+pubs[i].ID+'">Eltávolítás</button>');
						pubCount++;
					}
					if(data == 'nincs') $('.pubname :last').next().html('&ensp;<button class="kapcsaction" action="add" uid="'+userid+'" pid="'+pubs[i].ID+'">Hozzáadás</button>');
				}
			});
		}
	});
	
	$(document).on('click', '.kapcsaction', function() {
		if($(this).attr('action') == 'remove' && pubCount < 2) alert('A felhasználó csak ehhez az egy kocsmához van rendelve!');
		else {
			var uid = $(this).attr('uid');
			var pid = $(this).attr('pid');
			var action = $(this).attr('action');
			var button = $(this);
			$.ajax({
				type: 'POST',
				url: 'ajax.php?page=9',
				data: {uid: uid, pid: pid, action: action},
				async: false,
				success: function(data) {
					if(data == 'success') {
						if(action == 'add') {
							pubCount++;
							$(button).attr('action', 'remove');
							$(button).text('Eltávolítás');
						}
						if(action == 'remove') {
							pubCount--;
							$(button).attr('action', 'add');
							$(button).text('Hozzáadás');
						}
					}
				}
			});
		}
	})
});