<!DOCTYPE html>
<html>

<head>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="shortcut icon" href="#">
    <link rel="stylesheet" type="text/css" href="./css/common.css?v=1.0.13">
    <link rel="stylesheet" type="text/css" href="./css/main_page.css?v=1.0.14">
    <script src="./js/stomp.js?v=1.0.0"></script>
    <script src="./js/login.js?v=1.0.0"></script>
    <script src="./js/jquery.min.js"></script>
	
    <script>
        let timeoutLogin = -1;
        let timeoutLoginProcess = -1;
        let timeoutSession = -1;
        let timestamp = -1;
        let typeUser = -1;
        let allow_location = [];
        let connected = false;
    </script>
</head>

<body style="font-family: 'Arial'">
    <div class="container">
		<iframe style="border:none;display:none;" id="login_page" width="100%" height="100%" src="./login-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="user_page" width="100%" height="100%" src="./user-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="TBG_page" width="100%" height="100%" src="./TBG-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="DLT_page" width="100%" height="100%" src="./DLT-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="DNG_page" width="100%" height="100%" src="./DNG-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="HLG_page" width="100%" height="100%" src="./HLG-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="DAN_page" width="100%" height="100%" src="./DAN-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="HDG_page" width="100%" height="100%" src="./HDG-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="TNN_page" width="100%" height="100%" src="./TNN-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="NTT_page" width="100%" height="100%" src="./NTT-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="MTO_page" width="100%" height="100%" src="./MTO-page" scrolling="no" frameborder="0"></iframe>
        <iframe style="border:none;display:none;" id="CTO_page" width="100%" height="100%" src="./CTO-page" scrolling="no" frameborder="0"></iframe>
	</div>

    <div class="sec-center" id="user-profile"> 	         
        <input class="dropdown" type="checkbox" id="dropdown" name="dropdown"/>
        <label class="for-dropdown" for="dropdown" id="username-profile"></label>
        <div class="section-dropdown" id="section-dropdown" > 
            <a href="#" onclick="switch_user_form()" style="display: none;">Account</a>
            <a href="#" onclick="
                
                if (document.body.classList.contains('portraitClass')) {
                    document.body.classList.remove('portraitClass');
                }          

                exitFullScreen();
                logout_process();

                connected = false;
            ">Logout</a>
        </div>
    </div>

    <div class="back-button" id="back-button">
        <a href="#" onclick="switch_main_form()">
            <img src="./img/home-icon.png" alt="Back" width="50vw" height="50vh">
        </a>
    </div>

    <script>
        // Private function
         function openFullScreen() {
            var isMobileDevice = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            if (isMobileDevice) {
                var elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.mozRequestFullScreen) { /* Firefox */
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { /* IE/Edge */
                    elem.msRequestFullscreen();
                }

                var isPortrait = window.matchMedia("(orientation: portrait)").matches;
                if (isPortrait && !document.body.classList.contains('portraitClass')) {
                    document.body.classList.add('portraitClass');
                }
            }      
        }

        function exitFullScreen() {
            if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }
          
        function setTitleWeb(page_current)
        {
            if(page_current != null)
            {
                if (page_current.localeCompare("TBG_page") == 0)
                    document.title = "Go! Thái Bình";                  
                else if (page_current.localeCompare("HLG_page") == 0)
                    document.title = "Go! Hạ Long";   
                else if (page_current.localeCompare("HDG_page") == 0)
                    document.title = "Go! Hải Dương"; 
                else if (page_current.localeCompare("TNN_page") == 0)
                    document.title = "Go! Thái Nguyên"; 
                else if (page_current.localeCompare("DNG_page") == 0)
                    document.title = "Go! Đà Nẵng";
                else if (page_current.localeCompare("DLT_page") == 0)
                    document.title = "Go! Đà Lạt";
                else if (page_current.localeCompare("DAN_page") == 0)
                    document.title = "Go! Dĩ An";
                else if (page_current.localeCompare("NTT_page") == 0)
                    document.title = "Go! Nguyễn Thị Thập";
                else if (page_current.localeCompare("MTO_page") == 0)
                    document.title = "Go! Mỹ Tho";
                else if (page_current.localeCompare("CTO_page") == 0)
                    document.title = "Go! Cần Thơ";   
                else if (page_current.localeCompare("login_page") == 0)
                    document.title = "Trang chủ";      
            }
        }

        function switch_main_form()
        {
            document.getElementById("back-button").style.visibility = 'hidden'; 
             
            setTimeout(function() {

                // Deactive old screen
                document.getElementById(sessionStorage.getItem('page_current')).style.display = "none";

                // Active new screen
                document.title = "Trang chủ"; 
                sessionStorage.setItem('page_current', 'login_page');
                document.getElementById(sessionStorage.getItem('page_current')).style.display = "";

                var obj = new Object();
                obj.topic = null;
                obj.type = "Reload";

                document.getElementById(sessionStorage.getItem('page_current')).contentWindow.postMessage(JSON.stringify(obj), '*');                  
            }, 200);
        }

        function switch_user_form()
        {        
            // Deactive old screen
            document.getElementById(sessionStorage.getItem('page_current')).style.display = "none";

            // Active new screen
            document.title = "Tài khoản";   
            sessionStorage.setItem('page_current', 'user_page');
            document.getElementById(sessionStorage.getItem('page_current')).style.display = "";

            document.getElementById("dropdown").checked = false;
            // document.getElementById("back-button").style.left = (document.getElementById("user-profile").offsetLeft - 60) + "px";
            document.getElementById("back-button").style.visibility = 'visible';    

            var obj     = new Object();
            obj.topic   = null;
            obj.type    = "Reload-User";
            obj.leader  = sessionStorage.getItem("username");

            document.getElementById(sessionStorage.getItem('page_current')).contentWindow.postMessage(JSON.stringify(obj), '*');       
            
            var obj = new Object();
            obj.topic = null;
            obj.type = "Update-Type-User";
            obj.rootTypeUser = typeUser;
            obj.typeUser = -1;
            if (allow_location.includes(sessionStorage.getItem('page_current').split('_')[0]))
            {
                obj.typeUser = typeUser;
            }
            
            document.getElementById(sessionStorage.getItem('page_current')).contentWindow.postMessage(JSON.stringify(obj), '*');              
        }

        function login_process(user, pass) 
        {
            app.disconnect();
            app.start(user, pass);

            var obj = new Object();
            obj.topic = null;
            obj.type = "Loading";

            document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');

            // Send command
            if(sessionStorage.getItem('page_current').localeCompare("user_page") == 0)
            {
                var obj = new Object();
                obj.topic   = null;
                obj.type    = "Reload-User";
                obj.leader  = sessionStorage.getItem("username");

                document.getElementById(sessionStorage.getItem('page_current')).contentWindow.postMessage(JSON.stringify(obj), '*');    
            }
            else
            {
                var obj = new Object();
                obj.topic = null;
                obj.type = "Show-Location";
                obj.location = document.title;
                obj.typeUser = -1;
                if (allow_location.includes(sessionStorage.getItem('page_current').split('_')[0]))
                {
                    obj.typeUser = typeUser;
                }

                document.getElementById(sessionStorage.getItem('page_current')).contentWindow.postMessage(JSON.stringify(obj), '*'); 
            }

            clearTimeout(timeoutLogin);
            timeoutLogin = setTimeout(function() {

                if (!statusConnect) {
                    // Login fail => back to menu login
                    var obj = new Object();
                    obj.topic = null;
                    obj.type = "Login-Failed";

                    document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');

                    document.getElementById("user-profile").style.visibility = 'hidden';
                }
                else
                {
                    connect_stable = true;
                }
            }, 4000);
        }

        function handleDataMessage(topic, message, ignore) {
            var page_current = sessionStorage.getItem('page_current')

            if(page_current != null)
            {
                // Filter when the message is duplicated
                var msg = JSON.parse(message);
                if(ignore || timestamp != msg.timestamp)
                {
                    timestamp = msg.timestamp;

                    var obj = new Object();
                    obj.topic = topic;
                    obj.message = message;
                    obj.location = page_current;

                    document.getElementById(page_current).contentWindow.postMessage(JSON.stringify(obj), '*');

                    if(ignore)
                    {
                        var msg = JSON.parse(message);
                        if (msg.command.localeCompare("reponse_location") == 0)
                        {
                            if(msg.data.location.length > 0)
                            {
                                allow_location = msg.data.location[0];

                                if ((typeUser == 1) || (typeUser == 4))
                                {
                                    var obj = new Object();
                                    obj.topic = null;
                                    obj.type = "Update-Type-User";
                                    obj.rootTypeUser = typeUser;
                                    obj.typeUser = -1;
                                    if (allow_location.includes(sessionStorage.getItem('page_current').split('_')[0]))
                                    {
                                        obj.typeUser = typeUser;
                                    }
                                    
                                    document.getElementById(page_current).contentWindow.postMessage(JSON.stringify(obj), '*');                            
                                }
                            }
                        }
                    }
                }
            }
        }

        function connected_cb() 
        {		
            console.log("Connected to server");
            connected = true;

            // Set title
            setTitleWeb(sessionStorage.getItem('page_current'));

            setTimeout(function() {
                var obj = new Object();
                obj.type = 'Request-Location';
                obj.leader = sessionStorage.getItem("username");
                obj.user = sessionStorage.getItem("username");
                // console.log(obj)
                client.send("/topic/command_topic", {"content-type": "text/plain"}, JSON.stringify(obj)); 
            }, 1000); 

            // Send to login page
            var obj = new Object();
            obj.topic = null;
            obj.type = "Login-Passed";

            document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');		   

            // Send to main page
            var obj = new Object();
            obj.type = "Keep-Alive";

            window.parent.postMessage(JSON.stringify(obj), '*'); 

			openFullScreen();
        }   

        $(window).load(function() {
            window.addEventListener("orientationchange", function() {
                // Handle rotation event here
                if (window.orientation === 0) {
                    // Portrait orientation
                    if (connected && !document.body.classList.contains('portraitClass')) {
                        document.body.classList.add('portraitClass');
                    }
                } else {
                    // Landscape orientation
                    if (document.body.classList.contains('portraitClass')) {
                        document.body.classList.remove('portraitClass');
                    }
                }
            });

            window.addEventListener('resize', function() {
                if(sessionStorage.getItem('page_current').localeCompare("login_page") == 0)
                {
                    setTimeout(function() {
                            var obj = new Object();
                            obj.topic = null;
                            obj.type = "Reload";

                            document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');                         
                    }, 10);                 
                }
                else
                {
                    setTimeout(function() {
                            var page_current = sessionStorage.getItem('page_current');

                            var obj = new Object();
                            obj.topic = null;
                            obj.type = "Show-Location";
                            obj.location = document.title;
                            obj.typeUser = -1;
                            if (allow_location.includes(sessionStorage.getItem('page_current').split('_')[0]))
                            {
                                obj.typeUser = typeUser;
                            }

                            document.getElementById(page_current).contentWindow.postMessage(JSON.stringify(obj), '*');
                            
                            setTitleWeb(page_current);
                    }, 10); 
                }
            });

            window.addEventListener("keypress", function actionNewFolderByKeyEnter(event) {
                if (event.keyCode == "13") {

                    var obj = new Object();
                    obj.topic = null;
                    obj.type = "Request-Login";

                    document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');
                }
            });

            window.addEventListener('message', function(event) {
                if(event.data.length > 0)
                {
                    // message from child frame
                    var obj = JSON.parse(event.data);
                    if (obj.type.localeCompare("Login") == 0)
                    {
                        var username = obj.username;
                        var password = obj.password;

                        if ((username.length == 0) || (password.length == 0)) {
                            var obj = new Object();
                            obj.topic = null;
                            obj.type = "Login-Error";

                            if (username.length == 0)
                                obj.username = "";
                            else
                                obj.username = username;

                            if (username.length == 0)
                                obj.password = "";
                            else
                                obj.password = password;

                            document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*');
                        } else {
                            login_process(username, password);
                        }
                    }
                    else if (obj.type.localeCompare("Logout") == 0)
                    {
                        sessionStorage.clear();

                        sessionStorage.setItem('page_current', 'login_page');

                        if (document.body.classList.contains('portraitClass')) {
                            document.body.classList.remove('portraitClass');
                        }                        
                    }
                    else if (obj.type.localeCompare("Show-User-info") == 0)
                    {
                        document.getElementById("username-profile").innerHTML = sessionStorage.getItem("username") + "&nbsp&nbsp|&nbsp&nbsp" + "<img style=\"width:13px; cursor: pointer;\" src=\"./img/downward-arrow.png\"></img>";
                        document.getElementById("user-profile").style.visibility = 'visible';
                        document.getElementById("dropdown").checked = false;    

                        var page_current = sessionStorage.getItem('page_current');
                        
                        if ((page_current != null) && 
                            ((page_current.localeCompare("user_page") == 0) ||
                            (page_current.localeCompare("TBG_page") == 0)  ||
                            (page_current.localeCompare("HLG_page") == 0)  ||
                            (page_current.localeCompare("HDG_page") == 0)  ||
                            (page_current.localeCompare("TNN_page") == 0)  ||
                            (page_current.localeCompare("DNG_page") == 0)  ||
                            (page_current.localeCompare("DLT_page") == 0)  ||
                            (page_current.localeCompare("DAN_page") == 0)  ||
                            (page_current.localeCompare("NTT_page") == 0)  ||
                            (page_current.localeCompare("MTO_page") == 0)  ||
                            (page_current.localeCompare("CTO_page") == 0)))
                        {
                            // document.getElementById("back-button").style.left = (document.getElementById("user-profile").offsetLeft - 60) + "px";
                            document.getElementById("back-button").style.visibility = 'visible';
                        }

                        // Store type user
                        typeUser = obj.typeUser;

                        if ((typeUser == 1) || (typeUser == 4))
                        {
                            document.getElementById("section-dropdown").childNodes[1].style.display = "";
                            
                            var obj = new Object();
                            obj.topic = null;
                            obj.type = "Update-Type-User";
                            obj.rootTypeUser = typeUser;
                            obj.typeUser = -1;
                            if (allow_location.includes(page_current.split('_')[0]))
                            {
                                obj.typeUser = typeUser;
                            }
                            
                            document.getElementById(page_current).contentWindow.postMessage(JSON.stringify(obj), '*');                            
                        }
                    }
                    else if (obj.type.localeCompare("Detail") == 0)
                    {
                        // Deactive old screen
                        document.getElementById(sessionStorage.getItem('page_current')).style.display = "none";

                        // Active new screen  
                        sessionStorage.setItem('page_current', obj.location);
                        document.getElementById(sessionStorage.getItem('page_current')).style.display = "";

                        document.getElementById("dropdown").checked = false;
                        // document.getElementById("back-button").style.left = (document.getElementById("user-profile").offsetLeft - 60) + "px";
                        document.getElementById("back-button").style.visibility = 'visible';

                        setTitleWeb(obj.location);

                        // Send command
                        var obj_send = new Object();
                        obj_send.topic = null;
                        obj_send.type = "Pre-Show-Location";
                        obj_send.location = document.title;

                        document.getElementById(obj.location).contentWindow.postMessage(JSON.stringify(obj_send), '*');  

                        // Send command
                        var obj_send = new Object();
                        obj_send.topic = null;
                        obj_send.type = "Show-Location";
                        obj_send.location = document.title;
                        obj_send.typeUser = -1;
                        if (allow_location.includes(sessionStorage.getItem('page_current').split('_')[0]))
                        {
                            obj_send.typeUser = typeUser;
                        }

                        document.getElementById(obj.location).contentWindow.postMessage(JSON.stringify(obj_send), '*');                   
                    }
                    else if ((obj.type.localeCompare("Active-Popup") == 0) || (obj.type.localeCompare("Deactive-Popup") == 0))
                    {
                        if (obj.type.localeCompare("Active-Popup") == 0)
                        {
                            document.getElementById("back-button").style.visibility = 'hidden';
                            document.getElementById("user-profile").style.visibility = 'hidden';
                        }
                        else
                        {
                            document.getElementById("back-button").style.visibility = 'visible';
                            document.getElementById("user-profile").style.visibility = 'visible';
                        }
                    }
                    else if (obj.type.localeCompare("Keep-Alive") == 0)
                    {
                        //clearTimeout(timeoutSession);
                        //timeoutSession = setTimeout(function() {
                        //    logout_process();                             
                        //}, 1800000);  
                    }
                    else if ((obj.type.localeCompare("Request-Warning-Flag") == 0)  ||
                             (obj.type.localeCompare("Request-Threshold") == 0)     ||
                             (obj.type.localeCompare("Request-Notify") == 0)        ||
                             (obj.type.localeCompare("Request-Delete-Issue") == 0) || 
                             (obj.type.localeCompare("Request-Issue") == 0)         ||
                             (obj.type.localeCompare("Request-Chart") == 0))
                    {
                        // Send to server
                        client.send("/topic/command_topic", {"content-type": "text/plain"}, JSON.stringify(obj));
                    }
                    else if ((obj.type.localeCompare("Request-Location") == 0) || (obj.type.localeCompare("Set-Locations") == 0))
                    {
                        // Send to server
                        client.send("/topic/command_topic", {"content-type": "text/plain"}, JSON.stringify(obj));
                    }
                }
            });

            window.addEventListener("click", function(event) {
                var obj = new Object();
                obj.type = "Keep-Alive";

                window.parent.postMessage(JSON.stringify(obj), '*'); 
            });
			
            var flag_Relogin = false;
            var page_current = sessionStorage.getItem('page_current');
            if (page_current != null)
            {
                document.getElementById(page_current).style.display = "";

                if (page_current.localeCompare("login_page") == 0)
                {
                    document.title = "Trang chủ";             

                    if ((sessionStorage.getItem('username') === null)   || 
                        (sessionStorage.getItem('username') === 'null') || 
                        (sessionStorage.getItem('password') === null)   || 
                        (sessionStorage.getItem('password') === 'null'))
                    {
                        flag_Relogin = true;  
                    }
                    else
                    {
                        
                        var obj = new Object();
                        obj.topic = null;
                        obj.type = "Loading";

                        document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*'); 
                    }
                }
                else if (page_current.localeCompare("user_page") == 0)
                {
                    document.title = "Tài khoản";             
                }
                else
                {
                    setTitleWeb(page_current);
                }

                // schedule login
                if(!flag_Relogin)
                {
                    clearTimeout(timeoutLoginProcess);
                    timeoutLoginProcess = setTimeout(function() {
                        login_process(sessionStorage.getItem('username'), sessionStorage.getItem('password'));                         
                    }, 500);  
                }
            }
            else
            {
                flag_Relogin = true; 
                document.title = "Đăng nhập";
                sessionStorage.setItem('page_current', 'login_page');

                document.getElementById('login_page').style.display = "";                       
            }  
            
            if(flag_Relogin)
            {
                var obj = new Object();
                obj.topic = null;
                obj.type = "Re-login";

                document.getElementById("login_page").contentWindow.postMessage(JSON.stringify(obj), '*'); 

                document.getElementById("user-profile").style.visibility = 'hidden';
            }

            document.getElementById("dropdown").checked = false;
        });         
    </script>
</body>

</html>