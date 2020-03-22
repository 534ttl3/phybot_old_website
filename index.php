
<?php


			function getonlylastpartafterslash($item) {
				return substr($item, strrpos($item, '/') + 1);
			}

			function is_contentarticle($item) {
				if(is_file($item)) {
					$str =  getonlylastpartafterslash($item);
					return !(substr($str, 0,1) === '_' );	
				}
				return false;
			}
			function is_contentdir($item) {
				if(is_dir($item)) {
					$str =  getonlylastpartafterslash($item);
					return !(substr($str, 0,1) === '_' );	
				}
				return false;
			}


			function PrintFolderStructure($item, $level) {	
				$valid = 0;
				if(is_contentdir($item) && $item != "./contents") {
					$valid=1;

					$hierarchypadding = $level * 20;
					$paddingstr = $hierarchypadding."px;";

					$cleanstring = str_replace('_', ' ', getonlylastpartafterslash($item));
					echo '<div class="side-nav-submenu"> <div  class="side-nav-elem side-nav-subcategory-header" style="padding-left:'.$paddingstr.'">'.$cleanstring.'</div>';
				}
				else if(is_contentarticle($item)) {
							
						$valid=1;
						$hreflink = "?article=".$item;
						$hierarchypadding = $level * 20;
						$paddingstr = $hierarchypadding."px;";
						$cleanstring = str_replace('_', ' ', getonlylastpartafterslash($item));
						echo '<a class="side-nav-elem" href="'.$hreflink.'"><div style="padding-left:'.$paddingstr.'">'.$cleanstring.'</div></a>';
					
				}
				if(is_contentdir($item)) {
						$arr = scandir($item);
						for($i=0, $c = count($arr); $i<$c; $i++) {
							if($arr[$i] != '.' && $arr[$i] != '..'){
								PrintFolderStructure($item."/".$arr[$i], $level+1);
							}
						} 
				}

				if(is_contentdir($item) && $item != "./contents") {
					$valid=1;
					echo '</div>';
				}
			}

// initial values
$content = "";
$content_valid = false;
$title = "";

if($_SERVER['QUERY_STRING'] != "") {

	if(isset($_GET['article'])) {
		// todo: this is extremely easy to exploit
		$content = @file_get_contents($_GET['article']); // @ suppresses the warning
		if($content == false) {
			$content_valid = false;
			$content .= "<br/> Sorry, but this content request is invalid <br/>";
			$title = "invalid request";
		} else {
			$title_cleanstring = str_replace('_', ' ', getonlylastpartafterslash($_GET['article']));
			$title = $title_cleanstring;
			$content_valid = true;
		}
	}
} else {
	$content_valid = true;
	$content = '<div style="padding:15px;">
					 <h3> Hey Visitor from a foreign galaxy! </h3>  
					 <p> Open up the navigation drawer on the left hand side to unleash the power. </p>
				</div>';
}

