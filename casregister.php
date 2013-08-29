<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php
	// Parse CAS ticket parameter & make safe
	$ticket = $mysqli->real_escape_string($_GET['ticket']);
	
	// Create validation URL
	// **** REMEMBMER TO CHANGE SERVICE URL to production URL  *******
	$serviceURL = "https://web.engr.oregonstate.edu/~wongbe/CS419/casregister.php";
	$returnURL = $_GET['return'];
	// echo $returnURL;
	$validateURL = "https://login.oregonstate.edu/cas/serviceValidate?ticket=" . $ticket . "&service=" 
		. $serviceURL . "?return=" . $returnURL;
	
?>
<html>
<head>
	<script src="jquery-1.9.1.min.js"></script>
	<script src="jquery.validate.min.js"></script>
	<script src="jquery.form.min.js"></script>
	<script src="jquery.cookie.js"></script>
	<script type="text/javascript" src="register.js"></script>
	<script>
		$(document).ready(function() {
			// Use Ajax to validate CAS ticket #
			// Workaround for same origin policy from:
			// http://jsbin.com/oxokup/1/edit

			// URL for CAS Validation
			var url = "<?php echo $validateURL;?>";

			$.ajax({
				url: "http://query.yahooapis.com/v1/public/yql",
				data: {
					q: "select * from xml where url='" + url + "'",
					format: "json"
				},
				success: checkCASRegister
			});
		});
		
	</script>
</head>
<body>
	<?php
	?>
</body>
</html>