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
	if (loginStat && loginStat == 0) {
		checkSession(userName, loginSess);
	}

	// Check session again after verifying and welcome if logged in
	if (loginStat && loginStat == 0) {
		// Generate login message and log-out button
		$("#welcome").append(userName + " logged in");
		$("#formfields").append("<br><button type=\"button\" id=\"x\" onclick=\"logout()\">Log out</button>");
	}

	else {
<<<<<<< HEAD
		// Otherwise dyanmically generate login form fields
		$("#formfields").append("<div class='form-group'><label for=\"uid\" class='col-lg-2 control-label' id=\"login\">user id</label><div class='col-lg-10'><input type=\"text\" placeholder=\"Enter ONID username\" id=\"uid\" name=\"uid\" value=\"\" class=\"required form-control\" minlength=\"5\" maxlength=\"20\"/></div</div>");
		// $("#formfields").append("<label for=\"uid\" class='col-lg-2 control-label' id=\"login\">user id</label>");
		// $("#formfields").append("<div class='col-lg-10'>");
		// $("#formfields").append("<input type=\"text\" placeholder=\"Enter ONID username\" id=\"uid\" name=\"uid\" value=\"\" class=\"required form-control\" minlength=\"5\" maxlength=\"20\"/>");
		// $("#formfields").append("</div></div>");
		$("#formfields").append("<div class='form-group'><label for=\"pw\" class='col-lg-2 control-label' id=\"login\">password</label><div class='col-lg-10'><input type=\"password\" placeholder=\"Enter Password\" id=\"pw\" name=\"pw\" value=\"\" class=\"required form-control\" minlength=\"5\" maxlength=\"20\"/><br></div></div>");
		// $("#formfields").append("<label for=\"pw\" class='col-lg-2 control-label' id=\"login\">password</label>");
		// $("#formfields").append("<div class='col-lg-10'>");
		// $("#formfields").append("<input type=\"password\" placeholder=\"Enter Password\" id=\"pw\" name=\"pw\" value=\"\" class=\"required form-control\" minlength=\"5\" maxlength=\"20\"/><br>");
		// $("#formfields").append("</div></div>");
		$("#formfields").append("<button type='submit' class='btn-large btn-default' id=\"x\">Log In</button>");
		//Generate link to register
		// $("#welcome").append("<a href=\"register.php\">New user</a>");	
=======
		// Otherwise dyanmically generate CAS login form
		// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
		var serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/login/caslogin.php";
		
		$("#formfields").append("OSU Users<br>");
		$("#formfields").append("<input type=\"hidden\" id=\"service\" name=\"service\" value=\"" + serviceURL + "\" />");
		$("#formfields").append("<input type=\"submit\" id=\"x\" value=\"Log In\" />");

		// Generate link to register
		$("#welcome").append("<a href=\"register.php\">New user</a>");
		
>>>>>>> Added CAS login
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

		// Redirect back to home page
		window.location.href = "index.html";
	}
	// If ticket does validate, then post Ajax form to create new session
	else {
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
	if (data.status && data.status != 0) {
		logout();
	}
	
	// Redirect back to home page
	window.location.href = "index.html";
}

// Callback function on ajax post to create new session
function errorSession(data, status) {
	$.cookie('userMsg', "error creating session", { path: '/'});
}

		
// // Callback function for ajax login form
// function updateCookie(responseText, statusText, xhr, $form) {
	// // Convert JSON text response from form to javascript object
	// var loginObj = eval("(" + responseText + ")");
	
	// // Set cookie with login information
	// $.cookie('sessStat', loginObj.status, { path: '/'});
	// $.cookie('sessUser', loginObj.user, { path: '/'});
	// $.cookie('sessID', loginObj.sessID, { path: '/'});
	// $.cookie('sessExp', loginObj.expiration, { path: '/'});
	
	// // Update login box and message
	// if (loginObj.status != null && loginObj.status == 0) {
		// $("#welcome").empty();
		// $("#welcome").append(loginObj.user + " logged in");
		// $("#formfields").empty();
		// $("#formfields").append("<br><button type=\"button\" id=\"x\" onclick=\"logout()\">Log out</button>");
	// }
	
	// // Display any messages
	// if (loginObj.message != null && loginObj.message != "You are now logged in" ) {
		// $("#usermsg").append(loginObj.message);
	// }
	
// }

// Retrieves messages stored in cookie and display in
// message area
function checkMessages() {
	// Get user messages
	var userMessage = $.cookie('userMsg');
	$.cookie('userMsg', null, { path: '/'});
	
	// If message exists, display in message area
	if (userMessage != null) {
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
	$("#welcome").empty();
	
	
	// regenerate login form fields
	// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
	var serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/login/caslogin.php";
	
	$("#formfields").append("OSU Users<br>");
	$("#formfields").append("<input type=\"hidden\" id=\"service\" name=\"service\" value=\"" + serviceURL + "\" />");
	$("#formfields").append("<input type=\"submit\" id=\"x\" value=\"Log In\" />");
	
	// Generate link to register
	$("#welcome").append("<a href=\"register.php\">New user</a>");
	
	// Activate jquery form validate
	$("#loginform").validate();
	
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
	// Convert JSON text response from form to javascript object
	
	// Set cookie with login information
	$.cookie('sessStat', data.status, { path: '/'});
	$.cookie('sessUser', data.user, { path: '/'});
	$.cookie('sessID', data.sessID, { path: '/'});
	$.cookie('sessExp', data.expiration, { path: '/'});

	// If session expired, delete cookie and give message
	if (data.status && data.status != 0) {
		logout();
	}
	// Otherwise, update session expiration on server
	else {
		$.post("refresh.php", { userName: userName, loginSess: loginSess });
	}

}

function errorSession(data, info) {
	$.cookie('sessStat', null, { path: '/'});
	$.cookie('sessID', null, { path: '/'});
    $("#usermsg").append("session update error occurred");
}

