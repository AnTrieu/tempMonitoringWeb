<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" href="#">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="./css/user_page.css?v=1.0.4">

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="./js/jquery.min.js"></script>
<script src="./js/common.js?v1.0.1"></script>

<script>
let leader = "";
let typeUser = -1;
let maxRetries = 3;
let retryCount = 0;
let timeoutLocation = -1;

function deleteUser(user)
{
    document.getElementById("header_popup_confirm").innerText = 'XÓA TÀI KHOẢN';
    document.getElementById("content_popup_confirm").textContent = 'Xóa vĩnh viển tài khoản ?'
	document.getElementById("popup_confirm").style.visibility = "visible";
    document.getElementById("content_popup_confirm").name = user;
}

function resetUser(user)
{
    document.getElementById("header_popup_confirm").innerText = 'THIẾT LẬP TÀI KHOẢN';
    document.getElementById("content_popup_confirm").textContent = 'Đặt lại mật khẩu ?'
	document.getElementById("popup_confirm").style.visibility = "visible";
    document.getElementById("content_popup_confirm").name = user;
}

function selectLocation(user)
{
    // Active loader
    document.getElementById("wait_div").style.visibility = 'visible'; 

    var obj = new Object();
    obj.type = 'Request-Location';
    obj.leader = leader;
    obj.user = user;

    window.parent.postMessage(JSON.stringify(obj), '*');  

    clearTimeout(timeoutLocation);
    timeoutLocation = setTimeout(function() { 
        if (document.getElementById("wait_div").style.visibility == 'visible')
        {
            // Deactive loader
            document.getElementById("wait_div").style.visibility = 'hidden'; 

            showAlert('danger', 'Lỗi tải dữ liệu', 5000);  
        }                     
    }, 5000);
}

