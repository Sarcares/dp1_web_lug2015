<?php /** --- about.php --- **/
	require_once './codePiece/session.php';
	require_once './codePiece/intro.php';
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Sporting Club Pinamare</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="images/styles.css" type="text/css" />
	</head>
	
	<body>
	<div id="wrap">
  		<div id="header">
    		<h1 id="logo">Sporting<span class="gray">Club</span><span class="green">Pinamare</span></h1>
    		<h2 id="slogan">Sport &amp; Fun for whole the family!</h2>
    		<ul id="MenuAlto">
      			<li><a href="./index.php"><span>Home</span></a></li>
      			<li><a href="./activities.php"><span>Activities</span></a></li>
      			<?php if($loggedIn) {
      				echo "<li><a href='./reservations.php'><span>Reservations</span></a></li>";
      				echo "<li><a href='./logout.php'><span>Logout</span></a></li>";
      			}
      			else {
      				echo "<li><a href='./signUp.php'><span>Sign Up</span></a></li>";
      				echo "<li><a href='./login.php'><span>Login</span></a></li>";
      			}?>
      			<li id="current"><a href="./about.php"><span>About</span></a></li>
    		</ul>
  		</div>
  		
  		<div id="content-wrap">
  			<img src="images/act.jpg" width="950" height="215" alt="headerphoto" class="no-border" style="border-color: #9EC630;" />
    		<div id="sidebar">
    		    <?php if($loggedIn)
    		    	echo "<blockquote style='padding: 0 0 0 1px;'><h7>Welcome:</h7>",
    		    		"<p style='padding: 0 0 0 5px;'>$username</p></blockquote>";
    			?>
      			<h2> Options </h2>
      			<ul class="sidemenu">
	      			<li><a href="./index.php"> Home </a></li>
	      			<li><a href="./activities.php"> Activities </a></li>
	      			<?php if($loggedIn) {
	      				echo "<li><a href='./reservations.php'>Reservations</a></li>";
	      				echo "<li><a href='./logout.php'>Logout</a></li>";
	      			}
      				else {
      					echo "<li><a href='./signUp.php'> Sign Up </a></li>";
      					echo "<li><a href='./login.php'>Login</a></li>"; 
      				}?>
	      			<li><a href="./about.php"><span> About </span></a></li>
	    		</ul>
    		</div>
    		
    		<div id="main">
				<?php require_once './codePiece/noscript.php';	?>
      			<h1>Information <span class="gray">about the</span> <span class="green">Website</span> <span class="gray">and the</span> <span class="green">Sporting Club</span></h1>
      			<p>The <strong>Sporting Club Pinamare&copy;</strong> is one of the most suggestive club of the west Ligurian coast.<br>
      				It's surrounded by vast pine forest of Pinamare in <strong>Andora</strong>; a place where fascinating pine trees and typical Mediterranean plants face the sea, offering a unique and engaging landscape.</p>
				<p>The club was founded in the early 80s with the intention of creating not only a sporting club but a real social gathering where everybody can spend whole days of sport and relaxation surrounded by nature and tranquility.</p>
				<p>The club has several tennis courts, a football pitch with synthetic grass and an Olympic swimming pool. It also has a beautiful restaurant and clubhouse.</p>
				<br>				
				<div class="maps">
					<h2>Where to find us</h2>
					<p>Sporting Pinamare <br> Viale Argentina, 5 - Andora(SV) - Italy</p>
					<iframe height="350" width="500"src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d22976.63204118028!2d8.161554!3d43.957731!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x937a3ecaa7914730!2sSporting+Pinamare+Andora+Srl!5e0!3m2!1sit!2sit!4v1435158835838" style="border: 0; margin: 0 0 30px 0;"></iframe>
				</div>
				<p> <span style="color:red">Disclaimer</span>:<br> All material relating to the sporting club is owned by the <strong>Sporting Club Pinamare&copy;</strong> and I do not hold any rights on it. 
					I have chosen to represent this country club in my website simply to pay homage to my home town and to a place where I grew up and where I learned to love tennis.</p>
      			<br>
				<h3 class="green">Seascape of Andora</h3>
				<img src="images/andora.jpg" width="700" height="230" alt="Andora's seascape." class="no-border" style="margin:0 0 30px 0;" />
      		</div>
		</div>
		<?php include_once './codePiece/footer.php'; ?>
	</div>
	</body>
</html>
