function normalize(pos_img, img, body) {
	var scale = body / img;
	var pos = pos_img * scale;
	return Math.ceil(pos);
}

function showAlert(type, content, time) {
	document.getElementById("alert").style.display = "";
	
	if (type == "success" || type == "Success" || type == "SUCCESS") {
		document.getElementById("alert_icon").src = "./img/success.png";
		document.getElementById("alert").classList.remove("alert-error");
		document.getElementById("alert").classList.add("alert-success");		
		document.getElementById("alert").classList.add("action");
		document.getElementById("alert").classList.remove("hide");
		document.getElementById("alert-header").innerHTML = "";
		document.getElementById("alert-text").innerText = content;
		setTimeout(() => {
			document.getElementById("alert").classList.remove("action");
			document.getElementById("alert").classList.add("hide");
			document.getElementById("alert").style.display = 'none';
		}, time);
	} else {
		document.getElementById("alert_icon").src = "./img/danger.png";
		document.getElementById("alert").classList.remove("alert-success");
		document.getElementById("alert").classList.add("alert-error");
		document.getElementById("alert").classList.add("action");
		document.getElementById("alert").classList.remove("hide");
		document.getElementById("alert-header").innerHTML = "";
		document.getElementById("alert-text").innerText = content;
		setTimeout(() => {
			document.getElementById("alert").classList.remove("action");
			document.getElementById("alert").classList.add("hide");
			document.getElementById("alert").style.display = 'none';
		}, time);
	}
}

function epochToDateTime(epoch) {
    var date = new Date(epoch * 1000);
    var day = ("0" + date.getDate()).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();
    var hours = ("0" + date.getHours()).slice(-2);
    var minutes = ("0" + date.getMinutes()).slice(-2);
    var seconds = ("0" + date.getSeconds()).slice(-2);

    return day + "/" + month + "/" + year + " " + hours + ":" + minutes + ":" + seconds;
}