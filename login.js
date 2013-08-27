/* 
 * CS 419 Lending Project
 */

// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
 
// Checks to see if login cookie exists
// Dynamically generates login form if not logged in
function checkLogin() {
	// Get cookie values
	var loginStat = $.cookie('sessStat');
	var userName = $.cookie('sessUser');
	var loginSess = $.cookie('sessID');
	var loginExp = $.cookie('sessExp');
	
	// If user logged in, check if session expired on server
	// This will also unset the cookie if it is, or extend
	// the cookie if it isn't
	if (loginStat && loginStat === 0) {
		checkSession(userName, loginSess);
	}

	// Check session again after verifying and welcome if logged in
	if (loginStat && loginStat === 0) {
		// Generate login message and log-out button
		$("#formfields").append("<button type=\"button\" class='btn btn-default btn-block' id=\"x\" onclick=\"logout()\">Log out</button>");
	}

	else {

		// Otherwise dyanmically generate CAS login form
		// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
		// var serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/caslogin.php";
		var serviceURL = "https://web.engr.oregonstate.edu/~laurentt/lendingLibrary/caslogin.php";
		var returnURL = "?return=" + window.location.href;
		
		$("#formfields").append("<input type=\"hidden\" id=\"service\" name=\"service\" value=\"" + serviceURL + returnURL+ "\" /><button type='submit' class='btn btn-default btn-block' id=\"x\">OSU Log In</button></div></div>");

		// Activate jquery form validate
		$("#loginform").validate();

		}
}

// Checks CAS XML validation response to see if ticket is valid
// Function is called upon success of Ajax post to OSU CAS site
function checkCASLogin(data) {
	// Parse response variables
	var user;
	var failMsg;
	try {
		// Read user and message from JSON
		user = data.query.results.serviceResponse.authenticationSuccess.user;
		failMsg = data.query.results.serviceResponse.authenticationFailure;
	}
	catch(err) {
		$.cookie('userMsg', "Unable to validate", { path: '/'});
	}
	// Unable to validate CAS ticket
	if (!user) {
		// Ensure user is logged out by clearing cookie values
		$.cookie('sessStat', null, { path: '/'});
		$.cookie('sessID', null, { path: '/'});
		$.cookie('sessUser', null, { path: '/'});
		$.cookie('sessExp', null, { path: '/'});
		
		// Record fail message to cookie for later retrieval
		$.cookie('userMsg', failMsg, { path: '/'});

		// Redirect back to original page
		var searchURL = getUrlVars()["return"];
		window.location.href = searchURL;
	}
	// If ticket does validate, then post Ajax form to create new session
	else {
		// user = $.cookie('sessUser');
		$.ajax({url: "login.php",
			type: "POST",
			dataType: "json",
			data: { uid: user },
			success: createSession,
			error: errorSession});
	}
}

// Callback function on ajax post to create new session
function createSession(data, status) {
	
	// Set cookie with login information
	$.cookie('sessStat', data.status, { path: '/'});
	$.cookie('sessUser', data.user, { path: '/'});
	$.cookie('sessID', data.sessID, { path: '/'});
	$.cookie('sessExp', data.expiration, { path: '/'});

	// If session expired, delete cookie and give message
	if (data.status && data.status !== 0) {
		logout();
	}

	// Redirect back to original page
	var searchURL = getUrlVars()["return"];
	window.location.href = searchURL;
}

// Callback function on ajax post to create new session
function errorSession(data, status) {
	$.cookie('userMsg', "error creating session", { path: '/'});
}



// Retrieves messages stored in cookie and display in
// message area
function checkMessages() {
	// Get user messages
	var userMessage = $.cookie('userMsg');
	$.cookie('userMsg', null, { path: '/'});
	
	// If message exists, display in message area
	if (userMessage !== null) {
		$("#usermsg").empty();
		$("#usermsg").append(userMessage);
	}
	
}

// Logs user out by removing session cookie
function logout() {
	$.cookie('sessStat', null, { path: '/'});
	$.cookie('sessID', null, { path: '/'});
	$.cookie('sessUser', null, { path: '/'});
	$.cookie('sessExp', null, { path: '/'});
	
	$("#formfields").empty();
	$("#usermsg").empty();
	
	location.reload();
	
	// // regenerate login form fields
	// // **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
	// var serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/login/caslogin.php";
	// var returnURL = "?return=" + window.location.href;

	// $("#formfields").append("<input type=\"hidden\" id=\"service\" name=\"service\" value=\"" + serviceURL + returnURL
		// + "\" /><button type='submit' class='btn btn-default btn-block' id=\"x\">OSU Log In</button></div></div>");
	
	// // Alert user
	// $("#usermsg").append("You are logged out");
	
	// // Activate jquery form validate
	// $("#loginform").validate();
	
}


// Checks if cookie is expired on the server.  If not, it will
// update cookie's expiration date on server, or ask user to login
function checkSession(userName, loginSess) {

	$.ajax({url: "session.php",
		type: "POST",
		dataType: "json",
		data: { userName: userName, loginSess: loginSess },
		success: updateSession,
		error: errorSession});
}

function updateSession(data, status) {
	
	// Set cookie with login information
	$.cookie('sessStat', data.status, { path: '/'});
	$.cookie('sessUser', data.user, { path: '/'});
	$.cookie('sessID', data.sessID, { path: '/'});
	$.cookie('sessExp', data.expiration, { path: '/'});

	// If session expired, delete cookie and give message
	if (data.status && data.status !== 0) {
		logout();
	}
	// Otherwise, update session expiration on server
	else {
		$.post("refresh.php", { userName: data.user, loginSess: data.sessID });
	}

}

function errorSession(data, info) {
	$.cookie('sessStat', null, { path: '/'});
	$.cookie('sessID', null, { path: '/'});
    $("#usermsg").append("session update error occurred");
}

// Function to parse data from get parameters in URL
// http://papermashup.com/read-url-get-variables-withjavascript/
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
