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
<link rel="stylesheet" type="text/css" href="./css/user_page.css?v=1.0.5">

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

function opentab(evt) {						
    var i, tablinks;

    if(evt.currentTarget.classList.contains("active"))
        return;

    // Remove active class for all element
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Active button
    evt.currentTarget.className += " active";

    showUser(1, evt.currentTarget.attributes[1].value);
}

function deleteUser(key, type)
{
	if (type.localeCompare("Account") == 0)
	{
		document.getElementById("header_popup_confirm").innerText = 'REMOVE THE ACCOUNT';
		document.getElementById("content_popup_confirm").textContent = 'Delete the account permanently ?'
		document.getElementById("popup_confirm").style.visibility = "visible";		
	}
	else
	{
		document.getElementById("header_popup_confirm").innerText = 'REMOVE PHONE';
		document.getElementById("content_popup_confirm").textContent = 'Delete the account permanently ?'
		document.getElementById("popup_confirm").style.visibility = "visible";
	}
	
	document.getElementById("content_popup_confirm").name = key;
}

function resetUser(user)
{
    document.getElementById("header_popup_confirm").innerText = 'ACCOUNT SETTINGS';
    document.getElementById("content_popup_confirm").textContent = 'Would you like reset your password ?'
	document.getElementById("popup_confirm").style.visibility = "visible";
    document.getElementById("content_popup_confirm").name = user;
}

function selectLocation(key, type)
{
	// Send request
    var obj             = new Object();
    obj.type 			= 'Request-Location';
    obj.leader 			= leader;
    obj.user 			= key;
	obj.location_for 	= type;

    window.parent.postMessage(JSON.stringify(obj), '*'); 
	
    // Active loader
    document.getElementById("wait_div").style.visibility = 'visible'; 

    clearTimeout(timeoutLocation);
    timeoutLocation = setTimeout(function() { 
        if (document.getElementById("wait_div").style.visibility == 'visible')
        {
            // Deactive loader
            document.getElementById("wait_div").style.visibility = 'hidden'; 

            showAlert('danger', 'Data loading error', 5000);  
        }                     
    }, 3000);
}