if(!$content_valid) {
	echo "This content reference does not exist (or the file has no contents). Please proceed on to the <a href=\"/\">Main Page</a> ";
	//header( "refresh:5;url=/index.php" );
} else {
	//---ENCAPSULATES HTML BEGIN---//

?>

<!DOCTYPE html>
<html>
<head>
	<title>
		<?php $title.= " - PhyBot"; echo $title; ?>
	</title>
	<meta name="keywords" content="
	<?php echo $title; ?>
	" />
	<meta name="description" content="
	<?php echo $title; ?>
	" />
	<link rel="icon" type="image/png" href="/images/saturnfavicon.png" />


	<meta charset="utf-8"/>
	<meta http-equiv="Content-Type" content="text/html;"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	 <script src="jquery.min.js"></script>
	<?php 
		if(isset($_GET['article'])) {

		}
	?>
<meta name="theme-color" content="#000000"/>

	<style>
		html, body {
			margin: 0;       
			font-family:"RobotoDraft","Roboto",sans-serif;
			font-weight:100;
			line-height:2.1em;
			font-size:14px;
		}
		html, body { overflow: auto; background:#000; }
		body { margin:0; padding:0; }
		h1 {
			font-size:200%;

		}
		p {
			font-size:110%;
		}
		img {
			user-select: none;
		}
		.side-nav-drawer {
			position:fixed;
			visibility: hidden;
			font-size:100%;
			overflow-y: auto;
			overflow-x: hidden;
			top:0px;
			width: 250px;
			height:100%;
			background:#FFF;
			-webkit-transition: 0.23s -webkit-transform ease-out;
		}
		.side-nav-drawer-open {
		}
		.side-nav-drawer-closed {
		}

		a.side-nav-elem  {
			display:block;
			font-weight:thin;
			line-height: normal;
			text-decoration:none;
			color:#01476f;
			padding:12px 0px 12px 0px;
		}
		
		a.side-nav-elem:hover {
			/* transition: background 0.3s ease-out; */
			background:rgba(0, 0, 255, 0.15);
		}

		.side-nav-submenu:hover {
			background:rgba(0, 0, 255, 0.20);
		}

		.side-nav-subcategory-header {
			padding:12px 0px 12px 0px;	
			cursor: pointer;
			font-weight: bold;
		}

		#nav-overlay {
			position:fixed;
			height:200%;
			width:200%;
			left:0px;
			top:0px;
			background:rgba(0,0,0,0.63);
			display:none;
		}
		.nav-drawer-icon-button {
			position:absolute;
			height:100%;
			width:40px;
			top: 50%;
			-webkit-transform: translateY(-50%);
		}
		.actionbarwrapper {
			position:absolute;	/*  change this so that the action bar 
								    stays fixed */
			width:100%;
			height:55px;
			top:0px;
			left:0px;
			background: #000;
		}
		.actionbar {
			position:relative;    /* so that it doesnt vanish behind the contentwrapperwrapper*/
			height:100%;
		}
		.contentwrapperwrapper {	/* wraps the content wrapper ;) */
			position: relative;
			width:100%;
			background:#FFF;		/* dark mode todo: implement light/dark mode without using cookies/saving user-specific data */
		}

		.content {
			overflow-x: auto;
			margin: 0 auto;		/* center horizontally */
			max-width: 1000px;
			color:#000;
			font-size: 100%;
		}
		.actionbar_elem {
			float:left;
			position:relative;
			background:rgba(0, 0, 255, 0.5);
			min-width:30px;
			height:85%;
			top: 50%;
			-webkit-transform: translateY(-50%);
			padding-left:20px;
			padding-right:20px;
		}

		.div_button:hover { 
			cursor: pointer;
		}

		.contentwrapper {
			margin-top:55px;
		}

		#side_nav_drawer_menubutton {
			background:rgba(0,0,0,0.1) url(images/ic_menu_white_24px.svg) no-repeat 50% 50%;
		}

		@media only screen and (max-width:600px){
		}

		@media only screen and (min-width:1250px){

			.div_button {
				display:none;
			}

			.side-nav-drawer {
				-webkit-transform: translateX(100%) !important;
			}
			#nav-overlay {
				display:none !important;
			}
			.contentwrapper {
				margin-left:250px;
			}
			.actionbar {
				margin-left:250px;
			}
		}

		.infofooter {
			/* border-radius: 10px 0px 0px 0px; */ 
			position: relative;
			display:inline-block;
			color: #EEE;
			font-size:70%;  
			font-family: Arial, Helvetica, sans-serif; 
			background: black;
		}


		.phybotlogo {
			 float:right;width:100px;
			 background:url(images/phybotwithtextwhite.svg) 50% 50% no-repeat;
			 background-size:75%;
		}

		.active-nav-elem {
			background:rgba(0,0,0,0.2) !important;
		}


		body.noscroll{
			height: 100%;
		    width: 100%;
		    position:fixed;
		    overflow:hidden;
		}

	</style>
