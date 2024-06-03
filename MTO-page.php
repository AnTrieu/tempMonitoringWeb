<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" type="text/css" href="./css/common.css?v=1.0.4">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.7/dist/chartjs-plugin-zoom.min.js"></script>
    <script src="./js/jquery.min.js"></script>
    <script src="./js/common.js?v1.0.12"></script>
    <script src="js/xlsx.full.min.js"></script>

    <script>
        const MALL_pos = [[202, 1277], [757, 1237], [1129, 1019], [2026, 319], [1406, 973]];
        const EB_pos = [[1160, 680], [1300, 892], [1526, 862], [1823, 886], [1719, 1040]];
        
        let m_widthImg = -1;
        let m_heightImg = -1;
        let m_bodyWidth = -1;
        let m_bodyHeight = -1;

        let m_warningFlag = false;
        let m_threshold = 0;
        let m_thresholdDelta = 0;
        let m_temperate = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        let m_issue = ['', '', '', '', '', '', '', '', '', ''];
        let m_issueTemp = ['', '', '', '', '', '', '', '', '', ''];

        let data_chart = [];
        let m_timeUpdate = -1;
        let scheduleChart = -1;
        let m_timeFocus = -1;
        let myChart = null;

        let typeUser = -1;

        let m_flagDeleteNotify = false;
        let m_typeFocus = -1;
    </script>
