/* 
 * CS 419 Lending Project
 */
 
 
 function getSearchOptions() {
	// Activate jquery form validate
	$("#searchform").validate();
	
	// Call Ajax to retrieve list of item types
	$.ajax({url: "itemtype.php",
		type: "GET",
		dataType: "json",
		success: createItemMenu,
		error: errorQuery});
	
	
	// Watch for item being selected and call Ajax
	// on item details
	$("#typeoptions").change(function() {
		// First clear out prior menu items
		$("#attrib1opt").empty();
		$("#attrib1opt").append("<option value=\"\">choose</option>");
		$("#attrib2opt").empty();
		$("#attrib2opt").append("<option value=\"\">choose</option>");
		$("#attrib3opt").empty();
		$("#attrib3opt").append("<option value=\"\">choose</option>");
		
		// *** POSSIBLE TO ADD A LOCAL CACHE FOR SPEED ***
		
		// Now call ajax to get new attribute information
		$.ajax({url: "attribute.php",
			type: "GET",
			dataType: "json",
			data: { type: $("#typeoptions").val() },
			success: createAttributes,
			error: errorQuery});
	});

 }
 
 // Takes JSON returned from AJAX to populate item type menu
 function createItemMenu(data, status) {
	var i;
	for (i = 0; i < data.length; i++) {
		$("#typeoptions").append("<option value=\"" + data[i] + "\">" + data[i] + "</option>");
	}
 }
 
 // Prints error message if AJAX call fail
 function errorQuery() {
		$("#usermsg").append("Unable to access database");
 }
 
 
  // Takes JSON returned from AJAX to populate attribute menus
 function createAttributes(data, status) {
	// Create key - value associative array
	var units = new Object();
	var isNumber = new Object();
	
	for (var i = 0; i < data.length; i++) {
	
				
		// Add value to associative array
		units[data[i].field] = data[i].units;
		isNumber[data[i].field] = data[i].numeric;
		
		// Add options to 3 attribute menus
		$("#attrib1opt").append("<option value=\"" + data[i].field + "\">" 
				+ data[i].field + "</option>");
		$("#attrib2opt").append("<option value=\"" + data[i].field + "\">" 
				+ data[i].field + "</option>");
		$("#attrib3opt").append("<option value=\"" + data[i].field + "\">" 
				+ data[i].field + "</option>");
	}
	
	// Listen to see if any of the 3 menus are selected
	// Attribute 1
	$("#attrib1opt").change(function() {
		var unitType = units[$(this).val()];
		var nbrType = isNumber[$(this).val()];
		
		// Reset comparison drop down
		$("#attrib1comp").empty();
		
		// Update form input based on data type
		if (nbrType == 1) {
			$("#attrib1input").attr("type", "number");
			$("#attrib1input").attr("placeholder", unitType);
			$("#attrib1input").attr("required", "true");
			$("#attrib1comp").append("<option value=\"=\">=</option>");
			$("#attrib1comp").append("<option value=\">\">></option>");
			$("#attrib1comp").append("<option value=\"?=\">>=</option>");
			$("#attrib1comp").append("<option value=\"<\"><</option>");
			$("#attrib1comp").append("<option value=\"<=\"><=</option>");
			
		} else if (nbrType && nbrType == 0) {
			$("#attrib1input").attr("type", "text");
			$("#attrib1input").attr("placeholder", "search");
			$("#attrib1input").attr("required", "true");
			$("#attrib1comp").append("<option value=\"=\">=</option>");
		// If "choose" default option selected
		} else {
			$("#attrib1input").attr("type", "");
			$("#attrib1input").attr("placeholder", "");
			$("#attrib1input").attr("required", "false");
		}
	});

	// Attribute 2
	$("#attrib2opt").change(function() {
		var unitType = units[$(this).val()];
		var nbrType = isNumber[$(this).val()];
		
		// Reset comparison drop down
		$("#attrib2comp").empty();
		
		// Update form input based on data type
		if (nbrType == 1) {
			$("#attrib2input").attr("type", "number");
			$("#attrib2input").attr("placeholder", unitType);
			$("#attrib2input").attr("required", "true");
			$("#attrib2comp").append("<option value=\"=\">=</option>");
			$("#attrib2comp").append("<option value=\">\">></option>");
			$("#attrib2comp").append("<option value=\"?=\">>=</option>");
			$("#attrib2comp").append("<option value=\"<\"><</option>");
			$("#attrib2comp").append("<option value=\"<=\"><=</option>");
			
		} else if (nbrType && nbrType == 0) {
			$("#attrib2input").attr("type", "text");
			$("#attrib2input").attr("placeholder", "search");
			$("#attrib2input").attr("required", "true");
			$("#attrib2comp").append("<option value=\"=\">=</option>");
		// If "choose" default option selected
		} else {
			$("#attrib2input").attr("type", "");
			$("#attrib2input").attr("placeholder", "");
			$("#attrib2input").attr("required", "false");
		}
	});
	// Attribute 3
	$("#attrib3opt").change(function() {
		var unitType = units[$(this).val()];
		var nbrType = isNumber[$(this).val()];
		
		// Reset comparison drop down
		$("#attrib3comp").empty();
		
		// Update form input based on data type
		if (nbrType == 1) {
			$("#attrib3input").attr("type", "number");
			$("#attrib3input").attr("placeholder", unitType);
			$("#attrib3input").attr("required", "true");
			$("#attrib3comp").append("<option value=\"=\">=</option>");
			$("#attrib3comp").append("<option value=\">\">></option>");
			$("#attrib3comp").append("<option value=\"?=\">>=</option>");
			$("#attrib3comp").append("<option value=\"<\"><</option>");
			$("#attrib3comp").append("<option value=\"<=\"><=</option>");
			
		} else if (nbrType && nbrType == 0) {
			$("#attrib3input").attr("type", "text");
			$("#attrib3input").attr("placeholder", "search");
			$("#attrib3input").attr("required", "true");
			$("#attrib3comp").append("<option value=\"=\">=</option>");
		// If "choose" default option selected
		} else {
			$("#attrib3input").attr("type", "");
			$("#attrib3input").attr("placeholder", "");
			$("#attrib3input").attr("required", "false");
		}
	});

}

		// <option value="3">Checked Out</option>
