// this is to load a navbar for the neding library

loadNavBar = function(){
	$('body').prepend('\
		<div class="navbar navbar-fixed-top navbar-inverse"> \
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-responsive-collapse"> \
				<span class="icon-bar"></span> \
				<span class="icon-bar"></span> \
				<span class="icon-bar"></span> \
			</button>\
			<a class="navbar-brand pull-left" href="index.html">Lending Library</a> \
			<div class="nav-collapse pull-right navbar-responsive-collapse collapse"> \
			<ul class="nav navbar-nav"> \
				<li><a href="search.html">Search</a></li> \
				<li><a href="addItem.html">Add Item</a></li> \
				<li><a href="my_items.php">My Items</a></li> \
				<li><form id="loginform" method="get" action="https://login.oregonstate.edu/cas/login" class="form-horizontal"> \
					<fieldset> <div id="formfields" ></div></fieldset> </li>\
			</ul> \
			 </div> \
		</div>\
		');
} ; 