function showUser(page)
{
    // Refesh table dataTable
    var table = document.getElementById("table_user");
    for (var i = table.rows.length - 1; i >= 0; i--) {
        table.deleteRow(i);
    }
    
    // Active loader
    document.getElementById("wait_div").style.visibility = 'visible';  

    $.ajax({
        type: "GET",
        url: "./php/request_all_user.php",
        data: {},
        timeout: 3000,
        success: function(data_response) {

            if (data_response.length > 0) {
                var obj = JSON.parse(data_response);
                var table = document.getElementById("table_user");
                var counter = 0;
                var counterShow = 0;
                var counter_2 = 0;

                // Find new user
                if (page <= 0)
                {
                    for (var i = 0; i < Object.keys(obj).length; i++) 
                    {
                        if(obj[i].tags.length == 1)
                        {
                            tagsMaster = obj[i].tags[0];
                        }
                        else
                        {
                            tagsMaster = obj[i].tags;
                        }    
                        
                        var parts = tagsMaster.split("_");
                        if(parts.length == 3)
                        {
                            counter_2++;
                        }
                    }

                    page = Math.ceil(counter_2 / 20)
                }

                if (page <= 0)
                    return;

                // Add user                    
                for (var i = 0; i < Object.keys(obj).length; i++) 
                {
                    if(obj[i].tags.length == 1)
                    {
                        tagsMaster = obj[i].tags[0];
                    }
                    else
                    {
                        tagsMaster = obj[i].tags;
                    }   
                    
                    var parts = tagsMaster.split("_");                    
                    if (((typeUser == 4) && (parts.length == 3)) ||  ((typeUser == 1) && (parts.length == 4)))
                    {
                        counter++;
                        if ((((page - 1) * 20) < counter) && (counter <= (page * 20)))
                        {
                            var new_row = table.insertRow();
                            new_row.setAttribute("id", "user_" + obj[i].name);

                            var index_cell = new_row.insertCell();
                            index_cell.setAttribute("style", "width:5%;");
                            index_cell.innerHTML = counter;

                            var name_cell = new_row.insertCell();
                            name_cell.setAttribute("style", "width:25%;");
                            name_cell.innerHTML = "<a href=\"#\">" + obj[i].name + "</a>";

                            if(parts.length == 3)
                            {
                                var belong_cell = new_row.insertCell();
                                belong_cell.setAttribute("style", "width:25%;");
                                belong_cell.innerHTML = parts[1];

                                var type_cell = new_row.insertCell();
                                type_cell.setAttribute("style", "width:15%;");
                                type_cell.innerHTML = parts[0];       
                            }
                            else
                            {
                                var belong_cell = new_row.insertCell();
                                belong_cell.setAttribute("style", "width:25%;");
                                belong_cell.innerHTML = "--";

                                var type_cell = new_row.insertCell();
                                type_cell.setAttribute("style", "width:15%;");
                                type_cell.innerHTML = parts[1];                                 
                            }

                            var status_cell = new_row.insertCell();
                            status_cell.setAttribute("style", "width:15%;");
                            status_cell.innerHTML = "<span class=\"status text-success\">&bull;</span> Kích hoạt";     

                            var feature_cell = new_row.insertCell();
                            feature_cell.setAttribute("style", "width:15%;");
                            if (typeUser == 1)
                            {
                                feature_cell.innerHTML =    "<a href=\"#\" class=\"assign\" title=\"Tọa độ\" data-toggle=\"tooltip\" onclick=\'selectLocation(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe307;</i></a>" + 
                                                            "<a href=\"#\" class=\"reload\" title=\"Reset\" data-toggle=\"tooltip\" onclick=\'resetUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe86a;</i></a>" + 
                                                            "<a href=\"#\" class=\"delete\" title=\"Xóa\" data-toggle=\"tooltip\" onclick=\'deleteUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xE5C9;</i></a>";  
                            }
                            else
                            {
                                feature_cell.innerHTML =    "<a href=\"#\" class=\"assign disable\" title=\"Tọa độ\" data-toggle=\"tooltip\" onclick=\'selectLocation(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe307;</i></a>" + 
                                                            "<a href=\"#\" class=\"reload\" title=\"Reset\" data-toggle=\"tooltip\" onclick=\'resetUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe86a;</i></a>" + 
                                                            "<a href=\"#\" class=\"delete\" title=\"Xóa\" data-toggle=\"tooltip\" onclick=\'deleteUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xE5C9;</i></a>";  
                            }

                            counterShow++;
                        }                                          
                    }
                }

                // Count
                document.getElementById("show-index").textContent = counterShow;
                document.getElementById("total-index").textContent = counter;

                // Pages menu
                var ul = document.getElementById("page_menu");

                // Remove all page
                while (ul.firstChild) {
                    ul.removeChild(ul.firstChild);
                }

                // Create a new page
                var li = document.createElement("li");
                li.classList.add("page-item");
                if (page == 1)
                    li.classList.add("disabled");
                li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (page - 1) + ")\">Previous</a>";
                ul.appendChild(li);

                for(var i = 1; i <= Math.ceil(counter / 20); i++)
                {
                    if (((page - 2) <= i) && (i <= (page + 2)))
                    {
                        // Create a new li element
                        var li = document.createElement("li");
                        li.classList.add("page-item");
                        if( i == page)
                        {
                            li.classList.add("active");
                            li.innerHTML = "<a href=\"#\" class=\"page-link\" style=\"cursor: not-allowed;\">" + i + "</a>";
                        }
                        else
                        {
                            li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + i + ")\">" + i + "</a>";
                        }
                            
                        ul.appendChild(li);  
                    }                     
                }

                // Create a new li element
                var li = document.createElement("li");
                li.classList.add("page-item");
                if( page >= Math.ceil(counter / 20))
                {
                    li.classList.add("disabled");
                }
                li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (page + 1) + ")\">Next</a>";
                ul.appendChild(li);
            }

            // Deactive loader
            document.getElementById("wait_div").style.visibility = 'hidden';
        },
        error: function(xhr, status, error) {
            if (status === 'timeout' && retryCount < maxRetries) {
                retryCount++;
                // Retry AJAX call
                showUser(1);
            } else {
                showAlert('danger', 'Gặp sự cố khi kết nối đến máy chủ', 3000);

                // Deactive loader
                document.getElementById("wait_div").style.visibility = 'hidden';                
            }
        } 
	});      
}

