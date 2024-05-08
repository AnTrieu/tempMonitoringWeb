<!DOCTYPE html>
<html>

<head>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" type="text/css" href="./css/login_page.css?v=1.0.3">
    <script src="js/jquery.min.js"></script>
    <script src="./js/common.js?v1.0.1"></script>

    <script>
        let timeoutMainProcess = 0;
        let restartFlag = -1;
        let test = 3;
    </script>
</head>

<body style="font-family: 'Arial'">
    <div class="container fadeIn" id="container">
        <form method="POST" action="login-page" id="form-login" class="form-login">
			<div id="box1" class="fadeIn second" style="padding-bottom:20px;">
				<img width="100%" src="./img/logo.png">
			</div>	
            
            <!-- Main form-->
            <input type="text" id="username" class="fadeIn third" name="username" placeholder="Account" onfocus="focusFunction()" required>
            <input type="password" id="password" class="fadeIn third" name="password" placeholder="Password" onfocus="focusFunction()" required>
            <input type="button" id="button-confirm" style="cursor: pointer;" class="btn-login fadeIn fourth" value="Login" onclick="login_btn()">
            <div class="form-btn" id="form-btn">
                <a style="font-size: 13px; color: blue; background-color: WhiteSmoke;" href="#" class="fadeIn fourth" onclick="changeForm(1)">Change Password</a>	                    
            </div>	
            

            <input type="text" id="username_change" class="fadeIn second" style="display:none;" name="username" placeholder="Account" onfocus="focusFunction()" required>
            <input type="password" id="password_old" class="fadeIn third" style="display:none;" name="password" placeholder="Old password" onfocus="focusFunction()" required>
			<input type="password" id="password_new" class="fadeIn third" style="display:none;" name="password" placeholder="New password" onfocus="focusFunction()" required>
			<input type="password" id="password_new_comfirm" class="fadeIn third" style="display:none;" name="password" placeholder="New password" onfocus="focusFunction()" required>	   
            <input type="button" id="button-confirm-change" style="cursor: pointer; display:none;" class="btn-login fadeIn fourth" value="Confirm" onclick="actionChangePassword()">                     
            <div id="temp_btn" class="form-btn fadeIn third" style="display:none;">
                <a style="font-size: 13px; color: blue; background-color: WhiteSmoke;" href="#" class="fadeIn fourth" onclick="changeForm(2)">Back</a>                                   
            </div>	  

        </form>

        <div id="wait_div" class="wait_div">
            <div class="loader-outside">
                <div class="loader"></div>
            </div>
        </div>			
	</div>

    <!-- GO! THÁI BÌNH -->
    <div class="div-area" id="TBG_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! THÁI BÌNH</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>	    
    <div class="div-area" id="TBG_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="TBG_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="TBG_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>	    	
    <div class="div-area" id="TBG_btn">
        <button class="transparent-button" onclick="button_process('TBG_page')">GO! THÁI BÌNH</button>
    </div>
    <div class="div-area" id="TBG_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>	

	<!-- GO! HẠ LONG -->
    <div class="div-area" id="HLG_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! HẠ LONG</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>	 
    <div class="div-area" id="HLG_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="HLG_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>
    <div class="div-area" id="HLG_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>    
    <div class="div-area" id="HLG_btn">
        <button class="transparent-button" onclick="button_process('HLG_page')">GO! HẠ LONG</button>
    </div>
    <div class="div-area" id="HLG_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>	
	
	<!-- GO! HẢI DƯƠNG -->
    <div class="div-area" id="HDG_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! HẢI DƯƠNG</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>    
    <div class="div-area" id="HDG_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="HDG_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>		
    <div class="div-area" id="HDG_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>       
    <div class="div-area" id="HDG_btn">
        <button class="transparent-button" onclick="button_process('HDG_page')">GO! HẢI DƯƠNG</button>
    </div>
    <div class="div-area" id="HDG_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>	
	
	<!-- GO! THÁI NGUYÊN -->
    <div class="div-area" id="TNN_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! THÁI NGUYÊN</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>        
    <div class="div-area" id="TNN_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="TNN_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>
    <div class="div-area" id="TNN_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>       		
    <div class="div-area" id="TNN_btn">
        <button class="transparent-button" onclick="button_process('TNN_page')">GO! THÁI NGUYÊN</button>
    </div> 
    <div class="div-area" id="TNN_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>	
	
	<!-- GO! ĐÀ NẴNG -->
    <div class="div-area" id="DNG_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! ĐÀ NẴNG</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>       
    <div class="div-area" id="DNG_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="DNG_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>		
    <div class="div-area" id="DNG_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>       
	<div class="div-area" id="DNG_btn">
        <button class="transparent-button" onclick="button_process('DNG_page')">GO! ĐÀ NẴNG</button>
    </div>
    <div class="div-area" id="DNG_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
	
	<!-- GO! ĐÀ LẠT -->
    <div class="div-area" id="DLT_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! ĐÀ LẠT</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>     
    <div class="div-area" id="DLT_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="DLT_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="DLT_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>      	
	<div class="div-area" id="DLT_btn">
        <button class="transparent-button" onclick="button_process('DLT_page')">GO! ĐÀ LẠT</button>
    </div>
    <div class="div-area" id="DLT_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
	
	<!-- GO! DĨ AN -->
    <div class="div-area" id="DAN_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! DĨ AN</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>        
    <div class="div-area" id="DAN_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="DAN_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>		
    <div class="div-area" id="DAN_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>     
	<div class="div-area" id="DAN_btn">
        <button class="transparent-button" onclick="button_process('DAN_page')">GO! DĨ AN</button>
    </div>
    <div class="div-area" id="DAN_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
    
	<!-- GO! NGUYỄN THỊ THẬP -->
    <div class="div-area" id="NTT_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! NGUYỄN THỊ THẬP</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>        
    <div class="div-area" id="NTT_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="NTT_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="NTT_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>         	
	<div class="div-area" id="NTT_btn">
        <button class="transparent-button" onclick="button_process('NTT_page')">GO! NGUYỄN THỊ THẬP</button>
    </div>
    <div class="div-area" id="NTT_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
    
	<!-- GO! MỸ THO -->
    <div class="div-area" id="MTO_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! MỸ THO</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>       
    <div class="div-area" id="MTO_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="MTO_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="MTO_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>     		
	<div class="div-area" id="MTO_btn">
        <button class="transparent-button" onclick="button_process('MTO_page')">GO! MỸ THO</button>
    </div>
    <div class="div-area" id="MTO_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
    
	<!-- GO! CẦN THƠ -->
    <div class="div-area" id="CTO_label">
        <label class="transparent-label" style="font-size: 0.8vw; color: red;">GO! CẦN THƠ</label>
		<label class="transparent-label" style="font-size: 0.7vw; color: blue;">Inside: 0.0&deg;C - Outside: 0.0&deg;C</label>
    </div>     
    <div class="div-area" id="CTO_MALL_label">
        <label class="transparent-label" style="font-size: 1.2vw;">MALL</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>	
    <div class="div-area" id="CTO_EB_label">
        <label class="transparent-label" style="font-size: 1.2vw;">HYPER</label>
		<label class="transparent-label" style="color: white;"><span>0.0 &deg; C</span>&nbsp - &nbsp<span>0.0 &deg; C</span></label>
    </div>
    <div class="div-area" id="CTO_outsite">
        <label class="transparent-label" style="font-size: 1.0vw;">Outside</label>
        <label class="transparent-label" style="font-size: 0.8vw;">0.0 &deg; C</label>
    </div>     
	<div class="div-area" id="CTO_btn">
        <button class="transparent-button" onclick="button_process('CTO_page')">GO! CẦN THƠ</button>
    </div>
    <div class="div-area" id="CTO_EB_notify">
        <label class="transparent-notify">Normal temperature</label>
    </div>
	
    <script>
        // Private function
        function button_process(type)
        {
            if(type.length > 0)
            {
                var obj = new Object();
                obj.type = "Detail";
                obj.location = type;

                window.parent.postMessage(JSON.stringify(obj), '*');
            }
        }

        function focusFunction()
        {
            document.getElementById('username').style.border = "";
            document.getElementById('password').style.border = "";
            document.getElementById('username_change').style.border = "";
            document.getElementById('password_old').style.border = "";
            document.getElementById('password_new').style.border = "";
            document.getElementById('password_new_comfirm').style.border = "";
        }

        function main_process()
        {
            var img = new Image();
            img.src = document.body.style.backgroundImage.replace(/url\((['"])?(.*?)\1\)/gi, '$2');
            
            img.onload = function() {
                var width_Img = img.width;
                var height_Img = img.height;
                var bodyWidth = document.body.clientWidth;
                var bodyHeight = document.body.clientHeight;
                // console.log(height_Img + " " + bodyHeight);
                var width_map_label = normalize(290, width_Img, bodyWidth);
                var height_map_label = normalize(72, height_Img, bodyHeight);

                var width_label = normalize(242, width_Img, bodyWidth);
                var height_label = normalize(90, height_Img, bodyHeight);

                var width_outsite = normalize(120, width_Img, bodyWidth);
                var height_outsite = normalize(65, height_Img, bodyHeight);

                var width_button = normalize(360, width_Img, bodyWidth);
                var height_button = normalize(65, height_Img, bodyHeight);

                var width_notify = normalize(485 - 10, width_Img, bodyWidth); // padding-left: 10px
                var height_notify = normalize(60, height_Img, bodyHeight);
				
                // ----------------------------- Go Thai Bình ----------------------------- //
				var TBG_label = document.getElementById("TBG_label");
				TBG_label.style.visibility = "visible";
                TBG_label.style.width = width_map_label + "px"; 
                TBG_label.style.height = height_map_label + "px";                      
                TBG_label.style.top = normalize(309, height_Img, bodyHeight) + "px"; 
                TBG_label.style.left = normalize(205, width_Img, bodyWidth) + "px";

				var TBG_MALL_label = document.getElementById("TBG_MALL_label");
				TBG_MALL_label.style.visibility = "visible";
                TBG_MALL_label.style.width = width_label + "px"; 
                TBG_MALL_label.style.height = height_label + "px";                      
                TBG_MALL_label.style.top = normalize(70, height_Img, bodyHeight) + "px"; 
                TBG_MALL_label.style.left = normalize(1683, width_Img, bodyWidth) + "px"; 

				var TBG_EB_label = document.getElementById("TBG_EB_label");
				TBG_EB_label.style.visibility = "visible";
                TBG_EB_label.style.width = width_label + "px"; 
                TBG_EB_label.style.height = height_label + "px";                      
                TBG_EB_label.style.top = normalize(70, height_Img, bodyHeight) + "px"; 
                TBG_EB_label.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var TBG_outsite = document.getElementById("TBG_outsite");
				TBG_outsite.style.visibility = "visible";
                TBG_outsite.style.width = width_outsite + "px"; 
                TBG_outsite.style.height = height_outsite + "px";                      
                TBG_outsite.style.top = normalize(169, height_Img, bodyHeight) + "px"; 
                TBG_outsite.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var TBG_btn = document.getElementById("TBG_btn");
                TBG_btn.style.visibility = "visible";
                TBG_btn.style.width = width_button + "px"; 
                TBG_btn.style.height = height_button + "px";                      
                TBG_btn.style.top = normalize(169, height_Img, bodyHeight) + "px"; 
                TBG_btn.style.left = normalize(1570, width_Img, bodyWidth) + "px";
				
				var TBG_EB_notify = document.getElementById("TBG_EB_notify");
				TBG_EB_notify.style.visibility = "visible";
                TBG_EB_notify.style.width = width_notify + "px"; 
                TBG_EB_notify.style.height = height_notify + "px";                      
                TBG_EB_notify.style.top = normalize(244, height_Img, bodyHeight) + "px"; 
                TBG_EB_notify.style.left = normalize(1436, width_Img, bodyWidth) + "px"; 				
                // ------------------------------------------------------------------------ //
				
				// ----------------------------- Go Ha Long ------------------------------- //
				var HLG_label = document.getElementById("HLG_label");
				HLG_label.style.visibility = "visible";
                HLG_label.style.width = width_map_label + "px"; 
                HLG_label.style.height = height_map_label + "px";                      
                HLG_label.style.top = normalize(166, height_Img, bodyHeight) + "px"; 
                HLG_label.style.left = normalize(942, width_Img, bodyWidth) + "px";

				var HLG_MALL_label = document.getElementById("HLG_MALL_label");
				HLG_MALL_label.style.visibility = "visible";
                HLG_MALL_label.style.width = width_label + "px"; 
                HLG_MALL_label.style.height = height_label + "px";                      
                HLG_MALL_label.style.top = normalize(70, height_Img, bodyHeight) + "px"; 
                HLG_MALL_label.style.left = normalize(2213, width_Img, bodyWidth) + "px"; 

				var HLG_EB_label = document.getElementById("HLG_EB_label");
				HLG_EB_label.style.visibility = "visible";
                HLG_EB_label.style.width = width_label + "px"; 
                HLG_EB_label.style.height = height_label + "px";                      
                HLG_EB_label.style.top = normalize(70, height_Img, bodyHeight) + "px"; 
                HLG_EB_label.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var HLG_outsite = document.getElementById("HLG_outsite");
				HLG_outsite.style.visibility = "visible";
                HLG_outsite.style.width = width_outsite + "px"; 
                HLG_outsite.style.height = height_outsite + "px";                      
                HLG_outsite.style.top = normalize(169, height_Img, bodyHeight) + "px"; 
                HLG_outsite.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var HLG_btn = document.getElementById("HLG_btn");
                HLG_btn.style.visibility = "visible";
                HLG_btn.style.width = width_button + "px"; 
                HLG_btn.style.height = height_button + "px";                      
                HLG_btn.style.top = normalize(169, height_Img, bodyHeight) + "px"; 
                HLG_btn.style.left = normalize(2099, width_Img, bodyWidth) + "px";    

				var HLG_EB_notify = document.getElementById("HLG_EB_notify");
				HLG_EB_notify.style.visibility = "visible";
                HLG_EB_notify.style.width = width_notify + "px"; 
                HLG_EB_notify.style.height = height_notify + "px";                      
                HLG_EB_notify.style.top = normalize(244, height_Img, bodyHeight) + "px"; 
                HLG_EB_notify.style.left = normalize(1966, width_Img, bodyWidth) + "px"; 					
                // ------------------------------------------------------------------------ //
				
				// ----------------------------- Go Hai Duong ----------------------------- //
				var HDG_label = document.getElementById("HDG_label");
				HDG_label.style.visibility = "visible";
                HDG_label.style.width = width_map_label + "px"; 
                HDG_label.style.height = height_map_label + "px";                      
                HDG_label.style.top = normalize(260, height_Img, bodyHeight) + "px"; 
                HDG_label.style.left = normalize(940, width_Img, bodyWidth) + "px";

				var HDG_MALL_label = document.getElementById("HDG_MALL_label");
				HDG_MALL_label.style.visibility = "visible";
                HDG_MALL_label.style.width = width_label + "px"; 
                HDG_MALL_label.style.height = height_label + "px";                      
                HDG_MALL_label.style.top = normalize(338, height_Img, bodyHeight) + "px"; 
                HDG_MALL_label.style.left = normalize(1683, width_Img, bodyWidth) + "px"; 

				var HDG_EB_label = document.getElementById("HDG_EB_label");
				HDG_EB_label.style.visibility = "visible";
                HDG_EB_label.style.width = width_label + "px"; 
                HDG_EB_label.style.height = height_label + "px";                      
                HDG_EB_label.style.top = normalize(338, height_Img, bodyHeight) + "px"; 
                HDG_EB_label.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var HDG_outsite = document.getElementById("HDG_outsite");
				HDG_outsite.style.visibility = "visible";
                HDG_outsite.style.width = width_outsite + "px"; 
                HDG_outsite.style.height = height_outsite + "px";                      
                HDG_outsite.style.top = normalize(433, height_Img, bodyHeight) + "px"; 
                HDG_outsite.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var HDG_btn = document.getElementById("HDG_btn");
                HDG_btn.style.visibility = "visible";
                HDG_btn.style.width = width_button + "px"; 
                HDG_btn.style.height = height_button + "px";                      
                HDG_btn.style.top = normalize(433, height_Img, bodyHeight) + "px"; 
                HDG_btn.style.left = normalize(1570, width_Img, bodyWidth) + "px";       

				var HDG_EB_notify = document.getElementById("HDG_EB_notify");
				HDG_EB_notify.style.visibility = "visible";
                HDG_EB_notify.style.width = width_notify + "px"; 
                HDG_EB_notify.style.height = (height_notify + 4) + "px";  // Add delta                    
                HDG_EB_notify.style.top = normalize(505, height_Img, bodyHeight) + "px"; 
                HDG_EB_notify.style.left = normalize(1436, width_Img, bodyWidth) + "px"; 
                // ------------------------------------------------------------------------ //
				
                // ----------------------------- Go Thai Nguyen ----------------------------- //
				var TNN_label = document.getElementById("TNN_label");
				TNN_label.style.visibility = "visible";
                TNN_label.style.width = width_map_label + "px"; 
                TNN_label.style.height = height_map_label + "px";                      
                TNN_label.style.top = normalize(142, height_Img, bodyHeight) + "px"; 
                TNN_label.style.left = normalize(89, width_Img, bodyWidth) + "px";

				var TNN_MALL_label = document.getElementById("TNN_MALL_label");
				TNN_MALL_label.style.visibility = "visible";
                TNN_MALL_label.style.width = width_label + "px"; 
                TNN_MALL_label.style.height = height_label + "px";                      
                TNN_MALL_label.style.top = normalize(338, height_Img, bodyHeight) + "px"; 
                TNN_MALL_label.style.left = normalize(2213, width_Img, bodyWidth) + "px"; 

				var TNN_EB_label = document.getElementById("TNN_EB_label");
				TNN_EB_label.style.visibility = "visible";
                TNN_EB_label.style.width = width_label + "px"; 
                TNN_EB_label.style.height = height_label + "px";                      
                TNN_EB_label.style.top = normalize(338, height_Img, bodyHeight) + "px"; 
                TNN_EB_label.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var TNN_outsite = document.getElementById("TNN_outsite");
				TNN_outsite.style.visibility = "visible";
                TNN_outsite.style.width = width_outsite + "px"; 
                TNN_outsite.style.height = height_outsite + "px";                      
                TNN_outsite.style.top = normalize(433, height_Img, bodyHeight) + "px"; 
                TNN_outsite.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var TNN_btn = document.getElementById("TNN_btn");
                TNN_btn.style.visibility = "visible";
                TNN_btn.style.width = width_button + "px"; 
                TNN_btn.style.height = height_button + "px";                      
                TNN_btn.style.top = normalize(433, height_Img, bodyHeight) + "px"; 
                TNN_btn.style.left = normalize(2099, width_Img, bodyWidth) + "px"; 

                var TNN_EB_notify = document.getElementById("TNN_EB_notify");
				TNN_EB_notify.style.visibility = "visible";
                TNN_EB_notify.style.width = width_notify + "px"; 
                TNN_EB_notify.style.height = height_notify + "px";                      
                TNN_EB_notify.style.top = normalize(505, height_Img, bodyHeight) + "px"; 
                TNN_EB_notify.style.left = normalize(1966, width_Img, bodyWidth) + "px"; 
                // ------------------------------------------------------------------------ //

                // ----------------------------- Go Da Nang ------------------------------- //
				var DNG_label = document.getElementById("DNG_label");
				DNG_label.style.visibility = "visible";
                DNG_label.style.width = width_map_label + "px"; 
                DNG_label.style.height = height_map_label + "px";                      
                DNG_label.style.top = normalize(668, height_Img, bodyHeight) + "px"; 
                DNG_label.style.left = normalize(1005, width_Img, bodyWidth) + "px";

				var DNG_MALL_label = document.getElementById("DNG_MALL_label");
				DNG_MALL_label.style.visibility = "visible";
                DNG_MALL_label.style.width = width_label + "px"; 
                DNG_MALL_label.style.height = height_label + "px";                      
                DNG_MALL_label.style.top = normalize(610, height_Img, bodyHeight) + "px"; 
                DNG_MALL_label.style.left = normalize(1683, width_Img, bodyWidth) + "px"; 

				var DNG_EB_label = document.getElementById("DNG_EB_label");
				DNG_EB_label.style.visibility = "visible";
                DNG_EB_label.style.width = width_label + "px"; 
                DNG_EB_label.style.height = height_label + "px";                      
                DNG_EB_label.style.top = normalize(610, height_Img, bodyHeight) + "px"; 
                DNG_EB_label.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var DNG_outsite = document.getElementById("DNG_outsite");
				DNG_outsite.style.visibility = "visible";
                DNG_outsite.style.width = width_outsite + "px"; 
                DNG_outsite.style.height = height_outsite + "px";                      
                DNG_outsite.style.top = normalize(707, height_Img, bodyHeight) + "px"; 
                DNG_outsite.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var DNG_btn = document.getElementById("DNG_btn");
                DNG_btn.style.visibility = "visible";
                DNG_btn.style.width = width_button + "px"; 
                DNG_btn.style.height = height_button + "px";                      
                DNG_btn.style.top = normalize(707, height_Img, bodyHeight) + "px"; 
                DNG_btn.style.left = normalize(1570, width_Img, bodyWidth) + "px";

				var DNG_EB_notify = document.getElementById("DNG_EB_notify");
				DNG_EB_notify.style.visibility = "visible";
                DNG_EB_notify.style.width = width_notify + "px"; 
                DNG_EB_notify.style.height = (height_notify + 4) + "px";  // Add delta                    
                DNG_EB_notify.style.top = normalize(780, height_Img, bodyHeight) + "px"; 
                DNG_EB_notify.style.left = normalize(1436, width_Img, bodyWidth) + "px";                 
                // ------------------------------------------------------------------------ //
                
                // ----------------------------- Go Da Lat -------------------------------- //
				var DLT_label = document.getElementById("DLT_label");
				DLT_label.style.visibility = "visible";
                DLT_label.style.width = width_map_label + "px"; 
                DLT_label.style.height = height_map_label + "px";                      
                DLT_label.style.top = normalize(1045, height_Img, bodyHeight) + "px"; 
                DLT_label.style.left = normalize(1066, width_Img, bodyWidth) + "px";

				var DLT_MALL_label = document.getElementById("DLT_MALL_label");
				DLT_MALL_label.style.visibility = "visible";
                DLT_MALL_label.style.width = width_label + "px"; 
                DLT_MALL_label.style.height = height_label + "px";                      
                DLT_MALL_label.style.top = normalize(610, height_Img, bodyHeight) + "px"; 
                DLT_MALL_label.style.left = normalize(2213, width_Img, bodyWidth) + "px"; 

				var DLT_EB_label = document.getElementById("DLT_EB_label");
				DLT_EB_label.style.visibility = "visible";
                DLT_EB_label.style.width = width_label + "px"; 
                DLT_EB_label.style.height = height_label + "px";                      
                DLT_EB_label.style.top = normalize(610, height_Img, bodyHeight) + "px"; 
                DLT_EB_label.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var DLT_outsite = document.getElementById("DLT_outsite");
				DLT_outsite.style.visibility = "visible";
                DLT_outsite.style.width = width_outsite + "px"; 
                DLT_outsite.style.height = height_outsite + "px";                      
                DLT_outsite.style.top = normalize(707, height_Img, bodyHeight) + "px"; 
                DLT_outsite.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var DLT_btn = document.getElementById("DLT_btn");
                DLT_btn.style.visibility = "visible";
                DLT_btn.style.width = width_button + "px"; 
                DLT_btn.style.height = height_button + "px";                      
                DLT_btn.style.top = normalize(707, height_Img, bodyHeight) + "px"; 
                DLT_btn.style.left = normalize(2099, width_Img, bodyWidth) + "px";

                var DLT_EB_notify = document.getElementById("DLT_EB_notify");
				DLT_EB_notify.style.visibility = "visible";
                DLT_EB_notify.style.width = width_notify + "px"; 
                DLT_EB_notify.style.height = height_notify + "px";                      
                DLT_EB_notify.style.top = normalize(780, height_Img, bodyHeight) + "px"; 
                DLT_EB_notify.style.left = normalize(1966, width_Img, bodyWidth) + "px"; 
                // ------------------------------------------------------------------------ //

                // ----------------------------- Go Di An --------------------------------- //
				var DAN_label = document.getElementById("DAN_label");
				DAN_label.style.visibility = "visible";
                DAN_label.style.width = width_map_label + "px"; 
                DAN_label.style.height = height_map_label + "px";                      
                DAN_label.style.top = normalize(1142, height_Img, bodyHeight) + "px"; 
                DAN_label.style.left = normalize(305, width_Img, bodyWidth) + "px";

				var DAN_MALL_label = document.getElementById("DAN_MALL_label");
				DAN_MALL_label.style.visibility = "visible";
                DAN_MALL_label.style.width = width_label + "px"; 
                DAN_MALL_label.style.height = height_label + "px";                      
                DAN_MALL_label.style.top = normalize(880, height_Img, bodyHeight) + "px"; 
                DAN_MALL_label.style.left = normalize(1683, width_Img, bodyWidth) + "px"; 

				var DAN_EB_label = document.getElementById("DAN_EB_label");
				DAN_EB_label.style.visibility = "visible";
                DAN_EB_label.style.width = width_label + "px"; 
                DAN_EB_label.style.height = height_label + "px";                      
                DAN_EB_label.style.top = normalize(880, height_Img, bodyHeight) + "px"; 
                DAN_EB_label.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var DAN_outsite = document.getElementById("DAN_outsite");
				DAN_outsite.style.visibility = "visible";
                DAN_outsite.style.width = width_outsite + "px"; 
                DAN_outsite.style.height = height_outsite + "px";                      
                DAN_outsite.style.top = normalize(975, height_Img, bodyHeight) + "px"; 
                DAN_outsite.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var DAN_btn = document.getElementById("DAN_btn");
                DAN_btn.style.visibility = "visible";
                DAN_btn.style.width = width_button + "px"; 
                DAN_btn.style.height = height_button + "px";                      
                DAN_btn.style.top = normalize(975, height_Img, bodyHeight) + "px"; 
                DAN_btn.style.left = normalize(1570, width_Img, bodyWidth) + "px";

				var DAN_EB_notify = document.getElementById("DAN_EB_notify");
				DAN_EB_notify.style.visibility = "visible";
                DAN_EB_notify.style.width = width_notify + "px"; 
                DAN_EB_notify.style.height = (height_notify + 4) + "px";  // Add delta                    
                DAN_EB_notify.style.top = normalize(1050, height_Img, bodyHeight) + "px"; 
                DAN_EB_notify.style.left = normalize(1436, width_Img, bodyWidth) + "px";                
                // ------------------------------------------------------------------------ //
                
                // ----------------------------- Go Nguyen Thi Thap ----------------------- //
				var NTT_label = document.getElementById("NTT_label");
				NTT_label.style.visibility = "visible";
                NTT_label.style.width = width_map_label + "px"; 
                NTT_label.style.height = height_map_label + "px";                      
                NTT_label.style.top = normalize(1186, height_Img, bodyHeight) + "px"; 
                NTT_label.style.left = normalize(1018, width_Img, bodyWidth) + "px";

				var NTT_MALL_label = document.getElementById("NTT_MALL_label");
				NTT_MALL_label.style.visibility = "visible";
                NTT_MALL_label.style.width = width_label + "px"; 
                NTT_MALL_label.style.height = height_label + "px";                      
                NTT_MALL_label.style.top = normalize(880, height_Img, bodyHeight) + "px"; 
                NTT_MALL_label.style.left = normalize(2213, width_Img, bodyWidth) + "px"; 

				var NTT_EB_label = document.getElementById("NTT_EB_label");
				NTT_EB_label.style.visibility = "visible";
                NTT_EB_label.style.width = width_label + "px"; 
                NTT_EB_label.style.height = height_label + "px";                      
                NTT_EB_label.style.top = normalize(880, height_Img, bodyHeight) + "px"; 
                NTT_EB_label.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var NTT_outsite = document.getElementById("NTT_outsite");
				NTT_outsite.style.visibility = "visible";
                NTT_outsite.style.width = width_outsite + "px"; 
                NTT_outsite.style.height = height_outsite + "px";                      
                NTT_outsite.style.top = normalize(975, height_Img, bodyHeight) + "px"; 
                NTT_outsite.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var NTT_btn = document.getElementById("NTT_btn");
                NTT_btn.style.visibility = "visible";
                NTT_btn.style.width = width_button + "px"; 
                NTT_btn.style.height = height_button + "px";                      
                NTT_btn.style.top = normalize(975, height_Img, bodyHeight) + "px"; 
                NTT_btn.style.left = normalize(2099, width_Img, bodyWidth) + "px";    
                
                var NTT_EB_notify = document.getElementById("NTT_EB_notify");
				NTT_EB_notify.style.visibility = "visible";
                NTT_EB_notify.style.width = width_notify + "px"; 
                NTT_EB_notify.style.height = height_notify + "px";                      
                NTT_EB_notify.style.top = normalize(1050, height_Img, bodyHeight) + "px"; 
                NTT_EB_notify.style.left = normalize(1966, width_Img, bodyWidth) + "px";                 
                // ------------------------------------------------------------------------ //

                // ----------------------------- Go My Tho -------------------------------- //
				var MTO_label = document.getElementById("MTO_label");
				MTO_label.style.visibility = "visible";
                MTO_label.style.width = width_map_label + "px"; 
                MTO_label.style.height = height_map_label + "px";                      
                MTO_label.style.top = normalize(1297, height_Img, bodyHeight) + "px"; 
                MTO_label.style.left = normalize(885, width_Img, bodyWidth) + "px";

				var MTO_MALL_label = document.getElementById("MTO_MALL_label");
				MTO_MALL_label.style.visibility = "visible";
                MTO_MALL_label.style.width = width_label + "px"; 
                MTO_MALL_label.style.height = height_label + "px";                      
                MTO_MALL_label.style.top = normalize(1145, height_Img, bodyHeight) + "px"; 
                MTO_MALL_label.style.left = normalize(1683, width_Img, bodyWidth) + "px"; 

				var MTO_EB_label = document.getElementById("MTO_EB_label");
				MTO_EB_label.style.visibility = "visible";
                MTO_EB_label.style.width = width_label + "px"; 
                MTO_EB_label.style.height = height_label + "px";                      
                MTO_EB_label.style.top = normalize(1145, height_Img, bodyHeight) + "px"; 
                MTO_EB_label.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var MTO_outsite = document.getElementById("MTO_outsite");
				MTO_outsite.style.visibility = "visible";
                MTO_outsite.style.width = width_outsite + "px"; 
                MTO_outsite.style.height = height_outsite + "px";                      
                MTO_outsite.style.top = normalize(1240, height_Img, bodyHeight) + "px"; 
                MTO_outsite.style.left = normalize(1432, width_Img, bodyWidth) + "px"; 

                var MTO_btn = document.getElementById("MTO_btn");
                MTO_btn.style.visibility = "visible";
                MTO_btn.style.width = width_button + "px"; 
                MTO_btn.style.height = height_button + "px";                      
                MTO_btn.style.top = normalize(1240, height_Img, bodyHeight) + "px"; 
                MTO_btn.style.left = normalize(1570, width_Img, bodyWidth) + "px";

				var MTO_EB_notify = document.getElementById("MTO_EB_notify");
				MTO_EB_notify.style.visibility = "visible";
                MTO_EB_notify.style.width = width_notify + "px"; 
                MTO_EB_notify.style.height = (height_notify + 4) + "px";  // Add delta                    
                MTO_EB_notify.style.top = normalize(1315, height_Img, bodyHeight) + "px"; 
                MTO_EB_notify.style.left = normalize(1436, width_Img, bodyWidth) + "px";                 
                // ------------------------------------------------------------------------ //

                // ----------------------------- Go Can Tho ------------------------------- //
				var CTO_label = document.getElementById("CTO_label");
				CTO_label.style.visibility = "visible";
                CTO_label.style.width = width_map_label + "px"; 
                CTO_label.style.height = height_map_label + "px";                      
                CTO_label.style.top = normalize(1260, height_Img, bodyHeight) + "px"; 
                CTO_label.style.left = normalize(202, width_Img, bodyWidth) + "px";

				var CTO_MALL_label = document.getElementById("CTO_MALL_label");
				CTO_MALL_label.style.visibility = "visible";
                CTO_MALL_label.style.width = width_label + "px"; 
                CTO_MALL_label.style.height = height_label + "px";                      
                CTO_MALL_label.style.top = normalize(1145, height_Img, bodyHeight) + "px"; 
                CTO_MALL_label.style.left = normalize(2213, width_Img, bodyWidth) + "px"; 

				var CTO_EB_label = document.getElementById("CTO_EB_label");
				CTO_EB_label.style.visibility = "visible";
                CTO_EB_label.style.width = width_label + "px"; 
                CTO_EB_label.style.height = height_label + "px";                      
                CTO_EB_label.style.top = normalize(1145, height_Img, bodyHeight) + "px"; 
                CTO_EB_label.style.left = normalize(1962, width_Img, bodyWidth) + "px";

                var CTO_outsite = document.getElementById("CTO_outsite");
				CTO_outsite.style.visibility = "visible";
                CTO_outsite.style.width = width_outsite + "px"; 
                CTO_outsite.style.height = height_outsite + "px";                      
                CTO_outsite.style.top = normalize(1240, height_Img, bodyHeight) + "px"; 
                CTO_outsite.style.left = normalize(1962, width_Img, bodyWidth) + "px"; 

                var CTO_btn = document.getElementById("CTO_btn");
                CTO_btn.style.visibility = "visible";
                CTO_btn.style.width = width_button + "px"; 
                CTO_btn.style.height = height_button + "px";                      
                CTO_btn.style.top = normalize(1240, height_Img, bodyHeight) + "px"; 
                CTO_btn.style.left = normalize(2099, width_Img, bodyWidth) + "px";

                var CTO_EB_notify = document.getElementById("CTO_EB_notify");
				CTO_EB_notify.style.visibility = "visible";
                CTO_EB_notify.style.width = width_notify + "px"; 
                CTO_EB_notify.style.height = height_notify + "px";                      
                CTO_EB_notify.style.top = normalize(1315, height_Img, bodyHeight) + "px"; 
                CTO_EB_notify.style.left = normalize(1966, width_Img, bodyWidth) + "px";                     
                // ------------------------------------------------------------------------ //    
            };
        }

        function actionChangePassword() 
        {
            var flagError = false;
		
            if(document.getElementById("username_change").value.length == 0)
            {
                document.getElementById("username_change").style.border = "1px solid red";
                flagError = true;
            }
            if(document.getElementById("password_old").value.length == 0)
            {
                document.getElementById("password_old").style.border = "1px solid red";
                flagError = true;
            }	
            if(document.getElementById("password_new").value.length == 0)
            {
                document.getElementById("password_new").style.border = "1px solid red";
                flagError = true;
            }		
            if(document.getElementById("password_new_comfirm").value.length == 0)
            {
                document.getElementById("password_new_comfirm").style.border = "1px solid red";
                flagError = true;
            }	
            
            if(document.getElementById("password_new").value.localeCompare(document.getElementById("password_new_comfirm").value) != 0)
            {
                document.getElementById("password_new").style.border = "1px solid red";
                document.getElementById("password_new_comfirm").style.border = "1px solid red";
                flagError = true;
            }

            if(!flagError)
            {
                // Active wait popup
                document.getElementById("wait_div").style.visibility = 'visible';
                        
                $.ajax({
                    type: 'POST',
                    url: "./php/change_password.php",
                    data: {
                        'username': document.getElementById("username_change").value,
                        'password_old': document.getElementById("password_old").value,
                        'password_new': document.getElementById("password_new").value
                    },
                    success: function(data) {
                        //console.log(data);
                        // Login fail => back to menu login
                        document.getElementById("wait_div").style.visibility = 'hidden';
    
                        setTimeout(function() {
                            if(data.length == 0)
                            {
                                alert("Update successful");
                                
                                changeForm(2);
                            }
                            else if((data.localeCompare("error") == 0) || (data.localeCompare("error info") == 0))
                            {
                                alert("Update failed");
                            }
                        }, 50);	
                    }
                });
            }            
        }

        function login_btn() 
        {
            var obj = new Object();
            obj.type = "Login";
            obj.username = document.getElementById("username").value;
            obj.password = document.getElementById("password").value;

            window.parent.postMessage(JSON.stringify(obj), '*');
        }

        function changeForm(type)
        {
            if(type == 1)
            {
                document.getElementById("username").style.display = "none";
		        document.getElementById("password").style.display = "none";
		        document.getElementById("form-btn").style.display = "none";
                document.getElementById("button-confirm").style.display = "none";

                document.getElementById('username_change').style.display = "";
                document.getElementById('password_old').style.display = "";
                document.getElementById('password_new').style.display = "";
                document.getElementById('password_new_comfirm').style.display = "";
                document.getElementById("temp_btn").style.display = "";
                document.getElementById("button-confirm-change").style.display = "";                
            }
            else if(type == 2)
            {
                document.getElementById("username").style.display = "";
		        document.getElementById("password").style.display = "";
		        document.getElementById("form-btn").style.display = "";
                document.getElementById("button-confirm").style.display = "";

                document.getElementById('username_change').style.display = "none";
                document.getElementById('password_old').style.display = "none";
                document.getElementById('password_new').style.display = "none";
                document.getElementById('password_new_comfirm').style.display = "none";
                document.getElementById("temp_btn").style.display = "none";
                document.getElementById("button-confirm-change").style.display = "none";  
            }
        }  

        window.addEventListener("keypress", function actionNewFolderByKeyEnter(event) {
            if (event.keyCode == "13") {
                login_btn();
            }
        });

        window.addEventListener("click", function(event) {
            var obj = new Object();
            obj.type = "Keep-Alive";

            window.parent.postMessage(JSON.stringify(obj), '*');
        });

        window.addEventListener('message', function(event) {	
            if (event.data.length > 0) {	
                var obj = JSON.parse(event.data);
                
                // Message MQTT
				if (obj.topic != null)
				{
                    var msg = JSON.parse(obj.message);
                    if (msg.command.localeCompare("notify_data") == 0)
                    {
                        var currentTime = new Date();

                        // Reinit label
                        if(restartFlag >= 0)
                        {
                            for(var i = 0; i < msg.data.locations.length; i++)
                            {
                                if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("middle"))
                                    document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("middle")
                                if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("overload"))
                                    document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("overload")   
                            }

                            restartFlag = -1;
                        }

                        for(var i = 0; i < msg.data.locations.length; i++)
                        {
                            var location        = msg.data.locations[i][0];
                            var max_MALL        = -0xffff
                            var min_MALL        = 0xffff;
                            var max_EB          = -0xffff
                            var min_EB          = 0xffff;
                            var element_MALL    = document.getElementById(location + "_MALL_label");
                            var element_EB      = document.getElementById(location + "_EB_label");
                            var element_outsite = document.getElementById(location + "_outsite");
                            var element_label   = document.getElementById(location + "_label");
                            

                            if(element_MALL != null && element_EB != null && element_outsite != null && element_label != null)
                            {
                                // Find temperate max and min in group 
                                for(var j = 0; j < msg.data.locations[i][3].length; j++)
                                {
                                    var sensor = msg.data.locations[i][3][j];

                                    if ((-0xffff < sensor) && (sensor < 0xffff))
                                    {
                                        if(j < 5 && sensor < min_EB)
                                        {
                                            min_EB = sensor;
                                        }
                                        else if(j < 10 && sensor < min_MALL)
                                        {
                                            min_MALL = sensor;
                                        }                                         

                                        if(j < 5 && sensor > max_EB)
                                        {
                                            max_EB = sensor;
                                        }
                                        else if(j < 10 && sensor > max_MALL)
                                        {
                                            max_MALL = sensor;
                                        }                                          
                                    }
                                }
                                
                                // Not found value
                                if(max_MALL == -0xffff)
                                    max_MALL = 0;
                                if(min_MALL == 0xffff)
                                    min_MALL = 0;
                                if(max_EB == -0xffff)
                                    max_EB = 0;
                                if(min_EB == 0xffff)
                                    min_EB = 0;      

                                // Update value MALL
                                element_MALL.childNodes[3].childNodes[2].innerHTML = Math.floor(min_MALL / 10)  + "." + min_MALL % 10 + " &deg; C";
                                element_MALL.childNodes[3].childNodes[0].innerHTML = Math.floor(max_MALL / 10)  + "." + max_MALL % 10 + " &deg; C";   
                                
                                // Update value EB
                                element_EB.childNodes[3].childNodes[2].innerHTML = Math.floor(min_EB / 10)  + "." + min_EB % 10 + " &deg; C";
                                element_EB.childNodes[3].childNodes[0].innerHTML = Math.floor(max_EB / 10)  + "." + max_EB % 10 + " &deg; C";
                                
                                // Update value outsite
                                var sensor_out = msg.data.locations[i][3][msg.data.locations[i][3].length - 1];
                                if ((-0xffff >= sensor_out) || (sensor_out >= 0xffff))
                                    sensor_out = 0;
                                element_outsite.childNodes[3].innerHTML = Math.floor(sensor_out / 10)  + "." + sensor_out % 10 + " &deg; C";                                    

                                // Update value map
                                var sensor_in = min_MALL;
                                if(min_EB < min_MALL)
                                    sensor_in = min_EB;                                
                                element_label.childNodes[3].innerHTML = "Inside: " + Math.floor(sensor_in / 10)  + "." + sensor_in % 10 + "&deg;C - Outside: " + Math.floor(sensor_out / 10)  + "." + sensor_out % 10 + "&deg;C";
                            
                                // Update status warning to idle state
                                document.getElementById(msg.data.locations[i][0] + "_EB_notify").childNodes[1].innerText = "Normal temperature";

                                // Only Warning from 8h -> 22h
                                if (false || (8 <= currentTime.getHours() && currentTime.getHours() < 22))
                                {
                                    var flagIgnore = false;
                                    for(var j = 0; j < (msg.data.locations[i][3].length - 1); j++)
                                    {                                        
                                        if((-0xffff < msg.data.locations[i][3][j]) && (msg.data.locations[i][3][j] < 0xffff) && (msg.data.locations[i][3][j] >= (msg.data.locations[i][2] + 20)))
                                        {
                                            if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("middle"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("middle")
                                            if (!document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("overload"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.add("overload")

                                            document.getElementById(msg.data.locations[i][0] + "_EB_notify").childNodes[1].innerHTML = "Warning level: " + Math.floor((msg.data.locations[i][2] + 20) / 10)  + "." + (msg.data.locations[i][2] + 20) % 10 + "&deg;C";
                                            break;
                                        }
                                        else if((-0xffff < msg.data.locations[i][3][j]) && (msg.data.locations[i][3][j] < 0xffff) && (msg.data.locations[i][2] < msg.data.locations[i][3][j]) && (msg.data.locations[i][3][j] < (msg.data.locations[i][2] + 20)))
                                        {
                                            if (!document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("middle"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.add("middle")                                                
                                            if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("overload"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("overload")
                                                
                                            flagIgnore = true;
                                        }

                                        if (!flagIgnore && (j == (msg.data.locations[i][3].length - 2)))
                                        {         
                                            if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("middle"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("middle")
                                            if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("overload"))
                                                document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("overload")   
                                        }
                                    }
                                }
                                else
                                {
                                    if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("middle"))
                                        document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("middle")
                                    if (document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.contains("overload"))
                                        document.getElementById(msg.data.locations[i][0] + "_btn").childNodes[1].classList.remove("overload")                                             
                                }
                            }
                        }
                    }
                    
				}

                // Message internal
				else 
				{
                    if (obj.type.localeCompare("Request-Login") == 0)
                    {
                        login_btn();
                    }
                    else if ((obj.type.localeCompare("Re-login") == 0) || 
                             (obj.type.localeCompare("Login-Failed") == 0))
                    {
                        document.getElementById("container").style.visibility = 'visible';
                        document.getElementById("wait_div").style.visibility = 'hidden';

                        if (obj.type.localeCompare("Login-Failed") == 0)
                        {
                            alert("Login failed");

                            var obj         = new Object();
                            obj.type        = "Logout";

                            window.parent.postMessage(JSON.stringify(obj), '*');                            
                        }
                    }
                    else if (obj.type.localeCompare("Login-Passed") == 0)
                    {
                        // Store information
                        $.ajax({
                            type: "POST",
                            url: "./php/session-login.php",
                            data: {
                                'username': sessionStorage.getItem("username"),
                                'password': sessionStorage.getItem("password")
                            },
                            success: function(data) {
                                document.getElementById("container").style.visibility = 'hidden';
                                document.getElementById("wait_div").style.visibility = 'hidden';

                                document.body.style.backgroundImage = "url('./img/background_theme.png')";

                                var obj         = new Object();
                                obj.type        = "Show-User-info";
                                obj.typeUser    = JSON.parse(data)[4].length

                                window.parent.postMessage(JSON.stringify(obj), '*');

                                main_process();

                                restartFlag = 0;
                            }
                        });	
                    }
                    else if (obj.type.localeCompare("Reload") == 0)
                    {
                        main_process();
                    }
                    else if (obj.type.localeCompare("Login-Error") == 0)
                    {                      
                        if (obj.username.length == 0)
                        {
                            document.getElementById('username').style.border = "1px solid red";
                        }
                        if (obj.password.length == 0)
                        {
                            document.getElementById('password').style.border = "1px solid red";
                        }                        
                    }
                    else if (obj.type.localeCompare("Loading") == 0)
                    {
                        // Active wait popup
                        document.getElementById("container").style.visibility = 'hidden';
                        document.getElementById("wait_div").style.visibility = 'visible';                        
                    }			
				}
            }
        });        
        </script>    
</body>

</html>