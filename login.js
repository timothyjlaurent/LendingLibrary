/* 
 * CS 419 Lending Project
 */
 
// Checks to see if login cookie exists
// If it doesn't dynamically generates form to allow user to log in
function checkLogin() {
	// Get cookie values
	var loginStat = $.cookie('sessStat');
	var userName = $.cookie('sessUser');
	var loginSess = $.cookie('sessID');
	var loginExp = $.cookie('sessExp');
	var loginMsg = $.cookie('sessMsg');
	
	// If user logged in, check if session expired on server
	// This will also unset the cookie if it is, or extend
	// the cookie if it isn't
	if (loginStat != null && loginStat == 0) {
		checkSession(userName, loginSess);
	}

	// Check session again after verifying and welcome if logged in
	if (loginStat != null && loginStat == 0) {
		// Generate login message and log-out button
		$("#welcome").append(userName + " logged in");
		$("#formfields").append("<br><button type=\"button\" id=\"x\" onclick=\"logout()\">Log out</button>");
	}

	else {
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
		// Activate jquery form validate
		$("#loginform").validate();

		}
}

// Callback function for ajax login form
function updateCookie(responseText, statusText, xhr, $form) {
	// Convert JSON text response from form to javascript object
	var loginObj = eval("(" + responseText + ")");
	
	// Set cookie with login information
	$.cookie('sessStat', loginObj.status, { path: '/'});
	$.cookie('sessUser', loginObj.user, { path: '/'});
	$.cookie('sessID', loginObj.sessID, { path: '/'});
	$.cookie('sessExp', loginObj.expiration, { path: '/'});
	
	// Update login box and message
	if (loginObj.status != null && loginObj.status == 0) {
		$("#welcome").empty();
		$("#welcome").append(loginObj.user + " logged in");
		$("#formfields").empty();
		$("#formfields").append("<br><button type=\"button\" id=\"x\" onclick=\"logout()\">Log out</button>");
	}
	
	// Display any messages
	if (loginObj.message != null && loginObj.message != "You are now logged in" ) {
		$("#usermsg").append(loginObj.message);
	}
	
}

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
	$("#formfields").append("<label id=\"login\">user id</label><br>");
	$("#formfields").append("<input type=\"text\" id=\"uid\" name=\"uid\" value=\"\" class=\"required\" minlength=\"5\" maxlength=\"20\"/><br>");
	$("#formfields").append("<label id=\"login\">password</label><br>");
	$("#formfields").append("<input type=\"password\" id=\"pw\" name=\"pw\" value=\"\" class=\"required\" minlength=\"5\" maxlength=\"20\"/><br>");
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

function updateSession(data, status)
{
	// Convert JSON text response from form to javascript object
	var loginObj = eval("(" + responseText + ")");
	
	// Set cookie with login information
	$.cookie('sessStat', loginObj.status, { path: '/'});
	$.cookie('sessUser', loginObj.user, { path: '/'});
	$.cookie('sessID', loginObj.sessID, { path: '/'});
	$.cookie('sessExp', loginObj.expiration, { path: '/'});

	// If session expired, delete cookie and give message
	if (loginObj.status != null && loginObj.status == 0) {
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

