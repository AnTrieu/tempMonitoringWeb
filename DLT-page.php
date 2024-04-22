<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" type="text/css" href="./css/common.css?v=1.0.3">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="./js/jquery.min.js"></script>
    <script src="./js/common.js?v1.0"></script>
    <script src="js/xlsx.full.min.js"></script>

    <script>
        const MALL_pos = [[1634, 474], [1586, 633], [1470, 544], [1306, 551], [1083, 551]];
        const EB_pos = [[1050, 266], [1233, 227], [1271, 318], [1255, 462], [1041, 467]];
        
        let m_widthImg = -1;
        let m_heightImg = -1;
        let m_bodyWidth = -1;
        let m_bodyHeight = -1;

        let m_warningFlag = false;
        let m_threshold = 0;
        let m_temperate = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        let m_issue = ['', '', '', '', '', '', '', '', '', ''];
        let m_issueTemp = ['', '', '', '', '', '', '', '', '', ''];

        let data_chart = [];
        let m_timeUpdate = -1;
        let scheduleChart = -1;
        let myChart = null;

        let typeUser = -1;
    </script>
</head>
<body class="body_DLT">
    <div class="alert-error hide" style="display: none;" id="alert">
		<img src="./img/danger.png" class="alert_icon" id="alert_icon">
		<strong class="alert-header" id="alert-header"></strong>
		<p class="alert-text" id="alert-text"></p>
	</div>

    <label class="outsite-label" id="info_outsite"></label>
    <div class="vertical-dashed-line" id="out_site_1"></div>
    <div class="horizontal-dashed-line" id="out_site_2"></div>
    <div class="vertical-dashed-line" id="out_site_3"></div>

    <div class="vertical-dashed-line" id="MALL_main_2"></div>
    <div class="horizontal-dashed-line" id="MALL_main_3"></div>
    <div class="horizontal-dashed-line" id="MALL_main"></div>

    <div class="horizontal-line"   id="EB_main_1"></div>
    <div class="vertical-line" id="EB_main_2"></div>
    <div class="horizontal-line" id="EB_main_3"></div>
    <div class="horizontal-line" id="EB_main"></div>

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
                onclick="requestDataToDrawChart(new Date());">Chart</span></a></li>
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
            <label class="title-label" style="font-size: 1.2vw;  cursor: pointer;" 
                onclick="
                    if ((typeUser == 1) || (typeUser == 4))
                    {
                        setTimeout(function() {
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
            <input type="text" style="width:100%; height:100%; font-size: 1.2vw; display: none;" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" onblur="selectThreshold()" onkeydown="if (event.keyCode === 13) {selectThreshold();}">
        </div> 
        <div style="position: absolute; display: flex; justify-content: start; align-items: center;">
            <input type="checkbox" style="left: 0px; opacity: 1; pointer-events: auto;" 
                onclick="
                    if ((typeUser == 1) || (typeUser == 4))
                    {
                        m_timeUpdate = Math.floor(new Date().getTime() / 1000);
                        m_warningFlag = this.checked;

                        var obj = new Object();
                        obj.type        = 'Request-Warning-Flag';
                        obj.location    = document.body.className.split('_')[1];
                        obj.value       = m_warningFlag;

                        window.parent.postMessage(JSON.stringify(obj), '*');                         
                    } 
                    else
                    {
                        document.getElementById('title').childNodes[7].childNodes[1].checked = !this.checked;
                        showAlert('danger', 'No permissions are configured', 5000);   
                    }                       
                ">
            <label class="title-label" style="font-size: 1.2vw; padding-left: 30px;"></label> 
        </div>  
        <div style="position: absolute; display: flex; justify-content: start; align-items: center;">
            <textarea class="title-textarea" readonly></textarea> 
        </div>             
    </div>

    <div class="popup_enter" id="popup_enter_confirm">
		<div class="popup_enter_background">
			<div class="header_enter_confirm">
				<p>XÁC MINH DANH TÍNH</p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Mật khẩu </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Nhập mật khẩu" type="password" name="PASSWORD_input" id="PASSWORD_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('PASSWORD_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
				<button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_confirm'))">Hủy bỏ</button>
				<button class="btn_confirm" onclick="confirmPopup('PASSWORD_input');">Xác nhận</button></button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_enter_notify">
		<div class="popup_enter_background">
			<div class="header_enter_notify">
				<p>THÔNG TIN VỊ TRÍ</p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Ghi chú </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Nhập ghi chú" type="text" id="notify_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('notify_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
                <button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_notify'))">Hủy bỏ</button>
				<button class="btn_confirm" onclick="confirmPopup('notify_input');">Xác nhận</button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_enter_issue">
		<div class="popup_enter_background">
			<div class="header_enter_confirm">
				<p>THÔNG TIN SỰ CỐ</p>
			</div>
			<div class="content_enter_confirm">
				<div class="popup_device_info">
					<p><span>* </span>Ghi chú </p>
				</div>
				<div class="popup_device_info">
					<input placeholder="Nhập ghi chú" type="text" id="issue_input" onfocus = "this.style.border = ''" onkeydown="if (event.keyCode === 13) {confirmPopup('issue_input');}">
				</div>
			</div>
			<div class="btn_new_folder">
				<button class="btn_cancel" onclick="cancelPopup(document.getElementById('popup_enter_issue'))">Hủy bỏ</button>
				<button class="btn_confirm" onclick="confirmPopup('issue_input');">Xác nhận</button>
			</div>
		</div>
	</div>	

    <div class="popup_enter" id="popup_chart">
		<div class="popup_enter_background" style="width:100%; height:100vh; margin-top: 0%; background-color: transparent; position: relative;">
			<div class="row-temp" id="row-function">
				<label for="dateInput">Thời gian: </label>
				<input type="date" id="dateInput" style="margin-left: 10px;">
				<a href="#" class="filterButton" onclick="requestDataToDrawChart(new Date(document.getElementById('dateInput').value));">
					<img src="./img/search-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Tìm kiếm
				</a>
				<a href="#" class="downButton" onclick="downloadFunction()">
					<img src="./img/down-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Lưu trữ
				</a> 
				<a href="#" class="closeButton" onclick="cancelPopup(document.getElementById('popup_chart'));">
					<img src="./img/delete-icon.png" width="16" height="16" alt="Icon" style="padding-right:5px; ">
					Kết thúc
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
				["Thời gian", "Giá trị"],
				["", "", ""]
			]);

            for (var i = 1; i < data_chart.length; i++) 
            {
                XLSX.utils.sheet_add_aoa(sheet, [[epochToDateTime(Array.from(data_chart[i])[0]), Array.from(data_chart[i])[1]]], { origin: -1 });
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
			var currentDate = new Date(document.getElementById('dateInput').value);
		
			// Tạo chuỗi tên tệp
			var fileName = "export_" + currentDate.getDate() + "_" + (currentDate.getMonth() + 1) + "_" +  currentDate.getFullYear() + ".xlsx";
		
			// Xuất workbook thành file Excel với tên động
			XLSX.writeFile(workbook, fileName);	            
        }

        function requestDataToDrawChart(date)
        {           
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
            
            // Get 00:00:00 current day
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            date.setMilliseconds(0);

            // Set the value of the date input element to the formatted date
            document.getElementById('dateInput').value = (date.getFullYear()) + "-" + ((date.getMonth() + 1 < 10 ? '0' : '') + (date.getMonth() + 1)) + "-" + ((date.getDate() < 10 ? '0' : '') + date.getDate());

            var obj = new Object();
            obj.type         = 'Request-Chart';
            obj.location     = document.body.className.split('_')[1];
            obj.value        = document.getElementById('issue_input').value;
            obj.date         = date.getTime() / 1000;
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

        function selectThreshold()
        {
            document.getElementById('title').childNodes[5].childNodes[1].style.display = '';
            document.getElementById('title').childNodes[5].childNodes[3].style.display = 'none';   

            if(this.value !==''){
                m_timeUpdate = Math.floor(new Date().getTime() / 1000);
                m_threshold = parseInt(document.getElementById('title').childNodes[5].childNodes[3].value);
                document.getElementById('title').childNodes[5].childNodes[1].innerHTML = Math.floor(m_threshold / 10)  + '.' + m_threshold % 10 + ' &deg; C';

                var obj = new Object(); 
                obj.type        = 'Request-Threshold';
                obj.location    = document.body.className.split('_')[1];
                obj.value       = m_threshold;

                window.parent.postMessage(JSON.stringify(obj), '*');                         
            }
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
                    var obj = new Object();
                    obj.type         = 'Request-Notify';
                    obj.location     = document.body.className.split('_')[1];
                    obj.value        = document.getElementById('notify_input').value;
                    obj.slot         = document.getElementById('note-sensor').name

                    window.parent.postMessage(JSON.stringify(obj), '*');
                    
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
                        document.getElementById('title').childNodes[5].childNodes[1].style.display = 'none';
                        document.getElementById('title').childNodes[5].childNodes[3].style.display = '';
                        document.getElementById('title').childNodes[5].childNodes[3].value = document.getElementById('title').childNodes[5].childNodes[1].name;
                        document.getElementById('title').childNodes[5].childNodes[3].focus();
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
                else
                    document.getElementById('note-sensor').name = -1;

                document.getElementById("context-menu").style.top = element.offsetTop + "px";
                document.getElementById("context-menu").style.left = element.offsetLeft + "px";

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

            document.getElementById("info_outsite").style.top = normalize(1111, height_Img, bodyHeight) + 'px'; 
            document.getElementById("info_outsite").style.left = normalize(32, width_Img, bodyWidth) + 'px'; 
            document.getElementById("info_outsite").style.width = normalize(310, width_Img, bodyWidth) + 'px';
            document.getElementById("info_outsite").style.height = normalize(58, height_Img, bodyHeight) + 'px';
            document.getElementById("info_outsite").innerHTML = "Outside: " + Math.floor(sensor_value / 10)  + "." +sensor_value % 10 + " &deg; C";
            document.getElementById("info_outsite").classList.add("line-visible");                            
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
                    (document.getElementById("MALL_"+ sensor) == null) || 
                    (document.getElementById("MALL_circle_"+ sensor) == null) || 
                    (document.getElementById("MALL_lo_" + sensor) == null))
                {
                    continue;
                }

                document.getElementById("MALL_lo_" + sensor).style.width = normalize(100, width_Img, bodyWidth) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.height = normalize(140, height_Img, bodyHeight) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.top = normalize(930, height_Img, bodyHeight) + 'px';
                document.getElementById("MALL_lo_" + sensor).style.left = normalize(sensor * (18 + 100), width_Img, bodyWidth) + 'px'; 
                document.getElementById("MALL_lo_" + sensor).innerHTML = "Lo." + (sensor + EB_pos.length) + "<br>" + Math.floor(sensor_value / 10)  + "." + sensor_value % 10;                    
                document.getElementById("MALL_lo_" + sensor).classList.add("line-visible");

                document.getElementById("MALL_circle_" + sensor).style.top = normalize(MALL_pos[sensor - 1][1], height_Img, bodyHeight) + 'px'; 
                document.getElementById("MALL_circle_" + sensor).style.left = normalize(MALL_pos[sensor - 1][0], width_Img, bodyWidth) + 'px'; 
                document.getElementById("MALL_circle_" + sensor).classList.add("line-visible"); 
 
                var top = -35, left = 20;
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
                if ((EB_pos[sensor - 1][0] < 0) || (EB_pos[sensor - 1][1] < 0) || (document.getElementById("EB_"+ sensor) == null) || (document.getElementById("EB_circle_"+ sensor) == null))
                    continue;

                var sensor_value = m_temperate[sensor - 1];
                if ((-0xffff >= sensor_value) || (sensor_value >= 0xffff))
                    sensor_value = 0;

                document.getElementById("EB_lo_" + sensor).style.width = normalize(100, width_Img, bodyWidth) + 'px';
                document.getElementById("EB_lo_" + sensor).style.height = normalize(140, height_Img, bodyHeight) + 'px';
                document.getElementById("EB_lo_" + sensor).style.top = normalize(590, height_Img, bodyHeight) + 'px';
                document.getElementById("EB_lo_" + sensor).style.left = normalize(sensor * (18 + 100), width_Img, bodyWidth) + 'px';
                document.getElementById("EB_lo_" + sensor).innerHTML = "Lo." + sensor + "<br>" + Math.floor(sensor_value / 10)  + "." + sensor_value % 10;            
                document.getElementById("EB_lo_" + sensor).classList.add("line-visible");

                document.getElementById("EB_circle_" + sensor).style.top = normalize(EB_pos[sensor - 1][1], height_Img, bodyHeight) + 'px'; 
                document.getElementById("EB_circle_" + sensor).style.left = normalize(EB_pos[sensor - 1][0], width_Img, bodyWidth) + 'px'; 
                document.getElementById("EB_circle_" + sensor).classList.add("line-visible"); 

                var top = -35; left = 20;                      
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
            document.getElementById('title').childNodes[3].style.width = normalize(472, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[3].style.top = normalize(66, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[3].style.left = normalize(0, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[3].childNodes[1].textContent = "Warning level";

            document.getElementById('title').childNodes[5].style.height = normalize(61, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[5].style.width = normalize(157, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[5].style.top = normalize(66, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[5].style.left = normalize(472, width_Img, bodyWidth) + 'px';
            // Active update after 4 seconds
            if ((epochCurrentSeconds - m_timeUpdate) > 4)
            {
                document.getElementById('title').childNodes[5].childNodes[1].innerHTML = Math.floor(m_threshold / 10)  + "." + m_threshold % 10 + " &deg; C";
                document.getElementById('title').childNodes[5].childNodes[1].name = m_threshold;
            }            
            
            document.getElementById('title').childNodes[7].style.height = normalize(60, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[7].style.width = normalize(630, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[7].style.top = normalize(132, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[7].style.left = normalize(0, width_Img, bodyWidth) + 'px';
            // Active update after 4 seconds
            if ((epochCurrentSeconds - m_timeUpdate) > 4)
            {
                document.getElementById('title').childNodes[7].childNodes[1].checked = m_warningFlag;
            }
                
            document.getElementById('title').childNodes[7].childNodes[3].textContent = "Notification of errors";

            document.getElementById('title').childNodes[9].style.height = normalize(90, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[9].style.width = normalize(632, width_Img, bodyWidth) + 'px';
            document.getElementById('title').childNodes[9].style.top = normalize(204, height_Img, bodyHeight) + 'px';
            document.getElementById('title').childNodes[9].style.left = normalize(0, width_Img, bodyWidth) + 'px';

            // Clean textArea
            while (document.getElementById('title').childNodes[9].childNodes[1].firstChild) {
                document.getElementById('title').childNodes[9].childNodes[1].removeChild(document.getElementById('title').childNodes[9].childNodes[1].firstChild);
            }                

            for(var i = 0; i < m_issue.length; i++)
            {
                if (m_issue[i].length > 0)
                {
                    document.getElementById('title').childNodes[9].childNodes[1].appendChild(document.createTextNode(m_issue[i]));                        
                }
            }

            // Auto scroll
            for(var i = 0; i < m_issue.length; i++)
            {
                if(m_issue[i] !== m_issueTemp[i])
                {
                    document.getElementById('title').childNodes[9].childNodes[1].scrollTop = document.getElementById('title').childNodes[9].childNodes[1].scrollHeight;
                    m_issueTemp = m_issue;
                    break;
                }
            }

            if(document.getElementById('title').childNodes[9].childNodes[1].childNodes.length == 0)
            {
                document.getElementById('title').childNodes[9].childNodes[1].textContent = "No data";
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
            var xValues = [];
            for(var i = 0; i< data_chart.length; i++)
            {
                xValues.push(Array.from(data_chart[i])[0]);                                                               
            }
            
            var yValues = [];
            for(var i = 0; i< data_chart.length; i++)
            {
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
                            return (value * 10) > m_threshold ? 'red' : 'rgba(0,0,255,1.0)';
                        }                
                    }]
                },
                options: {
                    title: {
                        display: true,
                        text: 'BIỂU ĐỒ BIẾN ĐỘNG NHIỆT ĐỘ',
                        fontSize: 26
                    },                    
                    legend: {display: false},
                    scales: {
                        xAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: "Thời gian (s)",
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
								labelString: 'Nhiệt độ cảm biến (\u00B0C)',
                                fontSize: 20
                            },
							ticks: {
								// Thiết lập giới hạn tối thiểu và tối đa của trục y
								suggestedMin: -10, // Giá trị tối thiểu của trục y
								suggestedMax: 60, // Giá trị tối đa của trục y
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
                                return 'Nhiệt độ: ' + tooltipItem.yLabel + '\u00B0C';
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
                var verticalDashedLine = document.createElement("div");
                var redCircle = document.createElement("div");
                var locationLabel = document.createElement("label");
                var finalCircle = document.createElement("div");

                // Gán class và id cho mỗi phần tử
                verticalDashedLine.className = "vertical-dashed-line";
                verticalDashedLine.id = "MALL_" + i;

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
                document.body.appendChild(verticalDashedLine);
                document.body.appendChild(redCircle);
                document.body.appendChild(finalCircle);
            }

            for (var i = 1; i <= EB_pos.length; i++) {
                // Tạo phần tử div cho đường thẳng và đường tròn
                var verticalLine = document.createElement("div");
                var redCircle = document.createElement("div");
                var locationLabel = document.createElement("label");
                var finalCircle = document.createElement("div");

                // Gán class và id cho mỗi phần tử
                verticalLine.className = "vertical-line";
                verticalLine.id = "EB_" + i;

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
                document.body.appendChild(verticalLine);
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
                                    m_threshold     = msg.data.locations[i][2];
                                    m_warningFlag   = msg.data.locations[i][1];
                                    m_temperate     = msg.data.locations[i][3]; 
                                    m_issue         = msg.data.locations[i][5]; 

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

            document.getElementById('title').childNodes[9].childNodes[1].checked = false;                      
        });

    </script> 
</body>
</html>