function showUser(page, type)
{
    // Refesh table dataTable
    var table = document.getElementById("table_user");
    for (var i = table.rows.length - 1; i >= 0; i--) {
        table.deleteRow(i);
    }

    // Remove active class for all element
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
        if(tablinks[i].attributes[1].value.localeCompare(type) == 0)
        {
            // Active Acount button 
            tablinks[i].className += " active";            
        }
    }

    // Active loader
    document.getElementById("wait_div").style.visibility = 'visible';  

    if (type.localeCompare("Account") == 0)
    {
        // Update label
        document.getElementById("info_header").textContent = "Accounts";
        document.getElementById("info_key").textContent = "User";

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
                        if (((leader.localeCompare(parts[1]) == 0) && (typeUser == 4) && (parts.length == 3)) ||  ((typeUser == 1) && (parts.length == 4)))
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
                                name_cell.innerHTML = "<a href=\"#\" style=\"cursor: default;\">" + obj[i].name + "</a>";

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
                                    feature_cell.innerHTML =    "<a href=\"#\" class=\"assign\" title=\"Location\" data-toggle=\"tooltip\" onclick=\'selectLocation(\"" + obj[i].name + "\", \"Account\")\'><i class=\"material-icons\">&#xe307;</i></a>" + 
                                                                "<a href=\"#\" class=\"reload\" title=\"Reset\" data-toggle=\"tooltip\" onclick=\'resetUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe86a;</i></a>" + 
                                                                "<a href=\"#\" class=\"delete\" title=\"Delete\" data-toggle=\"tooltip\" onclick=\'deleteUser(\"" + obj[i].name + "\", \"Account\")\'><i class=\"material-icons\">&#xE5C9;</i></a>";  
                                }
                                else
                                {
                                    feature_cell.innerHTML =    "<a href=\"#\" class=\"assign disable\" title=\"Location\" data-toggle=\"tooltip\" onclick=\'selectLocation(\"" + obj[i].name + "\", \"Account\")\'><i class=\"material-icons\">&#xe307;</i></a>" + 
                                                                "<a href=\"#\" class=\"reload\" title=\"Reset\" data-toggle=\"tooltip\" onclick=\'resetUser(\"" + obj[i].name + "\")\'><i class=\"material-icons\">&#xe86a;</i></a>" + 
                                                                "<a href=\"#\" class=\"delete\" title=\"Delete\" data-toggle=\"tooltip\" onclick=\'deleteUser(\"" + obj[i].name + "\", \"Account\")\'><i class=\"material-icons\">&#xE5C9;</i></a>";  
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
                    li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (page - 1) + ", 'Account')\">Previous</a>";
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
                                li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + i + ", 'Account')\">" + i + "</a>";
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
                    li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (page + 1) + ", 'Account')\">Next</a>";
                    ul.appendChild(li);
                }

                // Deactive loader
                document.getElementById("wait_div").style.visibility = 'hidden';
            },
            error: function(xhr, status, error) {
                if (status === 'timeout' && retryCount < maxRetries) {
                    retryCount++;
                    // Retry AJAX call
                    showUser(1, 'Account');
                } else {
                    showAlert('danger', 'Connecting to the server is not working properly', 3000);

                    // Deactive loader
                    document.getElementById("wait_div").style.visibility = 'hidden';                
                }
            } 
        });    
    }
    else if (type.localeCompare("Sms") == 0)
    {
        // Update label
        document.getElementById("info_header").textContent = "Phone Number";
        document.getElementById("info_key").textContent = "Number";

        var obj = new Object(); 
        obj.type        = 'Request-Numbers';
        obj.leader      = leader;
        obj.page        = page;

        window.parent.postMessage(JSON.stringify(obj), '*');  

        clearTimeout(timeoutLocation);
        timeoutLocation = setTimeout(function() { 
            if (document.getElementById("wait_div").style.visibility == 'visible')
            {
                // Deactive loader
                document.getElementById("wait_div").style.visibility = 'hidden'; 

                // Count
                document.getElementById("show-index").textContent = 0;
                document.getElementById("total-index").textContent = 0;

                // Pages menu
                var ul = document.getElementById("page_menu");

                // Remove all page
                while (ul.firstChild) {
                    ul.removeChild(ul.firstChild);
                }

                // Create a new page
                var li = document.createElement("li");
                li.classList.add("page-item");
                li.classList.add("disabled");
                li.innerHTML = "<a href=\"#\" class=\"page-link\">Previous</a>";
                ul.appendChild(li);

                // Create a new li element
                var li = document.createElement("li");
                li.classList.add("page-item");
                li.classList.add("active");
                li.innerHTML = "<a href=\"#\" class=\"page-link\" style=\"cursor: not-allowed;\">1</a>";                   
                ul.appendChild(li);  

                // Create a new li element
                var li = document.createElement("li");
                li.classList.add("page-item");
                li.classList.add("disabled");
                li.innerHTML = "<a href=\"#\" class=\"page-link\">Next</a>";
                ul.appendChild(li);

                showAlert('danger', 'Data loading error', 5000);  
            }                     
        }, 3000);
    }
}