$(window).load(function() {
    window.addEventListener('message', function(event) {	
        if (event.data.length > 0) {	
            var obj = JSON.parse(event.data);  
            
            // Message MQTT
			if (obj.topic != null)
            {
                var msg = JSON.parse(obj.message);
                if (msg.command.localeCompare("reponse_location") == 0)
                {
                    if (document.getElementById("wait_div").style.visibility == 'visible')
                    {
                        // Deactive loader
                        document.getElementById("wait_div").style.visibility = 'hidden';                    
                        
                        document.getElementById("popup_location").style.visibility = "visible";
                        document.getElementById("popup_location").name = msg.data.user;

                        document.getElementById('TBG').checked = false;
                        document.getElementById('HDG').checked = false;
                        document.getElementById('DNG').checked = false;
                        document.getElementById('DAN').checked = false;
                        document.getElementById('MTO').checked = false;
                        document.getElementById('HLG').checked = false;
                        document.getElementById('TNN').checked = false;
                        document.getElementById('DLT').checked = false;
                        document.getElementById('NTT').checked = false;
                        document.getElementById('CTO').checked = false;

                        if(msg.data.location.length > 0)
                        {
                            for(var i = 0; i < msg.data.location[0].length; i++)
                            {
                                document.getElementById(msg.data.location[0][i]).checked = true;
                            }
                        }
                    }
                }                
            }

            // Message internal
            else
            {
                if (obj.type.localeCompare("Reload-User") == 0)
                {
                    document.getElementById('popup_confirm').style.visibility = 'hidden';
                    document.getElementById('popup-create').style.visibility = 'hidden';

                    leader = obj.leader;
                    if(typeUser > 0)
                    {
                        showUser(1);  
                    }                        
                }
                else if (obj.type.localeCompare("Update-Type-User") == 0)
                {
                    typeUser = obj.rootTypeUser;
                    showUser(1);  
                }
            }
        }
    });    
    
    window.addEventListener("click", function(e) {
        if ((e.target == document.getElementById("popup-create"))   ||
            (e.target == document.getElementById("popup_confirm"))  ||
            (e.target == document.getElementById("popup_location"))) {
            e.target.style.visibility = 'hidden';
        }				
    });    
});
</script>
</head>
<body>
<div class="alert-error hide" style="display: none;" id="alert">
	<img src="./img/danger.png" class="alert_icon" id="alert_icon">
	<strong class="alert-header" id="alert-header"></strong>
	<p class="alert-text" id="alert-text"></p>
</div>    

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-5">
                        <h2>Quản lý <b>tài khoản</b></h2>
                    </div>
                    <div class="col-sm-7">
                        <a href="#" class="btn btn-secondary" 
                        onclick="
                            if ((typeUser == 1) || (typeUser == 4))
                            {
                                document.getElementById('Username').style.border = ''; 
                                document.getElementById('Username').value = '';        
                                document.getElementById('Password').style.border = ''; 
                                document.getElementById('Password').value = '';     
                                document.getElementById('Confirm_Password').style.border = ''; 
                                document.getElementById('Confirm_Password').value = '';                                                                          
                                document.getElementById('popup-create').style.visibility = 'visible';                                
                            }
                        " ><i class="material-icons">&#xE147;</i> <span>Thêm tài khoản</span></a>		
                    </div>
                </div>
            </div>
            <div class="table-data">
                <table class="table table-striped table-hover" style="margin-bottom:0px;">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:25%;">Tên đăng nhập</th>						
                            <th style="width:25%;">Người tạo</th>
                            <th style="width:15%;">Loại</th>
                            <th style="width:15%;">Trạng tái</th>
                            <th style="width:15%;">Tác vụ</th>
                        </tr>
                    </thead>
                </table>
                <div class="table-data-2">
                    <table class="table table-striped table-hover" id="table_user">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div id="wait_div" class="wait_div">
                <div class="loader-outside">
                    <div class="loader"></div>
                </div>
            </div>	
        </div>      
    </div>
    <div>
        <div class="hint-text">Hiển thị <b id="show-index">0</b> trong tổng số <b id="total-index">0</b> tài khoản</div>
        <ul class="pagination" id="page_menu"></ul>
    </div>  
</div>     

