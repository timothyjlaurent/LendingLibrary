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
	
	// location.reload(true);
}


// Checks if cookie is expired on the server.  If not, it will
// update cookie's expiration date on server, or ask user to login
function checkSession(userName, loginSess) {

	$.ajax({url: "session.php",
		type: "POST",
		dataType: "xml",
		data: { userName: userName, loginSess: loginSess },
		success: updateSession,
		error: errorSession});
}

function updateSession(data, status)
{
	var userName = $.cookie('sessUser');
	var loginSess = $.cookie('sessID');
	
	var sessStatTxt; // is session valid 0 or -1
	var sessMsgTxt;
	
    // Parse session response
    var sessioninfo = $(data).find("sessioninfo");
    if (sessioninfo && sessioninfo.length) {
		var sessStat = $(sessioninfo).find("status");
		sessStatTxt = sessStat.text();
		var sessMsg = $(sessioninfo).find("message");
		sessMsgTxt = sessMsg.text();
    }
	// If unable to parse XML, assume an error
    else {
		$.cookie('sessStat', null, { path: '/'});
		var sessStatTxt = "-1";
		var sessMsgTxt = "Session expired, please log in";
	}
	
	// If session expired, delete cookie and give message
	if(sessStatTxt != 0) {
		$.cookie('sessStat', null, { path: '/'});
		$.cookie('sessID', null, { path: '/'});
		$("#usermsg").append(sessMsgTxt);
		
		// Refresh page http://stackoverflow.com/questions/5404839/how-can-i-refresh-a-page-with-jquery
		location.reload(true);
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



// // Checks to see if user is logged in and tries to register again
// // (not allowed).  Only displays registration form is user not logged in.
// function checkRegister() {
	// // Get cookie values
	// var loginStat = $.cookie('sessStat');
	
	// // If user logged in, give warning
	// if (loginStat != null && loginStat != -1) {
		// $("#regmsg").append("<p>Please log out before registering.<p>");
	// }
	// else {
		// $("#regmsg").append("<p>Register here for the weight loss challenge.</p>");
		
		// // Update the form
		// $("#registerform").append("<label>user id</label><br>");
		// $("#registerform").append("<input type=\"text\" id=\"uid\" name=\"uid\" value=\"\" class=\"required\" minlength=\"5\" maxlength=\"20\"/><br>");
		// $("#registerform").append("<label>password</label><br>");
		// $("#registerform").append("<input type=\"password\" id=\"pw\" name=\"pw\" value=\"\" class=\"required\" minlength=\"5\" maxlength=\"20\"/><br><br>");

		// $("#registerform").append("<label>first name</label><br>");
		// $("#registerform").append("<input type=\"text\" id=\"fname\" name=\"fname\" value=\"\" class=\"required\" minlength=\"2\" maxlength=\"50\"/><br>");
		// $("#registerform").append("<label>last name</label><br>");
		// $("#registerform").append("<input type=\"text\" id=\"lname\" name=\"lname\" value=\"\" class=\"required\" minlength=\"2\" maxlength=\"50\"/><br><br>");

		// $("#registerform").append("<label>current weight (lbs)</label><br>");
		// $("#registerform").append("<input type=\"number\" id=\"currWgt\" name=\"currWgt\" value=\"\" class=\"required\"/><br>");
		// $("#registerform").append("<label>target weight (lbs) </label><br>");
		// $("#registerform").append("<input type=\"number\" id=\"tarWgt\" name=\"tarWgt\" value=\"\" class=\"required\"/><br><br>");

		// $("#registerform").append("<label>coach's name</label><br>");
		// $("#registerform").append("<select id=\"coachName\" name=\"coachName\" class=\"required\"><option value =\"\"></option><option value =\"beckyw\">Becky W</option><option value =\"jims\">Jim the Slim</option><option value =\"jackyf\">Jacky Fit</option>");
		// $("#registerform").append("</select><br><br>");

		// $("#registerform").append("<input type=\"submit\" id=\"submitreg\" value=\"register\" />");
		
		// //  Activate jquery form validate
		// $("#registerform").validate();

	// }
	
// }

// // Draws line chart showing user's weight loss progress
// // Only visible if user is logged
// function drawChart() {

	// // Get user data is user is logged in
	// var loginStat = $.cookie('sessStat');
	// var userName = $.cookie('sessUser');

	// // if (loginStat != null && loginStat != -1) {

	
	// if (loginStat == null || loginStat != 0) {
		// $("#usermsg").append("You must be logged in to see/update results");
	// }
	
	// else {
		// // $("#usermsg").append("Getting chart data");
		// // getChartData(userName);

		// // Instantiate gooel chart object
		// var chartArray = new Array();
		// var chartData = new google.visualization.DataTable();
		
		// // Set chart columns
		// chartData.addColumn('date', 'Date');
		// chartData.addColumn('number', 'Current');
		// chartData.addColumn('string', 'Notes');
		// chartData.addColumn('number', 'Target');
		
		// // Set min / max figures for chart scaling
		// var pct = 0;
		
		// // Use ajax to get JSON user data
		// $.ajax({
			// type: "POST",
			// url: "mychartdata.php",
			// dataType: "json",
			// data: {userName: userName},
			// cache: false,
			// async: false,
			// // Help from: http://stackoverflow.com/questions/2529848/looping-through-json-array
			// success: function(data){ 
				// $.each(data, function(index, data) {
					// // Append user data to google chart object
					// var plotDate = new Date(data.currdate);
					// var currWeight = parseInt(data.weight, 10);
					// var targWeight = parseInt(data.target, 10);
					// chartData.addRow([plotDate, currWeight, data.note, targWeight]);
						
				// });
			// }
		// });
		
		// // Setup chart options
		// var options = {
			// title: 'title',
			// backgroundColor: '#E4E4E4',
			// displayRangeSelector: false,
			// displayAnnotations: true,
			// scaleType: 'maximized'
		// };
	
		// // Generate the chart in the placeholder DOM
		// var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
		// chart.draw(chartData, options);
		
	// // If user is logged in, then dynamically create the submit form
	// $("#resultsform").append("<label>date (between 6/1/13-8/30/13)</label><br>");
	// $("#resultsform").append("<input type=\"date\" id=\"date\" name=\"date\" value=\"\" class=\"required\" /><br>");
	// $("#resultsform").append("<label>current weight (lbs)</label><br>");
	// $("#resultsform").append("<input type=\"number\" id=\"updateWgt\" name=\"updateWgt\" value=\"\" class=\"required\" /><br>");
	// $("#resultsform").append("<label>notes (up to 50 chars)</label><br>");
	// $("#resultsform").append("<input type=\"text\" id=\"notes\" name=\"notes\" value=\"\" maxlength=\"50\"/><br>");
	
	// $("#resultsform").append("<input type=\"hidden\" name=\"userName\" id=\"userName\"value=\"" + userName + "\" /><br>");
	// $("#resultsform").append("<input type=\"submit\" id=\"submitresults\" value=\"submit\" />");
	// //  Activate jquery form validate
	// $("#resultsform").validate();
	// }
// }


// // Draws column chart showing each particpant's % weight loss since the competition
// // started.  Data is anonymized since chart can be viewed by anybody
// function drawBarChart() {

	// // Setup chart options
	// var options = {
		// title: 'Weight Loss Since June 1, 2013',
		// backgroundColor: '#E4E4E4',
		// is3D: true,
		// vAxis: {title: 'Contestant'}
	// };
	
	// var chartData = new google.visualization.DataTable();
		
	// // Set chart columns
	// chartData.addColumn('string', 'Contestant');
	// chartData.addColumn('number', 'Total % ');
	// chartData.addColumn('number', 'Weekly Avg %');
	
	// // Use ajax to get JSON user data
	// $.ajax({
		// type: "POST",
		// url: "challengedata.php",
		// dataType: "json",
		// cache: false,
		// async: false,
		// // Help from: http://stackoverflow.com/questions/2529848/looping-through-json-array
		// success: function(data){ 
			// $.each(data, function(index, data) {
				// // Append user data to google chart object
				// var totChange = parseFloat(data.total);
				// var avgChange = parseFloat(data.change);
				
				// chartData.addRow([(index + 1).toString(), totChange, avgChange]);
			
			// });
		// }
	// });

	// var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
	// chart.draw(chartData, options);	
// }
