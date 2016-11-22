$(document).ready(function(){
	$("#submit").click(function(){
		$.post("services/cv.php", {
            "perintah":"login",
            "user": $("#username").val(),
            "password": $("#password").val()
        } , function (data) {
            var result = JSON.parse(data);
            if(result.status == "gagal"){
                alert("username atau password anda salah");
                return;
            } else {
                var userInfo = {
                    "username": result.npm,
                    "nama": result.nama,
                    "npm": result.npm
                };
                sessionStorage.setItem("username", result.npm);
                sessionStorage.setItem("user", JSON.stringify(result));
                window.location.href = "home.html";
            }
        });
	});
});

function check() {
	if(sessionStorage.user){
		var user = JSON.parse(sessionStorage.getItem("user"));
		document.getElementById("nama").innerHTML = user.nama;
	} else {
		window.location.href = "index.html";
	}
}