<div class="popup-create" id="popup-create">
	<div class="form-create" id="form-create">
		<div class="div-title">
			<label class="title">
				TẠO TÀI KHOẢN
			</label>
		</div>
		<form action="#">
			<div>
				<label>Tên đăng nhập (<span style="color: red;">*</span>): </label>
                <br>
				<input type="text" value="" placeholder="Nhập Username" id="Username" onfocus="this.style.border = ''">
			</div>
			<div>
				<label>Mật khẩu (<span style="color: red;">*</span>): </label>
                <br>
				<input type="password" value="" placeholder="Nhập Password" id="Password" onfocus="this.style.border = ''">
			</div>
			<div>
				<label>Nhập lại mật khẩu (<span style="color: red;">*</span>): </label>
                <br>
				<input type="password" value="" placeholder="Nhập Password" id="Confirm_Password" onfocus="this.style.border = ''">
			</div>
		</form>
		<div class="btn_confirm_create">
			<button class="btn-cancel" onclick="
                document.getElementById('popup-create').style.visibility = 'hidden';
            ">Hủy bỏ</button>
			<button class="btn-confirm" onclick="
                var flagError = false;
                if (document.getElementById('Username').value.length == 0)
                {
                    document.getElementById('Username').style.border = '2px solid red';
                    flagError = true;
                }
                if (document.getElementById('Password').value.length == 0)
                {
                    document.getElementById('Password').style.border = '2px solid red';
                    flagError = true;
                }
                if (document.getElementById('Confirm_Password').value.length == 0)
                {
                    document.getElementById('Confirm_Password').style.border = '2px solid red';
                    flagError = true;
                }
                if ((document.getElementById('Username').value.indexOf(' ') >= 0) || 
                    (document.getElementById('Username').value.indexOf('.') >= 0) ||
                    (document.getElementById('Username').value.indexOf('_') >= 0))
                {
                    showAlert('danger', 'Chứa kí tự đặc biệt', 3000);
                    flagError = true;
                }

                if(flagError)
                    return;
                else
                {
                    if (document.getElementById('Password').value.localeCompare(document.getElementById('Confirm_Password').value) != 0)
                    {
                        showAlert('danger', 'Dữ liệu không khớp', 3000);
                    }
                    else
                    {
                        if ((typeUser == 1) || (typeUser == 4))
                        {
                            $.ajax({
                                type: 'POST',
                                url: './php/create_user.php',
                                data: {
                                    'user_input': document.getElementById('Username').value,
                                    'pass_input': document.getElementById('Password').value,
                                    'date_input': null,
                                    'type_input': typeUser == 4 ? 'Member' : 'Leader',
                                    'size_input': typeUser == 4 ? null : 1024,
                                    'leader_input': leader,
                                    'permission': 0
                                },
                                timeout: 3000,
                                success: function(data_response) {
                                    if (data_response.localeCompare('ok') == 0) {

                                        showUser(1);
                                        showAlert('success', 'Tạo tài khoản thành công', 3000);
                                    } else if (data_response.localeCompare('error exist') == 0) {
                                        showAlert('danger', 'Tài khoản đã tồn tại', 3000);
                                    } else {
                                        showAlert('danger', 'Tạo tài khoản thất bại', 3000);
                                    }                            
                                },
                                error: function(xhr, status, error) {
                                    console.log(status)
                                    if (status === 'timeout' && retryCount < maxRetries) {
                                        retryCount++;
                                        // Retry AJAX call
                                        createUser();
                                    } else {
                                        showAlert('danger', 'Gặp sự cố khi kết nối đến máy chủ', 3000);
                                    }
                                }                            
                            });	                               
                        }     
                        else
                        {
                            showAlert('danger', 'Không có quyền thiết lập', 3000);
                        }               
                    }	                    
                }

                document.getElementById('popup-create').style.visibility = 'hidden';
            ">Xác nhận</button>
		</div>
	</div>
</div>

