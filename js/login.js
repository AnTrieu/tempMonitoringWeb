let client;
let statusConnect = false
let ignoreFirstTime = true
let allocate_assigned = 0;
let connect_stable = false;
let flag_reconnect = 0;
let type_account_login = "Member";
let master_name = "";
let permission = -1;

function logout_process()
{
	sessionStorage.clear();

	window.location.href = "index";
}

let app = {
	
    printLogDebug: function (message) {
		if(connect_stable && message.indexOf("Whoops! Lost connection to") >= 0)
		{
			if(statusConnect)
			{
				client.disconnect();

				connect_stable = false;
				statusConnect = false;
				ignoreFirstTime = true;
				
				if ((sessionStorage.getItem('username') === null)   || 
					(sessionStorage.getItem('username') === 'null') || 
					(sessionStorage.getItem('password') === null)   || 
					(sessionStorage.getItem('password') === 'null'))
				{
					logout_process();
				}
				else
				{	
					clearInterval(flag_reconnect);
					flag_reconnect = setInterval(function() 
					{
						if(sessionStorage.getItem("username") != null && sessionStorage.getItem("password") != null)
						{
							app.start(sessionStorage.getItem("username"), sessionStorage.getItem("password"));
							setTimeout(function() {
								if (statusConnect) {
									connect_stable = true;
								}
							}, 4000);											
						}	
					}, 5000);					
				}
			}
		}
    },
    receivedMessage: function (topic ,message) {
		// console.log("Topic \"" + topic + "\" : " + message);
		// console.log("Topic \"" + topic);
		
		if(topic.localeCompare("/topic/" + app.user) == 0)
		{
			var msg = JSON.parse(message);

			if (msg.command.localeCompare("ping") == 0)
				logout_process();
			else
				handleDataMessage(topic ,message, true);
		}
		else
		{
			handleDataMessage(topic ,message, false);
		}
    },	
    send: function (topic, data) {
        // destination, headers, body
        client.send(topic, { "content-type": "text/plain" }, data);
    },
    subscribe: function (topic) {
        // destination, callback, headers
        client.subscribe("/topic/" + topic, function (response) {
            app.receivedMessage(topic, response.body);
        }, { id: topic });		
    },
	unsubscribe: function (topic) {
		client.unsubscribe(topic);
	},	
    onConnectCallback: function () 
	{
        // Connect succeed
        statusConnect = true;
				
		connected_cb();
		
		// Logout all user as same as name
		client.send("/topic/" + app.user, { "content-type": "text/plain" }, "{\"command\":\"ping\"}");
		
        client.subscribe("/topic/" + app.user, function (response) {
            app.receivedMessage("/topic/" + app.user, response.body);
        }, { id: app.user });		

		client.subscribe("/topic/web_topic", function (response) {
            app.receivedMessage("/topic/web_topic", response.body);
        }, { id: "/topic/web_topic_" + app.user});	

		clearInterval(flag_reconnect);
    },
    onErrorCallback: function () {
        // Disconnect
        // statusConnect = false;

        //console.log('Error');
    },
    createRabbitClient: function () {
        // Create STOMP client over websocket
        if (window.location.protocol === 'https:') {
			var hostname = window.location.hostname;
			hostname = hostname.replace("www.", "");

            var wsUri = "wss://" + hostname + ":15673/ws";
        }
        else {
            var wsUri = "ws://" + window.location.hostname + ":15674/ws";
        }
        return Stomp.client(wsUri);
    },
    onConnectionLost: function () {

        // Disconnect
        statusConnect = false;

        console.log('lost connect');
    },
    start: function (user, pass) {
		app.user = user;
		app.pass = pass;
		
		// Store session
		sessionStorage.setItem("username", user);
		sessionStorage.setItem("password", pass);
		sessionStorage.setItem("type_user_login", type_account_login);
		
        // Init status connect
        statusConnect = false;

        // Create RabbitMQ client using STOMP over websocket
        client = app.createRabbitClient(); // asign the created client as global for sending or subscribing messages

        // Enable debug
        client.debug = app.printLogDebug;
        //client.debug = null

        // username, password, connectCallback, errorCallback, host
        client.connect(user, pass, app.onConnectCallback, app.onErrorCallback, '/');

        client.onConnectionLost = app.onConnectionLost;
			
    },
    disconnect: function () {
        if (statusConnect) {
            client.disconnect();

            statusConnect = false;
			ignoreFirstTime = true;
			
			window.location.href = "index";
        }
    }
};
