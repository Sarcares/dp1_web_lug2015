<?php	/** --- logon.php --- **/
	require_once './codePiece/sessionMandatory.php';
	require_once './codePiece/intro.php';
	require_once './library/util.php';

    if( isset($loggedIn) && ($loggedIn) ):
		$ResultString = "<h2>You are already <span class='darkgray'>logged in</span>.</h2>";
	else:
		if(count($_POST)==0)
			$ResultString = "<h3>Please before visit this page go <a href='login.php'>here</a> and enter your data!</h3>";
		elseif( !validLoginValues() )
			$ResultString = "<h3>You have not entered all the data! Please go <a href='login.php'>back</a> and try again!</h3>";
		else {
			$conn = connectToDB($db_host, $db_user, $db_pass, $db_name);
			if($conn != false) {
				$user = sanitizeString($conn, $_POST['username']);
				$pass = md5( sanitizeString($conn, $_POST['password']) );	/* md5 create the hash of the password */
					
				if( validLogin($conn, "users", $user, $pass) ){
					$ResultString = "<h1><span class='darkgray'>Successful</span> Login!</h1>";
					$ResultString = $ResultString."<h3>You have been successfully logged in!</h3><h3>Click <a href='./personalPage.php'>here</a> to access to your <strong>Personal Page</strong>!</h3>";
					$_SESSION['user222325'] = $user;
					$username = $user;
					$_SESSION['pass222325'] = $pass;
					$_SESSION['time222325'] = time();
					$loggedIn = TRUE;
				}
				else {
					$ResultString = "<h3>Invalid <span class='darkgray'>username</span> or <span class='darkgray'>password</span>.</h3>
					<h3>Please go <a href='login.php'>back</a> and try again!</h3>
					<h3>If you are not register you can do a free registration <a href='./signUp.php'>here</a>!</h3>";
				}
                /** @var mysqli $conn */
                mysqli_close($conn);
			}
		}
	endif;
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
    <head>
        <title>Reservations Conference Hall</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="images/styles.css" type="text/css" />
	</head>
	
	<body>
        <div id="wrap">
            <?php require_once './codePiece/header.php'; ?>

            <div id="content-wrap">
                <img src="images/sala-congressi-resized.jpg" width="950" height="250" alt="headerphoto" class="no-border" />
                <?php require_once './codePiece/sidebar.php'; ?>

                <div id="main">
                    <?php require_once './codePiece/noscript.php';	?>
                    <?php echo $ResultString;	?>
                </div>
            </div>
            <?php include_once './codePiece/footer.php'; ?>
            <script type="text/javascript">
                document.forms[0].Username.focus();
            </script>
        </div>
	</body>
</html>
