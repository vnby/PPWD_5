$(document).ready(function() {
	if(sessionStorage.getItem("username") == null) {
		alert("Sialhkan login terlebih dahulu :)");
		window.location.href = "index.html";
	}

	$("#logout").click(function() {
		sessionStorage.removeItem("username");
	});

	$("#update").click(function() {
		window.location.href = "update.html";
	});

	$("#selesaiUpdate").click(function() {
		validasi();
	});

	function validasi() {
		var nama = document.getElementById("icon_prefix").value;
		var alamat = document.getElementById("icon_home").value;

		if(nama.length == 0) {
			alert("Isi nama terlebih dahulu");
		}
		if(alamat.length == 0) {
			alert("Isi alamat terlebih dahulu");
		}
		else {
			window.location.href = "home.html";
		}
	}

	$("#cari").click(function(){
		$.post("services/cv.php", {
			"perintah":"cari",
			"nama": $("#npmQuery").val()
		} , function (data) {
			var result = JSON.parse(data);

			var userInfo = {
				"username": result.npm,
				"nama": result.nama,
				"npm": result.npm
			};

			var listuser = result.users;

			var print = "<ul>";
			for (var i = 0; i < listuser.length; i++){
				var obj = listuser[i];
				print += "<li>";
				for (var key in obj){
					var value = obj[key];
					print += value + " ";
				}
				print += "<input type='button' value='Connect'></li>";
			}

			$("#searchresult").html(print);
		});

 });
});