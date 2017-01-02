
//set ie
var bro = "other";
if (navigator.userAgent.toLowerCase().indexOf('msie') > -1) {
	bro = "msie";
}

$.ajax({
	url: "ajax.php?page=4",
	type: 'GET',
	async: false,
	success: function(msg) {
		window.location = "index.php?page=main&bro="+bro;
	}
});