<div class="popup_confirm" id="popup_confirm">
	<div class="popup_confirm_background">
		<div class="header_popup_confirm">
			<p id="header_popup_confirm"></p>
		</div>
		<div class="content_popup_confirm">
			<p id="content_popup_confirm"></p>
		</div>
		<div class="btn_new_folder">
			<button class="btn_cancel" onclick="document.getElementById('popup_confirm').style.visibility = 'hidden';">Hủy bỏ</button>
			<button class="btn_confirm" onclick="
                document.getElementById('popup_confirm').style.visibility = 'hidden';
                showAlert('danger', 'Tính năng bị giới hạn ', 3000);
                return;
                
                $.ajax({
                    type: 'GET',
                    url: './php/request_all_user.php',
                    data: {},
                    timeout: 3000,
                    success: function(data_response) {
                        if (data_response.length > 0) {
                            var obj = JSON.parse(data_response);
                            var objArray = [document.getElementById('content_popup_confirm').name];

                            for (var i = 0; i < Object.keys(obj).length; i++) 
                            {
                                if(obj[i].tags.length == 1)
                                {
                                    tagsMaster = obj[i].tags[0];
                                }
                                else
                                {
                                    tagsMaster = obj[i].tags;
                                }   

                                var parts = tagsMaster.split('_');                    
                                if ((parts.length == 3) && (parts[1].localeCompare(document.getElementById('content_popup_confirm').name) == 0))
                                {
                                    objArray.push(obj[i].name);
                                }
                            }  
                            
                            if (objArray.length > 0) {
                                // Delete user
                                $.ajax({
                                    type: 'POST',
                                    url: './php/delete_user.php',
                                    data: {
                                        'users': objArray
                                    },
                                    success: function(result) {
                                    
                                        if (result.localeCompare('ok') != 0) {
                                            showAlert('danger', 'Xóa tài khoản thất bại', 3000);
                                        }
                                        else
                                        {
                                            showUser(1);
                                            showAlert('success', 'Xóa tài khoản thành công', 3000);
                                        }
                                    }
                                });                  
                            }
                        }            
                    },
                    error: function(xhr, status, error) {
                        if (status === 'timeout' && retryCount < maxRetries) {
                            retryCount++;
                            // Retry AJAX call
                            showUser(1);
                        } else {
                            showAlert('danger', 'Gặp sự cố khi kết nối đến máy chủ', 3000);               
                        }
                    } 
                });                   
            ">Xác nhận</button>
		</div>
	</div>
</div>

<div class="popup-program-list prevent-select" id="popup_location">
	<div class="popup-program-content" >
		<div class="program-list-table">
			<div class="header-program-table">
				<p>DANH SÁCH TRUNG TÂM GO!</p>
			</div>
            <div class="container">
                <div class="column">
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="TBG"></div>
                        <div><label for="checkbox1">GO! Thái Bình</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="HDG"></div>
                        <div><label for="checkbox2">GO! Hải Dương</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="DNG"></div>
                        <div><label for="checkbox3">GO! Đà Nẵng</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="DAN"></div>
                        <div><label for="checkbox4">GO! Dĩ An</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="MTO"></div>
                        <div><label for="checkbox5">GO! Mỹ Tho</label></div>
                    </div>
                </div>
                <div class="column">
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="HLG"></div>
                        <div><label for="checkbox6">GO! Hạ Long</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="TNN"></div>
                        <div><label for="checkbox7">GO! Thái Nguyên</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="DLT"></div>
                        <div><label for="checkbox8">GO! Đà Lạt</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="NTT"></div>
                        <div><label for="checkbox9">GO! Nguyễn Thị Thập</label></div>
                    </div>
                    <div class="checkbox-item">
                        <div><input type="checkbox" id="CTO"></div>
                        <div><label for="checkbox10">GO! Cần Thơ</label></div>
                    </div>
                </div>
            </div>
			<div class="btn_program_list">
                <button class="btn-cancel" onclick="document.getElementById('popup_location').style.visibility = 'hidden';">Hủy bỏ</button>
				<button class="btn-confirm" onclick="
                    var locations = [];
                    
                    if (document.getElementById('TBG').checked)
                        locations.push('TBG');
                    if (document.getElementById('HDG').checked)
                        locations.push('HDG');
                    if (document.getElementById('DNG').checked)
                        locations.push('DNG');                        
                    if (document.getElementById('DAN').checked)
                        locations.push('DAN');
                    if (document.getElementById('MTO').checked)
                        locations.push('MTO');
                    if (document.getElementById('HLG').checked)
                        locations.push('HLG');
                    if (document.getElementById('TNN').checked)
                        locations.push('TNN');
                    if (document.getElementById('DLT').checked)
                        locations.push('DLT');                        
                    if (document.getElementById('NTT').checked)
                        locations.push('NTT');
                    if (document.getElementById('CTO').checked)
                        locations.push('CTO');

                    var obj = new Object();
                    obj.type = 'Set-Locations';
                    obj.user = document.getElementById('popup_location').name;
                    obj.leader = leader;
                    obj.locations = locations;

                    window.parent.postMessage(JSON.stringify(obj), '*');  
                    
                    document.getElementById('popup_location').style.visibility = 'hidden';
                ">Xác nhận</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>