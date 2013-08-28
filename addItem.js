/* 
 * CS 419 Lending Project
 */
 
 
 function getAddOptions() {
	// Activate jquery form validate
	$("#addform").validate();
	console.log("in getSearchOptions_ debug test");
	// Call Ajax to retrieve list of item types
	$.ajax({url: "itemtype.php",
		type: "GET",
		dataType: "json",
		// success: alert("success"),
		// error: alert("Error")});
		success: createItemMenu,
		error: errorQuery});
			
	
	// Watch for item being selected and call Ajax
	// on item details
	$("#typeoptions").change(function() {
		// First clear out prior menu items
		$("#typefields").empty();
		// *** POSSIBLE TO ADD A LOCAL CACHE FOR SPEED ***
		// Now call ajax to get new attribute information
		$.ajax({url: "attribute.php",
			type: "GET",
			dataType: "json",
			data: { type: $("#typeoptions").val(),
					search: 0 },
			success: createAttributes,
			error: errorQuery});
	});

 }
 
 // Takes JSON returned from AJAX to populate item type menu
 function createItemMenu(data, status) {
	console.log("CreateMenu AJAX Success");
	var i;
	for (i = 0; i < data.length; i++) {
		console.log(data[i]);
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
	$('fieldtype').empty();
	for (var i = 0; i < data.length; i++) {	
		// Add value to associative array
		console.log(data[i].field);
		units[data[i].field] = data[i].units;
		isNumber[data[i].field] = data[i].numeric;
		var contID = "container-"+data[i].field;
		var fldname = "fldname-"+data[i].field;
		var valname = "val-"+data[i].field;
		console.log(contID);
		$('#typefields').append('<div class="container" id="'+contID+'">');
		$('#'+contID).append('<div id="'+fldname+'" class="col-lg-2 col-sm-2 col-12 control-label">');
		
		$('#'+fldname).append('<label>'+data[i].field.replace(/[-_]/g," ")+'</label>');
		
		$('#'+contID).append('<div class="col-lg-2 col-sm-2 col-12 hidden-xs">');
		
		$('#'+contID).append('<div class="col-lg-8 col-sm-8 col-12"><input type="text" placeholder="'+data[i].units+'" id="'+valname+'" name="'+valname+'" class="form-control"></input>');
		if (data[i].required == 1 ){
			$('#'+valname).addClass("required");
		}
		if (data[i].numeric == 1 ){
			$('#'+valname).addClass("number");
		}
	}

}

		// <option value="3">Checked Out</option>
