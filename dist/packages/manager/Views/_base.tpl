<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-gb" dir="ltr">
	<head>
		<!--================================ META ================================-->
		<meta charset="utf-8">
		<!--[if lt IE 9]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->

		<!--================================ THIRD-PARTY RESOURCES ================================-->
		<!-- TEMPORARY FIX -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/unsemantic/1.2.3/unsemantic-grid-responsive-tablet-no-ie7.min.css" type="text/css" rel="stylesheet">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
		<link href="https://rawgit.com/ahosgood/Arable/master/dist/arable.min.css" type="text/css" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

		<!--================================ INTERFACE RESOURCES ================================-->

		{resource:manager}
		{meta:tags}

		<!--================================ LINKED DOCUMENTS ================================-->
		<link rel="shortcut icon" type="image/x-icon" href="{core:logo-favicon}">

		<!--================================ MOBILE STUFF ================================-->
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
	</head>
	<body>
		<div class="app-header">
			<div class="logo">
				<a href="https://twistphp.com/" title="Twist PHP" target="_blank">
					<img src="{resource:core-uri}logos/logo.png">
				</a>
			</div>
			<ul id="menu" class="tabs grid-20 prefix-5 tablet-grid-25 mobile-grid-100">{view:./components/global/menu.php}</ul>
		</div>
		<div class="app-content">
			<div class="grid-container">
				<div class="grid-95 tablet-grid-65 tablet-prefix-5 mobile-grid-90 mobile-prefix-5 grid-parent">
					{route:response}
				</div>
			</div>
		</div>
		<footer>
			<div class="grid-100 mobile-grid-100">
				<p><a href="https://twistphp.com/" title="TwistPHP" target="_blank">TwistPHP</a> &copy; {date:Y}, Proud to be OpenSource | <a href="https://twistphp.com/docs/latest" title="TwistPHP Docs">docs</a></p>
			</div>
		</footer>
	</body>
</html>
