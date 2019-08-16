<?php
 include "globals.php";
// Prints a styled countdown using only CSS and PHP.
function cssCountdown($sec,$url,$confirm='')
{
	// Sets default for immediate redirect or confirmation message.
	$confirm = ($confirm == '') ? TRUE : FALSE;
	// Sets confirmation message.
	$confirm_headline = 'Had enough?';
	$confirm_yes = 'Yes!';
	$confirm_no = 'Not really &hellip;';
	$confirm_msg = "<h2>".$confirm_headline."</h2>\n<ul>\n<li><a href=\"".$url."\">".$confirm_yes."</a></li>\n<li><a href=\"".$_SERVER['PHP_SELF']."\">".$confirm_no."</a></li>\n</ul>\n";
	
	// Sets the amount of time that should pass until the counting stops. Is always amount of seconds to countdown from plus 1!
	$limit = $sec + 1;
	
	// Sets font-unit, maximum and minimum font-size.
	$font_size_unit = 'em';
	$font_size_max = 10;
	$font_size_min = (($font_size_max / $sec) > 1) ? $font_size_max / $sec : 1;
	// Sets maximum and minimum font-weight.
	$font_weight_max = 900;
	$font_weight_min = (($font_weight_max / $sec) > 100) ? $font_weight_max / $sec : 100;
	// Sets maximum and minimum opacity. Do not set to decimal numbers!
	$opacity_max = 99;
	$opacity_min = 20;
	// Sets maximum and minimum position. Do not move too near towards the outer boundaries, since fonts can become quite large and dissapear from the viewport!
	$position_max = 70;
	$position_min = 10;
	
	// Sets HTML-patterns with dynamic, inline CSS. Static styling is placed in the documents head or in an external stylesheet. 
	$clock_pat = "<div class=\"clock\" style=\"z-index:%d;\">%s</div>\n";
	$countdown_pat = "<h3 class=\"countdown\" style=\"top:%s;left:%s;z-index:%d;font-size:%s;font-weight:%d;opacity:.%s;filter:alpha(opacity=%s);-ms-filter:\"alpha(opacity=%s)\";-khtml-opacity:.%s;-moz-opacity:%s;\">%s</h3>\n";
	$confirm_pat = "<div id=\"msg\" style=\"z-index:%d;font-size:%s;\">\n%s</div>";
	
	// Disables all output buffering and automatically performs flush() after every print or echo.
	ob_implicit_flush(TRUE);

	// Counts until limit is reached.
	for($i = 0; $i < $limit; $i++)
	{
		// "Pulsates" until 1 is reached. Then redirects or prints confirmation message.
		if($i < $sec)
		{
			// Sets elapsed time.
			$elapsed = $sec - $i;
			
			// Start randomizing! Make sure stylings actually change by adding some changing value to them. In this case I use "+(1 / ($i + 1) * 10))" - a small dynamic value.
			srand(microtime()*1000000);
			// Font
			$font_size = rand($font_size_min,$font_size_max+(1 / ($i + 1) * 10)).$font_size_unit;
			$font_weight = rand($font_weight_min,$font_weight_max+(1 / ($i + 1) * 10));
			// Position
			$top = rand($position_min,$position_max+(1 / ($i + 1) * 10)).'%';
			$left = rand($position_min,$position_max+(1 / ($i + 1) * 10)).'%';
			// Opacity
			$opacity = rand($opacity_min,$opacity_max+(1 / ($i + 1) * 10));

			// Prints randomly styled countdown.
			printf($countdown_pat,$top,$left,$i,$font_size,$font_weight,$opacity,$opacity,$opacity,$opacity,$opacity,$elapsed);
			// Prints current date and time.
			printf($clock_pat,$i,date('r'));
			
			// "Pulsates" every 1 seconds.
			sleep(1);
		}else{
			if($confirm == FALSE)
			{
				// Redirects immediately.
				print "<meta http-equiv=\"refresh\" content=\"0; url=".$url."\">\n";
			}else{
				// Prints confirmation message, before going to destination URL.
				printf($confirm_pat,$i,($font_size_max/2).$font_size_unit,$confirm_msg);
			}
		}		
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	
	<title>Randomly styled PHP Countdown</title>
	
	<!--meta START-->
	<meta name="description" content="A styled countdown using only CSS and PHP." />
	<meta name="keywords" content="css,php,countdown,redirect" />
	<meta name="author" content="Dirk Sidney Jansen" />
	<!--meta END-->
	
	<!--styles START-->
	<style>
	html {
		height: 100%; /* Fix height to 100% for IE! */
		font-size: 100.01%; /* Correct font related browserbugs in IE, Opera and Safari. */
	}
	body {
		height: 100%; /* Fix height to 100% for IE! */
		overflow: hidden; /* Get rid of scroll-bars in IE! */
		font-family: Times,"Time New Roman",serif;
		font-size: 16px;
		background: #FEFEFE;
	}
	/* Neutralizes vertical margin styling. */
	html,body,div,ul,li,p {
		padding: 0;
		margin: 0;
	}
	/* Neutralizes anchors. */
	a {
		font-weight: 900;		
		letter-spacing: .01em;
		text-decoration: none;
		cursor: pointer;
		color: #000;
	}
	a:hover {
		color: #D22527;
	}
	/* Remove dotted links */
	a:focus {
		outline: none;
	}
	/* Lists */
	ul {
		list-style-type: none;
	}
	/* Typography */
	h2{
		font-size: 2.25em;
		line-height: 1em;
		margin: .2em;	
	}
	p,li {
		font-size: .9em;
		line-height: 1.6em;
	}
	/* Content */
	.clock,.countdown,#msg,#footer {
		position: absolute;	
	}
	.clock {
		top: 0;
		left: 0;
		padding: 1%;
		color: #FEFEFE;
		background: #554A46;
	}
	h3.countdown {
		margin: 0;
		padding: 0;
		line-height: 1em;
		background: transparent;
	}
	#msg {
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		color: #FFF;
		background: #554A46;
	}
	#msg h2{
		margin: .2em;	
	}
	#msg ul {
		float: right;
		margin: 0 .5em;
	}
	#msg a {
		color: #FFF;
		text-transform: lowercase;
		font-variant: small-caps;
	}
	#msg a:hover {
		color: #D22527;
	}
	#footer {
		bottom: 0;
		left: 0;
		width: 100%;
		z-index: 100;
		background: #FFF;
	}
	#footer p {
		margin-left: 1em;
	}
	</style>
	<!--styles END-->
</head>

<body>
<!--footer START-->
<div id="footer">
	<p>This randomly styled PHP Countdown was created by <span class="vcard"><a href="http://sidisinsane.com/" class="fn url" title="Homepage of Dirk Sidney Jansen">Dirk Sidney Jansen</a></span>. <a href="cssCountdown.txt" title="Repeat countdown">Show me the code!</a></p>
</div>
<!--footer END-->
<!--countdown START-->
<?php
// Must be placed after all other output!
cssCountdown(10,'http://sidisinsane.com/');
?>
<!--countdown END-->
</body>
</html>