$(window).load(function() {
    window.addEventListener('message', function(event) {	
        if (event.data.length > 0) {	
            var obj = JSON.parse(event.data);  
            
            // Message MQTT
			if (obj.topic != null)
            {
                var msg = JSON.parse(obj.message);
                if ((msg.command.localeCompare("reponse_location") == 0) && (typeUser > 0))
                {
                    if (((msg.data.location_for.localeCompare("Account") == 0) && (document.getElementById("wait_div").style.visibility == 'visible') && (document.getElementsByClassName("tablinks")[0].classList.contains("active"))) ||
                        ((msg.data.location_for.localeCompare("Sms") == 0) && (document.getElementById("wait_div").style.visibility == 'visible') && (document.getElementsByClassName("tablinks")[1].classList.contains("active"))))
                    {
                        // Deactive loader
                        document.getElementById("wait_div").style.visibility = 'hidden';                    
                        
                        document.getElementById("popup_location").style.visibility = "visible";
                        document.getElementById("popup_location").name = msg.data.user + "_" + msg.data.location_for;

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
                                if(msg.data.location[0][i].length > 0)
                                    document.getElementById(msg.data.location[0][i]).checked = true;
                            }
                        }
                    }
                }    
                else if ((msg.command.localeCompare("reponse_numbers") == 0) && (document.getElementById("wait_div").style.visibility == 'visible') && (document.getElementsByClassName("tablinks")[1].classList.contains("active")))
                {
					var counter = 0;
					
					// Refesh table dataTable
					var table = document.getElementById('table_user');
					for (var i = table.rows.length - 1; i >= 0; i--) {
						table.deleteRow(i);
					}
				
                    // Deactive loader
                    document.getElementById("wait_div").style.visibility = 'hidden';                    

                    for(var i = 0; i < msg.data.filter.length; i++)
                    {
                        if (((leader.localeCompare(msg.data.filter[i][1]) == 0) || (typeUser == 1)))
                        {
                            // Counter list
                            counter++;

                            var new_row = table.insertRow();
                            new_row.setAttribute("id", "user_" + msg.data.filter[i][0]);

                            var index_cell = new_row.insertCell();
                            index_cell.setAttribute("style", "width:5%;");
                            index_cell.innerHTML = (msg.page - 1) * 20 + counter;

                            var name_cell = new_row.insertCell();
                            name_cell.setAttribute("style", "width:25%;");
                            name_cell.innerHTML = "<a href=\"#\" style=\"cursor: default;\">" + msg.data.filter[i][0] + "</a>";

                            var belong_cell = new_row.insertCell();
                            belong_cell.setAttribute("style", "width:25%;");
                            belong_cell.innerHTML = msg.data.filter[i][1];

                            var type_cell = new_row.insertCell();
                            type_cell.setAttribute("style", "width:15%;");
                            type_cell.innerHTML = "Phone";  
                            
                            var status_cell = new_row.insertCell();
                            status_cell.setAttribute("style", "width:15%;");
                            status_cell.innerHTML = "<span class=\"status text-success\">&bull;</span> Kích hoạt";     

                            var feature_cell = new_row.insertCell();
                            feature_cell.setAttribute("style", "width:15%;");    
                            feature_cell.innerHTML =    "<a href=\"#\" class=\"assign\" title=\"Location\" data-toggle=\"tooltip\" onclick=\'selectLocation(\"" + msg.data.filter[i][0] + "\", \"Sms\")\'><i class=\"material-icons\">&#xe307;</i></a>" +
                                                        "<a href=\"#\" class=\"delete\" title=\"Delete\" data-toggle=\"tooltip\" onclick=\'deleteUser(\"" + msg.data.filter[i][0] + "\", \"Sms\")\'><i class=\"material-icons\">&#xE5C9;</i></a>";                              
                        }   
                        else if (msg.length > 0)
                        {
                            msg.length -= 1;
                        }                                         
                    }   
                    
                    // Count
                    document.getElementById("show-index").textContent = counter;
                    document.getElementById("total-index").textContent = msg.length;

                    // Pages menu
                    var ul = document.getElementById("page_menu");

                    // Remove all page
                    while (ul.firstChild) {
                        ul.removeChild(ul.firstChild);
                    }

                    // Create a new page
                    var li = document.createElement("li");
                    li.classList.add("page-item");
                    if (msg.page == 1)
                        li.classList.add("disabled");
                    li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (msg.page - 1) + ", 'Sms')\">Previous</a>";
                    ul.appendChild(li);

                    for(var i = 1; i <= Math.ceil(msg.length / 20); i++)
                    {
                        if (((msg.page - 2) <= i) && (i <= (msg.page + 2)))
                        {
                            // Create a new li element
                            var li = document.createElement("li");
                            li.classList.add("page-item");
                            if( i == msg.page)
                            {
                                li.classList.add("active");
                                li.innerHTML = "<a href=\"#\" class=\"page-link\" style=\"cursor: not-allowed;\">" + i + "</a>";
                            }
                            else
                            {
                                li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + i + ", 'Sms')\">" + i + "</a>";
                            }
                                
                            ul.appendChild(li);  
                        }                     
                    }

                    // Create a new li element
                    var li = document.createElement("li");
                    li.classList.add("page-item");
                    if( msg.page >= Math.ceil(msg.length / 20))
                    {
                        li.classList.add("disabled");
                    }
                    li.innerHTML = "<a href=\"#\" class=\"page-link\" onclick=\"showUser(" + (msg.page + 1) + ", 'Sms')\">Next</a>";
                    ul.appendChild(li);  

					if (msg.data.owner.length > 0)
					{
						showAlert('danger', 'Phone number has been included by \"' + msg.data.owner + "\"", 5000);  
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
                        showUser(1, 'Account');
                    }                        
                }
                else if (obj.type.localeCompare("Update-Type-User") == 0)
                {
                    if(typeUser != obj.rootTypeUser)
                    {
                        typeUser = obj.rootTypeUser;
                        showUser(1, 'Account'); 
                    }                   
                }
            }
        }
    });    
    
    window.addEventListener("click", function(e) {
        if ((e.target == document.getElementById("popup-create"))       ||
            (e.target == document.getElementById("popup-create-phone")) ||
            (e.target == document.getElementById("popup_confirm"))      ||
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
            <div class="tab">
				<button class="tablinks active" name="Account" onclick="opentab(event)">Account</button>
                <button class="tablinks" name="Sms" onclick="opentab(event)">SMS</button>
            </div>            
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-5">
                        <h2>Manage <b id="info_header">Accounts</b></h2>
                    </div>
                    <div class="col-sm-7">
                        <a href="#" class="btn btn-secondary" 
                        onclick="
                            if ((typeUser == 1) || (typeUser == 4))
                            {
                                if (document.getElementsByClassName('tablinks')[0].classList.contains('active'))
                                {
                                    document.getElementById('Username').style.border = ''; 
                                    document.getElementById('Username').value = '';        
                                    document.getElementById('Password').style.border = ''; 
                                    document.getElementById('Password').value = '';     
                                    document.getElementById('Confirm_Password').style.border = ''; 
                                    document.getElementById('Confirm_Password').value = '';                                                                          
                                    document.getElementById('popup-create').style.visibility = 'visible';                                      
                                } 
                                else if (document.getElementsByClassName('tablinks')[1].classList.contains('active'))
                                {
                                    document.getElementById('Phone_Number').style.border = ''; 
                                    document.getElementById('Phone_Number').value = '';                                     
                                    document.getElementById('popup-create-phone').style.visibility = 'visible';
                                }                             
                            }
                            else
                            {
                                showAlert('danger', 'No permissions are configured', 3000);   
                            }                             
                        " ><i class="material-icons">&#xE147;</i> <span>New</span></a>		
                    </div>
                </div>
            </div>
            <div class="table-data">
                <table class="table table-striped table-hover" style="margin-bottom:0px;">
                    <thead>
                        <tr>
                            <th style="width:5%;">#</th>
                            <th style="width:25%;" id="info_key">User</th>						
                            <th style="width:25%;">Creator</th>
                            <th style="width:15%;">Type</th>
                            <th style="width:15%;">Status</th>
                            <th style="width:15%;">Feature</th>
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
        <div class="hint-text">Shows <b id="show-index">0</b> in total <b id="total-index">0</b> account</div>
        <ul class="pagination" id="page_menu"></ul>
    </div>  
</div>     

<div class="popup-create" id="popup-create">
	<div class="form-create">
		<div class="div-title">
			<label class="title">
                NEW ACCOUNT
			</label>
		</div>
		<form action="#">
			<div>
				<label>User (<span style="color: red;">*</span>): </label>
                <br>
				<input type="text" value="" placeholder="Enter Username" id="Username" onfocus="this.style.border = ''">
			</div>
			<div>
				<label>Password (<span style="color: red;">*</span>): </label>
                <br>
				<input type="password" value="" placeholder="Enter Password" id="Password" onfocus="this.style.border = ''">
			</div>
			<div>
				<label>Re-Password (<span style="color: red;">*</span>): </label>
                <br>
				<input type="password" value="" placeholder="Enter Re-Password" id="Confirm_Password" onfocus="this.style.border = ''">
			</div>
		</form>
		<div class="btn_confirm_create">
			<button class="btn-cancel" onclick="
                document.getElementById('popup-create').style.visibility = 'hidden';
            ">Cancel</button>
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
                    showAlert('danger', 'Contains special characters', 3000);
                    flagError = true;
                }
                if ((document.getElementById('Username').value.localeCompare('Device') == 0) ||
                    (document.getElementById('Username').value.localeCompare('Gateway') == 0) ||
                    (document.getElementById('Username').value.localeCompare('OTPServer') == 0) ||
                    (document.getElementById('Username').value.localeCompare('port_get_info_user') == 0))
                {
                    showAlert('danger', 'Account is already set up', 3000);
                    flagError = true;                    
                }

                if(flagError)
                    return;
                else
                {
                    if (document.getElementById('Password').value.localeCompare(document.getElementById('Confirm_Password').value) != 0)
                    {
                        showAlert('danger', 'Information is inconsistent', 3000);
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

                                        showUser(1, 'Account');
                                        showAlert('success', 'Successful creation of an account', 3000);
                                    } else if (data_response.localeCompare('error exist') == 0) {
                                        showAlert('danger', 'Account is already set up', 3000);
                                    } else {
                                        showAlert('danger', 'Failure to create an account', 3000);
                                    }                            
                                },
                                error: function(xhr, status, error) {
                                    console.log(status)
                                    if (status === 'timeout' && retryCount < maxRetries) {
                                        retryCount++;
                                        // Retry AJAX call
                                        createUser();
                                    } else {
                                        showAlert('danger', 'Accessing the server is not working properly', 3000);
                                    }
                                }                            
                            });	                               
                        }     
                        else
                        {
                            showAlert('danger', 'No permissions are configured', 3000);
                        }               
                    }	                    
                }

                document.getElementById('popup-create').style.visibility = 'hidden';
            ">Confirm</button>
		</div>
	</div>
