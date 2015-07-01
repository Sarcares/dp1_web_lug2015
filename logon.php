<?php	/** --- logon.php --- **/
	require_once './codePiece/sessionMandatory.php';
	require_once './codePiece/intro.php';
	require_once './library/util.php';
	
	if($loggedIn):
		$ResultString = "<h2>You are already <span class='green'>logged in</span>.</h2>";
	else:
		if(count($_POST)==0)
			$ResultString = "<h3>Please before visit this page go <a href='login.php'>here</a> and enter your data!</h3>";
		elseif( !validLoginValues() )
			$ResultString = "<h3>You insert some invalid data! Please go <a href='login.php'>back</a> and try again!</h3>";
		else {
			$conn = connectToDB($db_host, $db_user, $db_pass, $db_name);
			if($conn !== false) {
				$user = sanitizeString($conn, $_POST['username']);
				$pass = md5( sanitizeString($conn, $_POST['password']) );	/* md5 create the hash of the password */
					
				if( validLogin($conn, "users", $user, $pass) ){
					$ResultString = "<h1>Succesful <span class='green'>Login</span>!</h1>";
					$ResultString = $ResultString."<h3>You have been succesfully logged in!</h3><h3>Click <a href='./reservations.php'>here</a> to book an activity!</h3>";
					$_SESSION['user'] = $user;
					$username = $user;
					$_SESSION['pass'] = $pass;
					$_SESSION['time'] = time();
					$loggedIn = TRUE;
				}
				else {
					$ResultString = "<h3>Invalid <span class='green'>username</span> or <span class='green'>password</span>.</h3>
					<h3>Please go <a href='login.php'>back</a> and try again!</h3>
					<h3>If you are not register you can do a free registration <a href='./signUp.php'>here</a>!</h3>";
				}
				mysqli_close($conn);
			}
		}
	endif;
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Sporting Club Pinamare</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="images/styles.css" type="text/css" />
		<script type="text/javascript" src="./library/functions.js"></script>
	</head>
	
	<body onload="javascript: document.forms[0].Username.focus();">
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
      				echo "<li id='current'><a href='./login.php'><span>Login</span></a></li>";
      			}?>
      			<li><a href="./about.php"><span>About</span></a></li>
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
	      				echo "<li><a href='./reservations.php'> Reservations </a></li>";
	      				echo "<li><a href='./logout.php'> Logout </a></li>";
	      			}
      				else {
      					echo "<li><a href='./signUp.php'> Sign Up </a></li>";
      					echo "<li><a href='./login.php'><span> Login </span></a></li>"; 
      				}?>
	      			<li><a href="./about.php"> About </a></li>
	    		</ul>
    		</div>
    		
    		<div id="main">
    			<?php require_once './codePiece/noscript.php';	?>
				<?php echo $ResultString;	?>
      		</div>
		</div>
		<?php include_once './codePiece/footer.php'; ?>
	</div>
	</body>
</html>