</head>
<body class="body_MTO">
    <div class="alert-error hide" style="display: none;" id="alert">
		<img src="./img/danger.png" class="alert_icon" id="alert_icon">
		<strong class="alert-header" id="alert-header"></strong>
		<p class="alert-text" id="alert-text"></p>
	</div>

    <label class="outsite-label" id="OUT_square_11" style="cursor: pointer;" onclick="show_menu(document.getElementById('OUT_square_11'));"></label>

    <div id="context-menu" style="display: none; position: absolute; background-color: white; border: 1px solid #ccc; top: 100px; left: 500px; color: black;z-index: 2000;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="pointer-events: none;">
                <a href="#" id="action1" style="color: black; padding-left: 5px; display: flex; justify-content: center; align-items: center;"><img src="./img/hot-icon.png" width="20px" height="20px" style="padding-right:5px; margin-right: 5px;">
                    <span id="menu-title"></span>
                </a>
            </li>
            <li>
                <a href="#" id="action1" style="color: black; padding-left: 5px; display: flex; justify-content: center; align-items: center;">
                    <img src="./img/info-icon.png" width="20px" height="20px" style="padding-right:5px; margin-right: 5px;">
                    <span style="font-style: italic; opacity: 0.5; overflow: hidden; white-space: nowrap;text-overflow: ellipsis; width: 100px;" id = "note-sensor"
                        onclick="
                            if ((typeUser == 1) || (typeUser == 4))
                            {
                                setTimeout(function() {
                                    document.getElementById('popup_enter_notify').style.visibility = 'visible';
                                    document.getElementById('notify_input').style.border = ''; 
                                    document.getElementById('notify_input').value = '';
                                    document.getElementById('notify_input').focus();                     
                                }, 500);

                                var obj = new Object();
                                obj.type = 'Active-Popup';

                                window.parent.postMessage(JSON.stringify(obj), '*');                                 
                            }
                            else
                            {
                                showAlert('danger', 'No permissions are configured', 5000);   
                            }                                
                        ">
                    </span>
                </a>
            </li>            
            <li><a href="#" id="action3" style="color: black; padding-left: 5px; display: flex; justify-content: start; align-items: center;"><img src="./img/note-icon.png" width="20px" height="20px" style="padding-right:5px; margin-right: 5px;"><span
                onclick="
                    if ((typeUser == 1) || (typeUser == 4))
                    {
                        setTimeout(function() {
                            document.getElementById('popup_enter_issue').style.visibility = 'visible';
                            document.getElementById('issue_input').style.border = ''; 
                            document.getElementById('issue_input').value = '';
                            document.getElementById('issue_input').focus();                     
                        }, 500);

                        var obj = new Object();
                        obj.type = 'Active-Popup';

                        window.parent.postMessage(JSON.stringify(obj), '*');                             
                    }
                    else
                    {
                        showAlert('danger', 'No permissions are configured', 5000);   
                    }                          
                ">Report an issue</span></a></li>            
            <li><a href="#" id="action2" style="color: black; padding-left: 5px; display: flex; justify-content: start; align-items: center;"><img src="./img/history-icon.png" width="20px" height="20px" style="padding-right:5px; margin-right: 5px;"><span
                onclick="
                    var sDate = new Date();
                    var eDate = new Date();

                    /* Get 00:00:00 current day */
                    sDate.setHours(0);
                    sDate.setMinutes(0);
                    sDate.setSeconds(0);
                    sDate.setMilliseconds(0);

                    /* Get 23:59:59 current day */
                    eDate.setHours(23);
                    eDate.setMinutes(59);
                    eDate.setSeconds(59);
                    eDate.setMilliseconds(0);  

                    requestDataToDrawChart(sDate, eDate);
                ">Chart</span></a></li>
        </ul>
    </div>

    <div class="title" id="title">
        <div style="position: absolute; display: flex; justify-content: center; align-items: center;">
            <label class="title-label" style="font-weight: bold;"></label> 
        </div>  
        <div style="position: absolute; display: flex; justify-content: center; align-items: center;">
            <label class="title-label" style="font-size: 1.2vw;"></label> 
        </div>   
        <div style="position: absolute; display: flex; justify-content: center; align-items: center;">
            <label class="title-label" style="font-size: 1.2vw; cursor: pointer;" 
                onclick="
                    if ((typeUser == 1) || (typeUser == 4))
                    {
                        setTimeout(function() {
                            document.getElementById('popup_enter_confirm_header').innerText = 'VALIDATION OF IDENTITY';                            
                            document.getElementById('popup_enter_confirm').style.visibility = 'visible';
                            document.getElementById('PASSWORD_input').style.border = ''; 
                            document.getElementById('PASSWORD_input').value = '';
                            document.getElementById('PASSWORD_input').focus();
                        }, 500);

                        var obj = new Object();
                        obj.type = 'Active-Popup';

                        window.parent.postMessage(JSON.stringify(obj), '*');                        
                    } 
                    else
                    {
                        showAlert('danger', 'No permissions are configured', 5000);   
                    }                    
                ">
            </label>
            <input type="text" style="width:100%; height:80%; font-size: 1.2vw; display: none;" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" onfocus="m_typeFocus = 0;" onblur="selectThreshold(0)" onkeydown="if (event.keyCode === 13) {selectThreshold(3);}">
            <input type="text" style="width:100%; height:80%; font-size: 1.2vw; display: none;" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" onfocus="m_typeFocus = 1;" onblur="selectThreshold(1)" onkeydown="if (event.keyCode === 13) {selectThreshold(3);}">
        </div>
        <div style="position: absolute; display: flex; justify-content: start; align-items: center;">
            <input type="checkbox" style="left: 0px; opacity: 1; pointer-events: auto; width: 1.3vw; height: 1.3vw;" 
                onclick="
                    // Ignore permission (Customer request)
                    m_timeUpdate = Math.floor(new Date().getTime() / 1000);
                    m_warningFlag = this.checked;

                    var obj = new Object();
                    obj.type        = 'Request-Warning-Flag';
                    obj.location    = document.body.className.split('_')[1];
                    obj.value       = m_warningFlag;

                    window.parent.postMessage(JSON.stringify(obj), '*');                        
                ">
            <label class="title-label" style="font-size: 1.0vw; padding-left: calc(1.3vw + 20px);"></label> 
        </div>
        <div style="position: absolute; display: flex; justify-content: start; align-items: center;">
            <img src="./img/email-delete-icon.png" style="width: 50%; height: 80%; cursor: pointer;"
                onclick="
                        if ((typeUser == 1) || (typeUser == 4))
                        {
                            setTimeout(function() {
                                document.getElementById('popup_enter_confirm_header').innerText = 'DELETE MESSAGES';                            
                                document.getElementById('popup_enter_confirm').style.visibility = 'visible';
                                document.getElementById('PASSWORD_input').style.border = ''; 
                                document.getElementById('PASSWORD_input').value = '';
                                document.getElementById('PASSWORD_input').focus();
                            }, 500);

                            var obj = new Object();
                            obj.type = 'Active-Popup';

                            window.parent.postMessage(JSON.stringify(obj), '*');                        
                        } 
                        else
                        {
                            showAlert('danger', 'No permissions are configured', 5000);   
                        }                    
            ">            
        </div>      
        <div style="position: absolute; display: flex; justify-content: start; align-items: center;" 
            onclick="
                if ((typeUser == 1) || (typeUser == 4))
                {
                    // define '999' is all message
                    document.getElementById('note-sensor').name = 999;

                    setTimeout(function() {
                        document.getElementById('popup_enter_issue').style.visibility = 'visible';
                        document.getElementById('issue_input').style.border = ''; 
                        document.getElementById('issue_input').value = '';
                        document.getElementById('issue_input').focus();                     
                    }, 500);

                    var obj = new Object();
                    obj.type = 'Active-Popup';

                    window.parent.postMessage(JSON.stringify(obj), '*');                             
                }
                else
                {
                    showAlert('danger', 'No permissions are configured', 5000);   
                }                         
            ">
            <textarea class="title-textarea" readonly></textarea> 
        </div>             
    </div>

    <div class="popup_enter" id="popup_enter_confirm">
		<div class="popup_enter_background">
			<div class="header_enter_confirm">
                <p id="popup_enter_confirm_header"></p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Password </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Enter Password" type="password" name="PASSWORD_input" id="PASSWORD_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('PASSWORD_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
				<button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_confirm'))">Cancel</button>
				<button class="btn_confirm" onclick="confirmPopup('PASSWORD_input');">Confirm</button></button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_enter_notify">
		<div class="popup_enter_background">
			<div class="header_enter_notify">
				<p>LOCATION DETAILS</p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Note </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Enter Note" type="text" id="notify_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('notify_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
                <button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_notify'))">Cancel</button>
				<button class="btn_confirm" onclick="confirmPopup('notify_input');">Confirm</button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_enter_issue">
		<div class="popup_enter_background">
			<div class="header_enter_confirm">
				<p>ACCIDENT DETAILS</p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Note </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Enter Note" type="text" id="issue_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('issue_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
				<button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_issue'))">Cancel</button>
				<button class="btn_confirm" onclick="confirmPopup('issue_input');">Confirm</button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_chart">
		<div class="popup_enter_background" style="width:100%; height:100vh; margin-top: 0%; background-color: transparent; position: relative;">
			<div class="row-temp" id="row-function">
                <div>
                    <div style="">
                        <label for="dateStartInput">Start: </label>
                        <input type="datetime-local" id="dateStartInput" style="margin-left: 10px;">
                    </div>
                    <div style="margin-top: 15px;">
                        <label for="dateEndInput">End: </label>
                        <input type="datetime-local" id="dateEndInput" style="margin-left: 10px;">
                    </div>                    
                </div>
				<a href="#" class="filterButton" onclick="requestDataToDrawChart(new Date(document.getElementById('dateStartInput').value), new Date(document.getElementById('dateEndInput').value));">
					<img src="./img/search-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Search
				</a>
				<a href="#" class="downButton" onclick="downloadFunction()">
					<img src="./img/down-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Download
				</a> 
				<a href="#" class="closeButton" onclick="cancelPopup(document.getElementById('popup_chart'));">
					<img src="./img/delete-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Close
				</a>                                     				
			</div>		
			<canvas id="myChart" style="background-color: transparent; display: block;"></canvas>
		</div>
        <div id="wait_div" class="wait_div">
            <div class="loader-outside">
                <div class="loader"></div>
            </div>
        </div>	        
    </div>	

    <script>
        function downloadFunction()
        {
			// Tạo một workbook mới
			var workbook = XLSX.utils.book_new();  

            // Tạo một trang tính mới
			var sheet = XLSX.utils.aoa_to_sheet([
				["Thời gian", "Giá trị (độ C)"],
				["", "", ""]
			]);

            for (var i = 1; i < data_chart.length; i++) 
            {
                XLSX.utils.sheet_add_aoa(sheet, [[epochToDateTime(Array.from(data_chart[i])[0]), Math.floor(Array.from(data_chart[i])[1] / 10)  + "." + (Array.from(data_chart[i])[1] % 10)]], { origin: -1 });
            }

			// Chỉnh định dạng kích thước của ô
			var cellWidth = 15; // Độ rộng ô (số ký tự)
			var cellHeight = 20; // Độ cao ô (pixel)
			
			// Tạo đối tượng định dạng cho cột
			var columnWidth = [{ wch: 2 * cellWidth },{ wch: cellWidth },{ wch: 3 * cellWidth }];
			
			// Chỉnh định dạng kích thước cho các cột
			sheet['!cols'] = columnWidth;

            // Thêm workbook vào Workbook
			XLSX.utils.book_append_sheet(workbook, sheet, 'Sheet1');

			// Lấy ngày hiện tại
			var currentDate = new Date(document.getElementById('dateStartInput').value);
		
			// Tạo chuỗi tên tệp
			var fileName = "export_" + document.body.className.split('_')[1] + "_" + currentDate.getDate() + "_" + (currentDate.getMonth() + 1) + "_" +  currentDate.getFullYear() + ".xlsx";
		
			// Xuất workbook thành file Excel với tên động
			XLSX.writeFile(workbook, fileName);	            
        }

        function requestDataToDrawChart(dateStart, dateEnd)
        {       
            // Invalid date
            if (!(dateStart instanceof Date && !isNaN(dateStart)) || !(dateEnd instanceof Date && !isNaN(dateEnd)) || (dateStart.getTime() > dateEnd.getTime()))
            {
                cancelPopup(document.getElementById('popup_chart'));
                showAlert('danger', 'Date not available', 5000);    
                return;            
            }

            if (myChart) {
                myChart.destroy();
                myChart = null;
            }  

            document.getElementById('myChart').style.backgroundColor = 'transparent';
            document.getElementById('wait_div').style.visibility = 'visible';
            document.getElementById('row-function').style.visibility = 'hidden';
            document.getElementById('popup_chart').style.visibility = 'visible';  

            clearTimeout(scheduleChart);
            scheduleChart = setTimeout(function() { 
                document.getElementById('wait_div').style.visibility = 'hidden';
                document.getElementById('popup_chart').style.visibility = 'hidden';
                document.getElementById('row-function').style.visibility = 'hidden'; 
                showAlert('danger', 'Data loading error', 5000);                       
            }, 15000);

            // Clean buffer
            data_chart = [];
            
            // Set the value of the date input element to the formatted date
            var year = dateStart.getFullYear();
            var month = String(dateStart.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
            var day = String(dateStart.getDate()).padStart(2, '0');
            var hours = String(dateStart.getHours()).padStart(2, '0');
            var minutes = String(dateStart.getMinutes()).padStart(2, '0');
            document.getElementById('dateStartInput').value = `${year}-${month}-${day}T${hours}:${minutes}`;

            // Set the value of the date input element to the formatted date
            var year = dateEnd.getFullYear();
            var month = String(dateEnd.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
            var day = String(dateEnd.getDate()).padStart(2, '0');
            var hours = String(dateEnd.getHours()).padStart(2, '0');
            var minutes = String(dateEnd.getMinutes()).padStart(2, '0');
            document.getElementById('dateEndInput').value = `${year}-${month}-${day}T${hours}:${minutes}`;

            var obj = new Object();
            obj.type         = 'Request-Chart';
            obj.location     = document.body.className.split('_')[1];
            obj.value        = document.getElementById('issue_input').value;
            obj.dateStart    = dateStart.getTime() / 1000;
            obj.dateEnd      = dateEnd.getTime() / 1000;
            obj.user         = sessionStorage.getItem('username');
            obj.slot         = document.getElementById('note-sensor').name

            window.parent.postMessage(JSON.stringify(obj), '*');

            var obj = new Object();
            obj.type = 'Active-Popup';

            window.parent.postMessage(JSON.stringify(obj), '*');  
        }

        function cancelPopup(element)
        {
            if (myChart) {
                document.getElementById('row-function').style.visibility = 'hidden';

                myChart.destroy();
                myChart = null;
            }    
            element.style.visibility = 'hidden';

            var obj = new Object();
            obj.type = 'Deactive-Popup';

            window.parent.postMessage(JSON.stringify(obj), '*');    
        }

        function selectThreshold(type)
        {
            clearTimeout(m_timeFocus);
            m_timeFocus = setTimeout(function(type) {

                if ((type == 3) || ((m_typeFocus > -1) && (type == m_typeFocus)))
                {
                    if ((document.getElementById('title').childNodes[5].childNodes[3].value.length > 0) && 
                        (document.getElementById('title').childNodes[5].childNodes[5].value.length > 0) &&
                        (document.getElementById('title').childNodes[5].childNodes[1].style.display.localeCompare('none') == 0))
                    {                
                        m_timeUpdate        = Math.floor(new Date().getTime() / 1000);
                        m_threshold         = parseInt(document.getElementById('title').childNodes[5].childNodes[3].value);
                        m_thresholdDelta    = parseInt(document.getElementById('title').childNodes[5].childNodes[5].value);
                        document.getElementById('title').childNodes[5].childNodes[1].innerHTML = Math.floor(m_threshold / 10)  + "." + m_threshold % 10 + " &deg; C  &plusmn; " + Math.floor(m_thresholdDelta / 10)  + "." + m_thresholdDelta % 10 + " &deg; C";

                        var obj = new Object(); 
                        obj.type        = 'Request-Threshold';
                        obj.location    = document.body.className.split('_')[1];
                        obj.value       = m_threshold;
                        obj.delta       = m_thresholdDelta;
                        obj.user        = sessionStorage.getItem('username');

                        window.parent.postMessage(JSON.stringify(obj), '*');                         
                    }  
                    
                    document.getElementById('title').childNodes[5].childNodes[1].style.display = '';
                    document.getElementById('title').childNodes[5].childNodes[3].style.display = 'none';
                    document.getElementById('title').childNodes[5].childNodes[5].style.display = 'none'; 

                    m_typeFocus = -1;
                }                    
            }, 200, type);          
        }

        function confirmPopup(target)
        {
            if(target === 'issue_input')
            {
                if (document.getElementById('issue_input').value.length == 0)
                {
                    document.getElementById('issue_input').style.border = '2px solid red';
                    document.getElementById('issue_input').blur();
                    return;
                }
                else if (document.getElementById('issue_input').value.indexOf('_') !== -1)
                {
                    showAlert('danger', 'Contains no characters \'_\'', 3000);
                    cancelPopup(document.getElementById('popup_enter_issue'));
                }
                else
                {
                    var obj = new Object();
                    obj.type         = 'Request-Issue';
                    obj.location     = document.body.className.split('_')[1];
                    obj.value        = document.getElementById('issue_input').value;
                    obj.slot         = document.getElementById('note-sensor').name
                    obj.user         = sessionStorage.getItem('username');

                    window.parent.postMessage(JSON.stringify(obj), '*');
                    
                    cancelPopup(document.getElementById('popup_enter_issue'));               
                }      
            }
            else if(target === 'notify_input')
            {
                if(document.getElementById('notify_input').value.length == 0)
                {
                    document.getElementById('notify_input').style.border = '2px solid red';
                    return;
                }
                else
                {
                    if (document.getElementById('notify_input').value.indexOf('_') !== -1)
                    {
                        showAlert('danger', 'Contains no characters \'_\'', 3000);
                    }
                    else
                    {
                        var obj = new Object();
                        obj.type         = 'Request-Notify';
                        obj.location     = document.body.className.split('_')[1];
                        obj.value        = document.getElementById('notify_input').value;
                        obj.slot         = document.getElementById('note-sensor').name
                        obj.user         = sessionStorage.getItem('username');

                        window.parent.postMessage(JSON.stringify(obj), '*');
                    }
                    
                    cancelPopup(document.getElementById('popup_enter_notify'));               
                }       
            }
            else if(target === 'PASSWORD_input')
            {
                if(document.getElementById('PASSWORD_input').value.length == 0)
                {
                    document.getElementById('PASSWORD_input').style.border = '2px solid red';
                    return;
                }
                else
                {
                    var password = sessionStorage.getItem('password');

                    if(password != null && (document.getElementById('PASSWORD_input').value.localeCompare(password) == 0))
                    {	        
                        var header = document.getElementById('popup_enter_confirm_header').innerText;
                        
                        if (header.localeCompare('VALIDATION OF IDENTITY') == 0)
                        {
                            document.getElementById('title').childNodes[5].childNodes[1].style.display = 'none';
                            document.getElementById('title').childNodes[5].childNodes[3].style.display = '';
                            document.getElementById('title').childNodes[5].childNodes[3].value = document.getElementById('title').childNodes[5].childNodes[1].name.split('_')[0];
                            document.getElementById('title').childNodes[5].childNodes[5].style.display = '';
                            document.getElementById('title').childNodes[5].childNodes[5].value = document.getElementById('title').childNodes[5].childNodes[1].name.split('_')[1];                            
                            document.getElementById('title').childNodes[5].childNodes[3].focus();
                        }
                        else if (header.localeCompare('DELETE MESSAGES') == 0)
                        {
                            var obj = new Object();
                            obj.type        = 'Request-Delete-Issue';
                            obj.location    = document.body.className.split('_')[1];
                            obj.user        = sessionStorage.getItem('username');

                            window.parent.postMessage(JSON.stringify(obj), '*');   

                            m_flagDeleteNotify = true;
                        }
                    }
                    else
                    {
                        showAlert('danger', 'Incorrect password', 3000);
                    }
                    
                    cancelPopup(document.getElementById('popup_enter_confirm'));             
                }                   
            }
        }

        function show_menu(element)
        {
            if(element != null)
            {
                var circleId = element.attributes[1].value.split('_');
                var number = parseInt(circleId[2]);
                if (circleId[0].localeCompare("MALL") == 0)
                    number +=  EB_pos.length;
               
                document.getElementById("menu-title").textContent = "Lo." + number;
                document.getElementById("note-sensor").innerHTML = element.name;
                var parts = element.id.split('_');
                if(parts[0] == "EB")
                    document.getElementById('note-sensor').name = parseInt(parts[2]);
                else if(parts[0] == "MALL")
                    document.getElementById('note-sensor').name = EB_pos.length + parseInt(parts[2]);
                else if(parts[0] == "OUT")
                    document.getElementById('note-sensor').name = 11;                    
                else
                    document.getElementById('note-sensor').name = -1;

                if (circleId[0].localeCompare("OUT") == 0)
                {
                    document.getElementById("context-menu").style.top = (element.offsetTop + element.clientHeight/2) + "px";
                    document.getElementById("context-menu").style.left = (element.offsetLeft + element.clientWidth/2) + "px";
                }
                else
                {
                    document.getElementById("context-menu").style.top = element.offsetTop + "px";
                    document.getElementById("context-menu").style.left = element.offsetLeft + "px";
                    if(m_widthImg > 0 && m_heightImg > 0 && m_bodyWidth > 0 && m_bodyHeight > 0)
                    {
                        if ((element.offsetTop + 136) > m_bodyHeight)
                        {
                            document.getElementById("context-menu").style.top = (element.offsetTop - 136) + "px";
                        }
                    }
                }

                document.getElementById("context-menu").style.display = "";
            }            

            event.preventDefault();
            event.stopPropagation(); // Now the event won't bubble up
        }

        function draw_onsite(width_Img, height_Img, bodyWidth, bodyHeight)
        {
            var sensor_value = m_temperate[10];
            if ((-0xffff >= sensor_value) || (sensor_value >= 0xffff))
                sensor_value = 0;

            document.getElementById("OUT_square_11").style.top = normalize(733, height_Img, bodyHeight) + 'px'; 
            document.getElementById("OUT_square_11").style.left = normalize(35, width_Img, bodyWidth) + 'px'; 
            document.getElementById("OUT_square_11").style.width = normalize(310, width_Img, bodyWidth) + 'px';
            document.getElementById("OUT_square_11").style.height = normalize(58, height_Img, bodyHeight) + 'px';
            document.getElementById("OUT_square_11").innerHTML = "Outside: " + Math.floor(sensor_value / 10)  + "." +sensor_value % 10 + " &deg; C";
            document.getElementById("OUT_square_11").classList.add("line-visible");  
        }

        function draw_MALL(width_Img, height_Img, bodyWidth, bodyHeight)
        {
            for(var sensor = 1; sensor <= MALL_pos.length; sensor++)
            {
                var sensor_value = m_temperate[EB_pos.length + sensor - 1];
                if ((-0xffff >= sensor_value) || (sensor_value >= 0xffff))
                    sensor_value = 0;

                if ((MALL_pos[sensor - 1][0] < 0) || 
                    (MALL_pos[sensor - 1][1] < 0) || 
                    (document.getElementById("MALL_circle_"+ sensor) == null) || 
                    (document.getElementById("MALL_lo_" + sensor) == null))
                {
                    continue;
                }

                document.getElementById("MALL_lo_" + sensor).style.width = normalize(100, width_Img, bodyWidth) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.height = normalize(140, height_Img, bodyHeight) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.top = normalize(550, height_Img, bodyHeight) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.left = normalize(sensor * (18 + 100), width_Img, bodyWidth) + 'px'; 
                document.getElementById("MALL_lo_" + sensor).innerHTML = "Lo." + (sensor + EB_pos.length) + "<br>" + Math.floor(sensor_value / 10)  + "." + sensor_value % 10;                    
                document.getElementById("MALL_lo_" + sensor).classList.add("line-visible");

                document.getElementById("MALL_circle_" + sensor).style.top = normalize(MALL_pos[sensor - 1][1], height_Img, bodyHeight) + 'px'; 
                document.getElementById("MALL_circle_" + sensor).style.left = normalize(MALL_pos[sensor - 1][0], width_Img, bodyWidth) + 'px'; 
                document.getElementById("MALL_circle_" + sensor).classList.add("line-visible"); 
 
                var top = -40; left = 30; 
                document.getElementById("MALL_final_" + sensor).style.top = normalize(MALL_pos[sensor - 1][1] + top, height_Img, bodyHeight) + 'px'; 
                document.getElementById("MALL_final_" + sensor).style.left = normalize(MALL_pos[sensor - 1][0] + left, width_Img, bodyWidth) + 'px'; 
                document.getElementById("MALL_final_" + sensor).childNodes[1].innerHTML = Math.floor(sensor_value / 10)  + "." + sensor_value % 10 + " &deg; C";                
                document.getElementById("MALL_final_" + sensor).classList.add("line-visible");                           
            }
        }

        function draw_EB(width_Img, height_Img, bodyWidth, bodyHeight)
        {
            for(var sensor = 1; sensor <= EB_pos.length; sensor++)
            {
                if ((EB_pos[sensor - 1][0] < 0) || 
                    (EB_pos[sensor - 1][1] < 0) ||
                    (document.getElementById("EB_circle_"+ sensor) == null))
                    continue;

                var sensor_value = m_temperate[sensor - 1];
                if ((-0xffff >= sensor_value) || (sensor_value >= 0xffff))
                    sensor_value = 0;

                document.getElementById("EB_lo_" + sensor).style.width = normalize(100, width_Img, bodyWidth) + 'px';
                document.getElementById("EB_lo_" + sensor).style.height = normalize(140, height_Img, bodyHeight) + 'px';
                document.getElementById("EB_lo_" + sensor).style.top = normalize(1280, height_Img, bodyHeight) + 'px';
                document.getElementById("EB_lo_" + sensor).style.left = normalize(1465 + sensor * (18 + 100), width_Img, bodyWidth) + 'px';
                document.getElementById("EB_lo_" + sensor).innerHTML = "Lo." + sensor + "<br>" + Math.floor(sensor_value / 10)  + "." + sensor_value % 10;            
                document.getElementById("EB_lo_" + sensor).classList.add("line-visible");

                document.getElementById("EB_circle_" + sensor).style.top = normalize(EB_pos[sensor - 1][1], height_Img, bodyHeight) + 'px'; 
                document.getElementById("EB_circle_" + sensor).style.left = normalize(EB_pos[sensor - 1][0], width_Img, bodyWidth) + 'px'; 
                document.getElementById("EB_circle_" + sensor).classList.add("line-visible"); 

                var top = -40; left = 30;                       
                document.getElementById("EB_final_" + sensor).style.top = normalize(EB_pos[sensor - 1][1] + top, height_Img, bodyHeight) + 'px'; 
                document.getElementById("EB_final_" + sensor).style.left = normalize(EB_pos[sensor - 1][0] + left, width_Img, bodyWidth) + 'px';
                document.getElementById("EB_final_" + sensor).childNodes[1].innerHTML = Math.floor(sensor_value / 10)  + "." + sensor_value % 10 + " &deg; C"; 
                document.getElementById("EB_final_" + sensor).classList.add("line-visible");                 
            }          
        }

        function draw_all(location, width_Img, height_Img, bodyWidth, bodyHeight)
        {
            var epochCurrentSeconds = Math.floor(new Date().getTime() / 1000);

            if (location != null)
            {
                document.getElementById('title').childNodes[1].style.height = normalize(61, height_Img, bodyHeight) + 'px';
                document.getElementById('title').childNodes[1].style.width = normalize(324, width_Img, bodyWidth) + 'px';
                document.getElementById('title').childNodes[1].style.top = normalize(0, height_Img, bodyHeight) + 'px';
                document.getElementById('title').childNodes[1].style.left = normalize(308, width_Img, bodyWidth) + 'px';
                document.getElementById('title').childNodes[1].childNodes[1].textContent = location.toUpperCase();
            }

            document.getElementById('title').childNodes[3].style.height = normalize(61, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[3].style.width = normalize(308, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[3].style.top = normalize(66, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[3].style.left = normalize(0, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[3].childNodes[1].textContent = "Warning level";

            document.getElementById('title').childNodes[5].style.height = normalize(61, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[5].style.width = normalize(321, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[5].style.top = normalize(66, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[5].style.left = normalize(308, width_Img, bodyWidth) + 'px';
            // Active update after 4 seconds
            if ((epochCurrentSeconds - m_timeUpdate) > 4)
            {
                document.getElementById('title').childNodes[5].childNodes[1].innerHTML = Math.floor(m_threshold / 10)  + "." + m_threshold % 10 + " &deg; C  &plusmn; " + Math.floor(m_thresholdDelta / 10)  + "." + m_thresholdDelta % 10 + " &deg; C";
                document.getElementById('title').childNodes[5].childNodes[1].name = m_threshold + "_" + m_thresholdDelta;
            }         

            /* Request flag */
            document.getElementById('title').childNodes[7].style.height = normalize(60, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[7].style.width = normalize(530, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[7].style.top = normalize(132, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[7].style.left = normalize(0, width_Img, bodyWidth) + 'px';
            // Active update after 4 seconds
            if ((epochCurrentSeconds - m_timeUpdate) > 4)
            {
                document.getElementById('title').childNodes[7].childNodes[1].checked = m_warningFlag;
            }                
            document.getElementById('title').childNodes[7].childNodes[3].textContent = "The system is being maintained";

            /* Delete Message */
            document.getElementById('title').childNodes[9].style.height = normalize(60, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[9].style.width = normalize(100, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[9].style.top = normalize(132, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[9].style.left = normalize(530, width_Img, bodyWidth) + 'px';

            /* Textarea */
            document.getElementById('title').childNodes[11].style.height = normalize(80, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[11].style.width = normalize(630, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[11].style.top = normalize(210, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[11].style.left = normalize(0, width_Img, bodyWidth) + 'px';

            // Clean textArea
            while (document.getElementById('title').childNodes[11].childNodes[1].firstChild) {
                document.getElementById('title').childNodes[11].childNodes[1].removeChild(document.getElementById('title').childNodes[11].childNodes[1].firstChild);
            }                

            for(var i = 0; i < m_issue.length; i++)
            {
                if (m_issue[i].length > 0)
                {
                    document.getElementById('title').childNodes[11].childNodes[1].appendChild(document.createTextNode(m_issue[i]));                        
                }
            }

            // Auto scroll
            for(var i = 0; i < m_issue.length; i++)
            {
                if(m_issue[i] !== m_issueTemp[i])
                {
                    document.getElementById('title').childNodes[11].childNodes[1].scrollTop = document.getElementById('title').childNodes[11].childNodes[1].scrollHeight;
                    m_issueTemp = m_issue;
                    break;
                }
            }

            if(document.getElementById('title').childNodes[11].childNodes[1].childNodes.length == 0)
            {
                document.getElementById('title').childNodes[11].childNodes[1].textContent = "No data";

                m_flagDeleteNotify = false;
            }           
            else
            {
                if(m_flagDeleteNotify)
                {
                    showAlert('danger', 'Eliminate the unsuccessful message', 3000);
                    m_flagDeleteNotify = false;
                }                
            }

            // Out site
            draw_onsite(width_Img, height_Img, bodyWidth, bodyHeight);
            
            // MALL sensor
            draw_MALL(width_Img, height_Img, bodyWidth, bodyHeight);

            // EB sensor
            draw_EB(width_Img, height_Img, bodyWidth, bodyHeight);

            document.getElementById("title").style.visibility = 'visible';  
        }

        function drawChart()
        {
            var xValues = [], yValues = [];
            for(var i = 0; i< data_chart.length; i++)
            {
                xValues.push(Array.from(data_chart[i])[0]);  
                yValues.push(parseFloat(Array.from(data_chart[i])[1]) / 10);                                                               
            }
               
            myChart = new Chart('myChart', {               
                type: 'line',
                data: {
                    labels: xValues,
                    datasets: [{
                        fill: false,
                        lineTension: 0,
                        backgroundColor: 'rgba(0,0,255,1.0)',
                        borderColor: 'rgba(0,0,255,0.1)',
                        data: yValues,
                        // Đặt màu cho các điểm lớn hơn 25
                        pointBackgroundColor: function(context) {
                            var index = context.dataIndex;
                            var value = context.dataset.data[index];
                            return ((value * 10) > (m_threshold + 20)) ? 'red' : (((value * 10) > m_threshold) ? 'yellow' : 'rgba(0,0,255,1.0)');
                        },
                        pointRadius: 2 // Kích thước của các điểm                 
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'x'
                            },
                            zoom: {
                                enabled: true,
                                mode: 'x',
                                speed: 0.00000000000001, // Đặt tốc độ zoom nhanh hơn nữa
                                sensitivity: 0.000000000005, // Đặt độ nhạy cao hơn
                                wheel: {
                                    enabled: true,
                                    speed: 1000  // Tăng tốc độ zoom bánh xe chuột
                                },
                                rangeMin: {
                                    x: null,
                                },
                                rangeMax: {
                                    x: null,
                                }                                
                            }
                        }   
                    },                
                    title: {
                        display: true,
                        text: 'TEMPERATURE FLUCTUATION CHART',
                        fontSize: 26
                    },                    
                    legend: {display: false},
                    scales: {
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "Time (hh:mm)",
                                fontSize: 20
                            },
                            ticks: {
								autoSkip: true,
								maxTicksLimit: 15,
                                callback: function(value, index, values) {
                                    var date = new Date(value * 1000);
                                    var hours = date.getHours();
                                    var minutes = date.getMinutes();
                                    return hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');                                
                                }
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
								display: true,
								labelString: 'Temperature (\u00B0C)',
                                fontSize: 20
                            },
							ticks: {
								// Thiết lập giới hạn tối thiểu và tối đa của trục y
								suggestedMin: -10, // Giá trị tối thiểu của trục y
								suggestedMax: 40, // Giá trị tối đa của trục y
							}							
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItem, data) {
                                // Định dạng tiêu đề của tooltip
                                var label = tooltipItem[0].xLabel;
                                var date = new Date(label * 1000);
                                var hours = date.getHours();
                                var minutes = date.getMinutes();
								var seconds = date.getSeconds();
                                return hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
                            },
                            label: function(tooltipItem, data) {
                                // Định dạng nội dung của tooltip
                                return 'Temperature: ' + tooltipItem.yLabel + '\u00B0C';
                            }
                        }
                    }
                }
            });

            document.getElementById("myChart").style.width = "100vw";
            document.getElementById("myChart").style.height = "calc(100% - 60px)";
            document.getElementById("myChart").style.backgroundColor = "white";
        }

        $(window).load(function() {

            for (var i = 1; i <= MALL_pos.length; i++) 
            {
                // Tạo phần tử div cho đường thẳng và đường tròn
                var redCircle = document.createElement("div");
                var locationLabel = document.createElement("label");
                var finalCircle = document.createElement("div");

                redCircle.classList.add("red-circle");
                redCircle.id = "MALL_circle_" + i;
                redCircle.addEventListener("click", (function(index) {
                    return function() {
                        show_menu(document.getElementById("MALL_circle_" + index));
                    };
                })(i));


                locationLabel.className = "location-label";
                locationLabel.id = "MALL_lo_" + i;

                finalCircle.className = "final-circle";
                finalCircle.id = "MALL_final_" + i;

                // Tạo hai label mới
                var labelTop = document.createElement("label");
                labelTop.className = "custom-label";
                labelTop.style.fontWeight = "bold";
                labelTop.textContent = "Lo." + (i + EB_pos.length);

                var labelBottom = document.createElement("label");
                labelBottom.className = "custom-label";
                labelBottom.style.fontSize = "0.8vw";

                // Thêm label vào finalCircle
                finalCircle.appendChild(labelTop);
                finalCircle.appendChild(labelBottom);

                // Thêm các phần tử vào body
                document.body.appendChild(locationLabel);
                document.body.appendChild(redCircle);
                document.body.appendChild(finalCircle);
            }

            for (var i = 1; i <= EB_pos.length; i++) {
                // Tạo phần tử div cho đường thẳng và đường tròn
                var redCircle = document.createElement("div");
                var locationLabel = document.createElement("label");
                var finalCircle = document.createElement("div");

                redCircle.classList.add("red-circle");
                redCircle.id = "EB_circle_" + i;
                redCircle.addEventListener("click", (function(index) {
                    return function() {
                        show_menu(document.getElementById("EB_circle_" + index));
                    };
                })(i));

                locationLabel.className = "location-label";
                locationLabel.id = "EB_lo_" + i;

                finalCircle.className = "final-circle";
                finalCircle.id = "EB_final_" + i;

                // Tạo hai label mới
                var labelTop = document.createElement("label");
                labelTop.className = "custom-label";
                labelTop.style.fontWeight = "bold";
                labelTop.textContent = "Lo." + i;

                var labelBottom = document.createElement("label");
                labelBottom.className = "custom-label";
                labelBottom.style.fontSize = "0.8vw";

                // Thêm label vào finalCircle
                finalCircle.appendChild(labelTop);
                finalCircle.appendChild(labelBottom);

                // Thêm các phần tử vào body
                document.body.appendChild(locationLabel);
                document.body.appendChild(redCircle);
                document.body.appendChild(finalCircle);
            }

            // Add click event listener to the window
            window.addEventListener("click", function(event) {
                document.getElementById("context-menu").style.display = "none";       
            });

            window.addEventListener('message', function(event) {	
                if (event.data.length > 0) {	
                    var obj = JSON.parse(event.data);

                    // Message MQTT
                    if (obj.topic != null)
                    {
                        var parts = obj.location.split('_');
                        var msg = JSON.parse(obj.message);
                        if (msg.command.localeCompare("notify_data") == 0)
                        {
                            for(var i = 0; i < msg.data.locations.length; i++)
                            {
                                if(msg.data.locations[i][0].localeCompare(parts[0]) == 0)
                                {
                                    m_threshold         = msg.data.locations[i][2];
                                    m_thresholdDelta    = msg.data.locations[i][6];
                                    m_warningFlag       = msg.data.locations[i][1];
                                    m_temperate         = msg.data.locations[i][3]; 
                                    m_issue             = msg.data.locations[i][5]; 

                                    for(var j = 1; j <= m_temperate.length; j++)
                                    {
                                        if (j <= EB_pos.length)
                                        {
                                            if(document.getElementById("EB_circle_" + j) == null)
                                                continue;

                                            document.getElementById("EB_circle_" + j).name = msg.data.locations[i][4][j - 1]; 
                                            if ((-0xffff < m_temperate[j - 1]) && (m_temperate[j - 1] < 0xffff))
                                            {
                                                if (!document.getElementById("EB_circle_" + j).classList.contains("online"))
                                                    document.getElementById("EB_circle_" + j).classList.add("online");    
                                                    
                                                if (m_temperate[j - 1] >= m_threshold)
                                                {
                                                    if (!document.getElementById("EB_circle_" + j).classList.contains("warning"))
                                                        document.getElementById("EB_circle_" + j).classList.add("warning");                                                    
                                                }
                                                else
                                                {
                                                    if (document.getElementById("EB_circle_" + j).classList.contains("warning"))
                                                        document.getElementById("EB_circle_" + j).classList.remove("warning");                                                   
                                                }                                                    
                                            }
                                            else
                                            {
                                                if (document.getElementById("EB_circle_" + j).classList.contains("online"))
                                                    document.getElementById("EB_circle_" + j).classList.remove("online");   
                                                if (document.getElementById("EB_circle_" + j).classList.contains("warning"))
                                                    document.getElementById("EB_circle_" + j).classList.remove("warning");                                                                                                 
                                            }                                            
                                        }           
                                        else if (j <= (EB_pos.length + MALL_pos.length))
                                        {
                                            if(document.getElementById("MALL_circle_" + (j - EB_pos.length)) == null)
                                                continue;

                                            document.getElementById("MALL_circle_" + (j - EB_pos.length)).name = msg.data.locations[i][4][j - 1];

                                            if ((-0xffff < m_temperate[j - 1]) && (m_temperate[j - 1] < 0xffff))
                                            {
                                                if (!document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.contains("online"))
                                                    document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.add("online");

                                                if (m_temperate[j - 1] >= m_threshold)
                                                {
                                                    if (!document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.contains("warning"))
                                                        document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.add("warning");                                                    
                                                }
                                                else
                                                {
                                                    if (document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.contains("warning"))
                                                        document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.remove("warning");                                                   
                                                }
                                            }
                                            else
                                            {
                                                if (document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.contains("online"))
                                                    document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.remove("online");              
                                                if (document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.contains("warning"))
                                                    document.getElementById("MALL_circle_" + (j - EB_pos.length)).classList.remove("warning");                                                                                                       
                                            }
                                        } 
                                        else
                                        {
                                            if(document.getElementById("OUT_square_11") == null)
                                                continue;

                                            document.getElementById("OUT_square_11").name = msg.data.locations[i][4][j - 1];
                                        }                                                                                     
                                    }
                                    
                                    if(m_widthImg > 0 && m_heightImg > 0 && m_bodyWidth > 0 && m_bodyHeight > 0)
                                    {
                                        draw_all(null, m_widthImg, m_heightImg, m_bodyWidth, m_bodyHeight);
                                    }

                                    break;
                                }
                            }
                           
                        }
                        else if (msg.command.localeCompare("chart_data") == 0)
                        {
                            for(var i = 0; i< msg.data.filter.length; i++)
                            {
                                var item = new Set([msg.data.filter[i]['t'], msg.data.filter[i]['v'] <= -0xffff || msg.data.filter[i]['v'] >= 0xffff ? 0 : msg.data.filter[i]['v']]);
                                data_chart.push(item);
                            }
  
                            if (data_chart.length >= msg.length)
                            {
                                clearTimeout(scheduleChart);
                                document.getElementById('wait_div').style.visibility = 'hidden';
                                document.getElementById('row-function').style.visibility = 'visible';

                                drawChart();
                            }
                        }                
                    }

                    // Message internal
                    else 
                    {
                        if (obj.type.localeCompare("Pre-Show-Location") == 0)
                        {
                            // Reset page
                            m_warningFlag = false;
                            m_threshold = 0;
                            m_temperate = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];    
                            m_issue = ['', '', '', '', '', '', '', '', '', ''];
                            m_issueTemp = ['', '', '', '', '', '', '', '', '', ''];
                            data_chart = [];
                            typeUser = -1;

                            document.getElementById('popup_enter_confirm').style.visibility = 'hidden';
                            document.getElementById('popup_enter_notify').style.visibility = 'hidden';
                        }
                        else if (obj.type.localeCompare("Show-Location") == 0)
                        {
                            if(m_widthImg < 0 || m_heightImg < 0 || m_bodyWidth < 0 || m_bodyHeight < 0)
                            {
                                var img = new Image();
                                img.src = window.getComputedStyle(document.querySelector('body')).getPropertyValue('background-image').replace(/url\((['"])?(.*?)\1\)/gi, '$2');

                                img.onload = function() {

                                    m_widthImg = img.width;
                                    m_heightImg = img.height;
                                    m_bodyWidth = document.body.clientWidth;
                                    m_bodyHeight = document.body.clientHeight;
                                    
                                    draw_all(obj.location, m_widthImg, m_heightImg, m_bodyWidth, m_bodyHeight);
                                };
                            }
                            else
                            {
                                // Update size body
                                m_bodyWidth = document.body.clientWidth;
                                m_bodyHeight = document.body.clientHeight;

                                draw_all(obj.location, m_widthImg, m_heightImg, m_bodyWidth, m_bodyHeight);
                            }

                            // Xóa biểu đồ cũ
                            if (myChart) {
                                myChart.destroy();
                                myChart = null;

                                drawChart();
                            }   
                            
                            typeUser = obj.typeUser;
                        }
                        else if (obj.type.localeCompare("Update-Type-User") == 0)
                        {
                            // console.log(typeUser)
                            typeUser = obj.typeUser;
                        }                        
                    }
                }
            });

            window.addEventListener("click", function(e) {
                if ((e.target == document.getElementById("popup_enter_confirm")) ||
                    (e.target == document.getElementById("popup_enter_notify"))  ||
                    (e.target == document.getElementById("popup_enter_issue"))   ||
                    (e.target == document.getElementById("popup_chart"))) {
                        cancelPopup(e.target);
                }				
            });    
            
            document.getElementById('title').childNodes[7].childNodes[1].checked = false;
        });

    </script> 
</body>
</html>
