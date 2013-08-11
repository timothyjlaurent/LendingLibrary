/* Function that calls Ajax to checkout item.
	Needs itemID as parameter
*/
function checkout(item) {

	// Use ajax to check out item
	$.ajax({url: "checkout.php",
		type: "POST", // GET for testing
		dataType: "json",
		data: { id: item },
		success: confirmCheckout,
		error: errorAjax("check out")
		});
}

/* Success callback function for checkout.
	Updates messages and menu options.
*/
function confirmCheckout(data) {

	// Checkout is successful
	if (data.status && data.status == 0) {
		// turn off the button & display message
		$("#chkbutton").empty();
		$("#usermsg").empty();
		$("#usermsg").append("Item checked out.  Please return by: " + data.message);
	}
	else {
		$("#usermsg").empty();
		$("#usermsg").append("Unable to checkout");
	}



}




/* Function that calls Ajax to checkin item.
	Needs itemID as parameter.
*/
function checkin(itemNum) {

	// Use ajax to check out item
	$.ajax({
		url: "checkin.php",
		type: "POST", // GET for testing
		dataType: "json",
		data: { id: itemNum },
		success: confirmCheckin,
		// error: errorAjax("check in")
		});

}

/* Success callback function for checkin.
	Updates messages and menu options.
*/
function confirmCheckin(data) {
	// Checkout is successful
	if (data.status && data.status == 0) {
		$("#usermsg").empty();
		// $("#usermsg").append("checked in");
		
		// De-activate button
		divID = "checkin" + data.item;
		buttonID = "btn" + data.item;
		$("#" + buttonID).remove();
		$("#" + divID).append(data.message);
	}
	else {
		$("#usermsg").append("unable to checked in");
	}
	
}

/* Error callback function for check in and out.
	Needs either "check in" or "check out" as
	parameter.
*/
function errorAjax(tranType) {
	$("#usermsg").empty();
	$("#usermsg").append("* Unable to " + tranType);
}