</head>
<body>
	<div class="contentwrapperwrapper">

		<div id="clear" style="width:100%; height:1px;"></div>

		<div class="contentwrapper">
		<div id="clear" style="width:100%; height:1px;"></div>

			<div class="content">
				<div id="clear" style="width:100%; height:1px;"></div>

				<?php

				echo $content;	

				?>

				<div id="clear" style="width:100%; height:1px;"></div>
			</div>
		</div>
	</div>


	<div id="infofooter">
		<div style="float:right; padding-left:10px; padding-right:10px">
			<a href="legalterms/l3g4lt3rm5.html" target="_blank" style="text-decoration:none; color:grey;" > 
				Datenschutz, Disclaimer, Impressum 
			</a>
		</div> 
	</div>

	<nav class="actionbarwrapper" >
		<div class="actionbar">
			<div id="side_nav_drawer_menubutton" class="actionbar_elem div_button">
			</div>
			<a href="/">
				<div class="actionbar_elem phybotlogo"></div>
			</a>
		</div>
	</nav>
	<div id="nav-overlay" style=" display: none;"></div>
	<div class="side-nav-drawer" style=" display: block;">
			<?php


			PrintFolderStructure("./contents", 0);

			?>


	</div>
	<script>
		function ShowHideNavDrawer() {

			// this toggleClass goes before all other things
			$( "body" ).toggleClass("noscroll");
			if (  $( ".side-nav-drawer" ).css( "-webkit-transform" ) == 'none' ){
				if(  $("#nav-overlay").css( "display" ) == "none"   )
					$("#nav-overlay").css("display", "block");
				$(".side-nav-drawer").css("-webkit-transform","translate("+ $(".side-nav-drawer").width() +"px)");
			} else {
				if(  $("#nav-overlay").css( "display" ) == "block"  ) {
					$("#nav-overlay").css("display", "none");
				}
				$(".side-nav-drawer").css("-webkit-transform","" );
			}

		}

		// pulsate
		$.fn.pulse = function(options) {
			var options = $.extend({
				times: 3,
				duration: 1000
			}, options);
			
			var period = function(callback) {
				$(this).animate({opacity: 0}, options.duration, function() {
					$(this).animate({opacity: 1}, options.duration, callback);
				});
			};
			return this.each(function() {
				var i = +options.times, self = this,
				repeat = function() { --i && period.call(self, repeat) };
				period.call(this, repeat);
			});
		};

	function getParameterByName(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
			var navdrawerpixelshift =  "-" + $(".side-nav-drawer").width() + "px";
			$( ".side-nav-drawer" ).css("left", navdrawerpixelshift).css("display", "block");

			$( "a.side-nav-elem" ).click(function() {
			    	ShowHideNavDrawer();
			});
			$( "#side_nav_drawer_menubutton" ).click(function() {
				ShowHideNavDrawer();
			});
			$( "#nav-overlay" ).click(function() {
				ShowHideNavDrawer();
			});


			// pulsate
			$("#side_nav_drawer_menubutton").pulse({times: 2, duration: 1000});


	function myTrim(x) {
	    return x.replace(/^\s+|\s+$/gm,'');
	}

	
if(getParameterByName("article")){

	var tarticlestring = getParameterByName("article");
	var lastSegment = tarticlestring.split('/').pop();
	var tarticlestring2 = lastSegment.split('_').join(" ");
	var articlestring = myTrim(tarticlestring2);
	//alert("." + tarticlestring + "," + lastSegment + "," + tarticlestring2 + "," + articlestring + ".");

	// find the specific articlestring div
	var $articlestringdiv = $('a.side-nav-elem > div').filter(function() {
		if(myTrim( $(this).text() ) === articlestring) {
			//alert("true");
			return true;
		}
		return false;
	});

	//alert("end" + $articlestringdiv.html());

	$articlestringdiv.parent("a").addClass("active-nav-elem");
	var $articlecategorydiv =  $articlestringdiv.parent("a").parent("div.side-nav-submenu").children("div.side-nav-subcategory-header");
	//$articlecategorydiv.nextAll().toggle();
		// toggle them fast, so that the scrollTop after that gets the right values
	$(".side-nav-subcategory-header").not($articlecategorydiv).nextAll().toggle(0);



	var scroll = $articlecategorydiv.position().top;
	// Scroll the current category to the top of the view
	$(".side-nav-drawer").scrollTop( scroll );

} else {
	$(".side-nav-subcategory-header").nextAll().toggle(0);
}
	// get current article from url
	//var currentarticledirty = getParameterByName('article');
	//var currentarticleclean = str_replace('_', ' ', currentarticledirty);


// now, at the bottom of the page, everything is loaded, I set up the event listeners
	// to be able to switch categories on/off
	$(".side-nav-subcategory-header").click(function() {
		$(this).nextAll().toggle("fast");
	});



// fix initial css 

// prevent initial flicker of toggle menu
$('.side-nav-drawer').css('visibility', 'visible');

	</script>


</body>
</html>

<?php 
}
//---ENCAPSULATES HTML END---//
?>