</div>

<div class="popup-create" id="popup-create-phone">
	<div class="form-create">
		<div class="div-title">
			<label class="title">
                NEW PHONE NUMBER 
			</label>
		</div>
		<form action="#">
			<div>
				<label>Phone number(<span style="color: red;">*</span>): </label>
                <br>
				<input type="text" value="" placeholder="Enter Phone Number" id="Phone_Number" onfocus="this.style.border = ''" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
			</div>
		</form>
		<div class="btn_confirm_create">
			<button class="btn-cancel" onclick="
                document.getElementById('popup-create-phone').style.visibility = 'hidden';
            ">Cancel</button>
			<button class="btn-confirm" onclick="
                var flagError = false;
                if (document.getElementById('Phone_Number').value.length != 10)
                {
                    document.getElementById('Phone_Number').style.border = '2px solid red';
                    flagError = true;

                    showAlert('danger', 'Incorrect format for a phone number', 3000);
                }      
                
                if(flagError)
                    return;     
                else
                {
                    if ((typeUser == 1) || (typeUser == 4))
                    {
                        var obj             = new Object();
                        obj.type            = 'Set-Number-Phone';
                        obj.leader          = leader;
                        obj.number_phone    = document.getElementById('Phone_Number').value;
                        obj.locations       = [];

                        window.parent.postMessage(JSON.stringify(obj), '*');                                                         
                    }     
                    else
                    {
                        showAlert('danger', 'No permissions are configured', 3000);
                    }                       
                }      
                
                document.getElementById('popup-create-phone').style.visibility = 'hidden';
				
				// Active loader
				document.getElementById('wait_div').style.visibility = 'visible';	
	
				clearTimeout(timeoutLocation);
				timeoutLocation = setTimeout(function() { 
					if (document.getElementById('wait_div').style.visibility == 'visible')
					{
						// Deactive loader
						document.getElementById('wait_div').style.visibility = 'hidden'; 

						showAlert('danger', 'Configuration failed', 5000);  
					}                     
				}, 3000);				
            ">Confirm</button>
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
			<button class="btn_cancel" onclick="document.getElementById('popup_confirm').style.visibility = 'hidden';">Cancel</button>
			<button class="btn_confirm" onclick="
                document.getElementById('popup_confirm').style.visibility = 'hidden';
                if (document.getElementById('header_popup_confirm').innerHTML.localeCompare('REMOVE THE ACCOUNT') == 0)
                {
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
                                                showAlert('danger', 'Account deactivation was unsuccessful.', 3000);
                                            }
                                            else
                                            {
                                                showUser(1, 'Account');
                                                showAlert('success', 'Account deactivation was successful.', 3000);
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
                                showUser(1, 'Account');
                            } else {
                                showAlert('danger', 'Accessing the server is not working properly', 3000);               
                            }
                        } 
                    });                         
                }   
                else if (document.getElementById('header_popup_confirm').innerHTML.localeCompare('ACCOUNT SETTINGS') == 0)
                {
                    $.ajax({
                        type: 'POST',						
                        url: './php/reset_user.php',
                        data: {
                        'Name': document.getElementById('content_popup_confirm').name
                        },
                        success: function(data) {
                            if (data.localeCompare('error') == 0) {
                                showAlert('danger', 'Failure to set up an account', 3000);
                            }
                            else
                            {
                                showAlert('success', 'Success in account setting', 3000);
                            }
                        }
                    });                    
                }  
				else if (document.getElementById('header_popup_confirm').innerHTML.localeCompare('REMOVE PHONE') == 0)
				{
					// Fimd page active
					var page = 1;
					var ul = document.getElementById('page_menu');
					for(var i = 0; i < ul.childNodes.length; i++)
					{
						if (ul.childNodes[i].classList.contains('active'))
						{
							page = i;
							break;
						}
					}

                    var obj             = new Object();
                    obj.type            = 'Delete-Number-Phone';
                    obj.leader          = leader;
                    obj.number_phone    = document.getElementById('content_popup_confirm').name;
					obj.page			= page;
					
                    window.parent.postMessage(JSON.stringify(obj), '*'); 
					
					// Active loader
					document.getElementById('wait_div').style.visibility = 'visible';	
						
					clearTimeout(timeoutLocation);
					timeoutLocation = setTimeout(function() { 
						if (document.getElementById('wait_div').style.visibility == 'visible')
						{
							// Deactive loader
							document.getElementById('wait_div').style.visibility = 'hidden'; 

							showAlert('danger', 'Configuration failed', 5000);  
						}                     
					}, 3000);
				}
            ">Confirm</button>
		</div>
	</div>
</div>

<div class="popup-program-list prevent-select" id="popup_location">
	<div class="popup-program-content" >
		<div class="program-list-table">
			<div class="header-program-table">
				<p>GO! CENTER LIST</p>
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
                <button class="btn-cancel" onclick="document.getElementById('popup_location').style.visibility = 'hidden';">Cancel</button>
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

                    var obj          = new Object();
                    obj.type         = 'Set-Locations';
                    obj.user         = document.getElementById('popup_location').name.split('_')[0];
                    obj.leader       = leader;
                    obj.locations    = locations;
                    obj.location_for = document.getElementById('popup_location').name.split('_')[1];

                    window.parent.postMessage(JSON.stringify(obj), '*');  
                    
                    document.getElementById('popup_location').style.visibility = 'hidden';
                ">Confirm</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>