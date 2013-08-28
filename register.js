function createRegisterForm() {

	// Only generate registration form  if user is not
	// Get cookie values
	var loginStat = $.cookie('sessStat');
	var userName = $.cookie('sessUser');

	
	// If user logged in with us, give them an error message
	if (loginStat && loginStat == 0) {
		$("#usermsg").append("You are already registered");
	}
	else {
		// Display registration instructions
		$("#formlegend").append("Register for OSU Lending Library");
		$("#instructions").append("The OSU Lending Library is only available to OSU students, staff and other individuals with a valid OSU ID.");
		$("#instructions").append("To register your account for the Lending Library Service, please login in with ONID first:<br><br>");

		// If user logged in with ONID, generate registration form fields
		if (userName) {
			$("#instructions").empty();
			$("#instructions").append("Please complete the following fields:");

			$("#fngroup").append("<label class='control-label' for='fname'>First name</label>");
			$("#fngroup").append("<div class='controls'><input type='text' id='fname' name='fname' class='required' minlength='2' maxlength='25'></div>");

			$("#lngroup").append("<label class='control-label' for='lname'>Last name</label>");
			$("#lngroup").append("<div class='controls'><input type='text' id='lname' name='lname' class='required' minlength='2' maxlength='35'></div>");

			$("#uidgroup").append("<label class='control-label' for='uname'>User name</label>");
			$("#uidgroup").append("<div class='controls'><input type='text' id='uname' name='uname' readonly value='" + userName + "' placeholder='" + userName + "'></div>");

			$("#rbgroup").append("I agree to the terms and conditions for the lending library<br>");
			$("#rbgroup").append("<button type='submit' class='btn-default' id='regbutton'>Register</button>");

			// Activate jquery form validate
			$("#registerform").validate();

			
		}
		// Otherwise, prompt them to login with ONID
		else {
			// Generate button for onid login
			// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
			var serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/casregister.php";
			var returnURL = "?return=" + window.location.href;
			
			$("#onidfields").append("<input type=\"hidden\" id=\"service\" name=\"service\" value=\"" + serviceURL + returnURL
				+ "\" /><button type='submit' class='btn-default' id=\"x\">Start</button></div></div>");
		}
	}
}

// Checks CAS XML validation response to see if ticket is valid
// Function is called upon success of Ajax post to OSU CAS site
function checkCASRegister(data) {
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

	}
	// Set the user name retrieved from ONID to the cookie
	else {
		$.cookie('sessUser', user, { path: '/'});
	}

	// Redirect back to original page
	var searchURL = getUrlVars()["return"];
	window.location.href = searchURL;
	
}


// Callback function for ajax login form
function finishRegistration(data) {
	$("#usermsg").append("hello ajax");

	// Set cookie with login information
	$.cookie('sessStat', data.status, { path: '/'});
	$.cookie('sessUser', data.user, { path: '/'});
	$.cookie('sessID', data.sessID, { path: '/'});
	$.cookie('sessExp', data.expiration, { path: '/'});

	// Update registration form and message if 0 returned
	// which means registration completed (but may not
	// have completed login)
	if (data.status && data.status == 0) {
		$("#usermsg").empty();
		$("#instructions").empty();
		$("#fngroup").empty();
		$("#lngroup").empty();
		$("#uidgroup").empty();
		$("#rbgroup").empty();
	}

	// If user is logged in
	if (data.sessID !== null) {
		// Generate log-out button
		$("#formfields").empty();
		$("#formfields").append("<button type=\"button\" class='btn btn-default btn-block' id=\"x\" onclick=\"logout()\">Log out</button>");
	}
	
	// Append greeting
	$("#usermsg").append(data.message);
	

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