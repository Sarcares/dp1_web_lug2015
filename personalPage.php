<?php	/** --- personalPage.php --- **/
	require_once './codePiece/sessionMandatory.php';
	require_once './codePiece/intro.php';
	require_once './library/util.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reservations Conference Hall</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="images/styles.css" type="text/css" />
        <script type="text/javascript" src="library/checks.js"></script>
        <script type="application/javascript" src="library/graphics.js" ></script>
    </head>
	
	<body>
	<div id="wrap">
        <?php require_once './codePiece/header.php'; ?>
  		
  		<div id="content-wrap">
            <img src="images/sala-congressi-resized.jpg" width="950" height="250" alt="headerphoto" class="no-border" />
            <?php require_once './codePiece/sidebar.php'; ?>
    		
    		<div id="main">
    		<?php require_once './codePiece/noscript.php';	?>
    		<?php if($loggedIn):

    			if(count($_POST)!==0) {
    /**********************************/
    /******* REMOVE RESERVATION *******/
    /**********************************/
                    if(isset($_POST['id'])) {
                        $id = $_POST['id'];

                        /** @var mysqli $conn */
                        $conn = connectToDB($db_host, $db_user, $db_pass, $db_name);
                        if($conn !== false) {
                            $id = sanitizeString($conn, $id);
                            try {
                                if(!mysqli_autocommit($conn, FALSE))
                                    throw new Exception("DEBUG - Impossible to set autocommit to FALSE");

                                $res = mysqli_query($conn, "SELECT * FROM booking WHERE id=$id AND username='$username' FOR UPDATE ");
                                if(!$res)	# Fetch data from the database
                                    throw new Exception("DEBUG - Query 1 (fetch reservation's info) failed!");
                                $row = mysqli_fetch_array($res);
                                if($row==NULL)
                                    throw new Exception("<p class='red'>The desired reservation does not exist anymore!</p>");
                                mysqli_free_result($res);

                                $res = mysqli_query($conn, "DELETE FROM booking WHERE id=$id");
                                if(!$res)	# Remove reservation from the database
                                    throw new Exception("DEBUG - Query 2 (delete reservation) failed!");

                                if(!mysqli_commit($conn))
                                    throw new Exception("<p style='color:red'>Impossible to commit the operation!</p>");

                                if(!mysqli_autocommit($conn, TRUE))
                                    throw new Exception("DEBUG - Impossible to set autocommit to TRUE");
                            }
                            catch (Exception $e) {
                                mysqli_rollback($conn);
                                mysqli_autocommit($conn, TRUE);
                                echo $e->getMessage();
                            }
                            mysqli_close($conn);
                        }
                    }
	/*******************************/
	/******* ADD RESERVATION *******/
	/*******************************/
    				elseif( areReservationValuesSet() )
                    {
                        if( areReservationValuesEmpty() ) {
                            echo "<p style='color:red'>You have not entered all the data necessary for the registration!</p>";
                        }
                        else {
                            $name = $_POST['name'];         $part = $_POST['participants'];
                            $sHour = $_POST['StartHour'];   $sMinute = $_POST['StartMinute'];
                            $eHour = $_POST['EndHour'];     $eMinute = $_POST['EndMinute'];

                            $conn = connectToDB($db_host, $db_user, $db_pass, $db_name);
                            if($conn!==false) {
                                $name = sanitizeString($conn, $name);
                                $part = (int) sanitizeString($conn, $part);
                                $sHour = sanitizeString($conn, $sHour);
                                $eHour = sanitizeString($conn, $eHour);
                                $sMinute = sanitizeString($conn, $sMinute);
                                $eMinute = sanitizeString($conn, $eMinute);
                                if( areReservationValuesOk($part, $sHour, $eHour, $sMinute, $eMinute) ) {
                                    $start = $sHour.":".$sMinute.":00";
                                    $end = $eHour.":".$eMinute.":00";
                                    try {
                                        /** @var mysqli $conn **/
                                        if(!mysqli_autocommit($conn, FALSE))
                                            throw new Exception("DEBUG - Impossible to set autocommit to FALSE");

                                        //in other case it's also possible to use the LOCK TABLE but we don't have administrator's privilege
                                        $res = mysqli_query($conn, "SELECT * FROM booking WHERE '$start' < end_time AND '$end' > start_time FOR UPDATE");
                                        if(!$res)	/* FOR UPDATE - lock the table for preventing a concurrency access */
                                            throw new Exception("DEBUG - Query 1 (check availability) failed!");                                                                              
                                        
                                        
                                        $row = mysqli_fetch_array($res);		    #Fetching all the records
                                        $i = 0;
                                        $tot[0] = 0;
                                        while ($row != NULL) {                                    
                                        	$startTime[$i] = $row['start_time'];
                                        	$endTime[$i] = $row['end_time'];
                                        	$parts[$i] = $row['participants'];
                                        	$i++;
                                        	
                                        	$row = mysqli_fetch_array($res);
                                        }
                                        mysqli_free_result($res);
                                        $N = $i;

                                        for($i = $sHour; $i <= $eHour; $i++) {      #checking minute by minute
                                            if($sHour == $eHour) {          #if are equal I will check only the minutes
                                                $sm = $sMinute;
                                                $em = $eMinute;
                                            }
                                            else {
                                                if ($i == $sHour) {         #This is the first case, I'm starting from the user's minute
                                                    $sm = $sMinute;
                                                    $em = 60;
                                                } else {
                                                    if ($i == $eHour) {     #This is the last case, I'm ending in the user's minute
                                                        $sm = 0;
                                                        $em = $eMinute;
                                                    } else {                #This is the default case, I will check all the hour
                                                        $sm = 0;
                                                        $em = 60;
                                                    }
                                                }
                                            }
                                            for ($j = $sm; $j < $em; $j++) {    #cycling on the hours
                                                $tot = 0;
                                                $time = $i.":".$j.":00";
                                                for ($conf = 0; $conf < $N; $conf++) {  #cycling on the minutes
                                                    $x = strtotime($time);
                                                    $xEnd = strtotime($endTime[$conf]);
                                                    $xStart = strtotime($startTime[$conf]);
                                                    if (($x < $xEnd) && ($x >= $xStart))
                                                        $tot += $parts[$conf];

                                                    if( ($tot+$part) > ROOMSIZE ) {     # Checking the availability
                                                        #echo("<p>DEBUG: i=$i j=$j tot=$tot </p>");
                                                        throw new Exception("<p style='color:red'>Reservation avoided! There are not enough places for your reservation at the specified time!</p>");
                                                    }
                                                }
                                                #echo("<p>DEBUG: i=$i j=$j tot=$tot </p>");
                                            }
                                        }
                                       
                                   /*   --- Removed because it is not into the assignment's specifications ---
                                        $res = mysqli_query($conn, "SELECT * FROM booking WHERE username='$username' AND '$start' < end_time AND '$end' > start_time FOR UPDATE");
                                        if(!$res)   #for preventing the case when more users have access to the same account
                                            throw new Exception("DEBUG - Query 2 (check previous reservations in the same time slot) failed!");
                                        $row = mysqli_fetch_array($res);
                                        mysqli_free_result($res);
                                        if($row!=NULL)	# No more then 1 reservation for each user in the same time slot
                                            throw new Exception("<p style='color:red'>Invalid condition! You can't have 2 overlapped reservations!</p>");
                                    */
                                        $res = mysqli_query($conn, "INSERT INTO booking (name, username, participants, start_time, end_time) VALUES ('$name', '$username', '$part', '$start', '$end');");
                                        if(!$res)
                                            throw new Exception("DEBUG - Query 3 (insert reservation) failed!");

                                        if(!mysqli_commit($conn))
                                            throw new Exception("<p style='color:red'>Impossible to commit the operation!</p>");

                                        if(!mysqli_autocommit($conn, TRUE))
                                            throw new Exception("DEBUG - Impossible to set autocommit to TRUE");
                                    }
                                    catch (Exception $e) {
                                        mysqli_rollback($conn);
                                        mysqli_autocommit($conn, TRUE);
                                        echo $e->getMessage();
                                    }
                                }
                                mysqli_close($conn);
                            }
                        }
    				}
	    		}
    			?>

      			<h1>Your Conferences</h1>
                <?php
    /************************************/
    /******* DISPLAY RESERVATIONS *******/
	/************************************/
                    $conn = connectToDB($db_host, $db_user, $db_pass, $db_name);
      				if($conn !== false) {    # fetch the reservations
                        $res = mysqli_query($conn, "SELECT * FROM booking WHERE username='$username' ORDER BY participants DESC");
                        if (!$res):
                            echo "<p class='red'>Error during the download of the reservations!</p>";
                        else:
                            $row = mysqli_fetch_array($res);
                            if ($row == NULL)
                                echo "<BLOCKQUOTE><P><span class='darkgray'>At the moment, you do not have reservations.</span></P></BLOCKQUOTE>";
                            else {
                                $i = 0;
                                while ($row != NULL) {
                                    $id = $row['id'];
                                    $name = $row['name'];
                                    $participants = $row['participants'];
                                    $start = $row['start_time'];
                                    $end = $row['end_time'];
                                    echo "<form id='reservation$i' action='./personalPage.php' method='post'>";
                                    echo "<TABLE>",
                                        "<TR><TH><h6 style='text-align: left'>$name</h6></TH><TH><input name='id' value='$id' type='text' readonly style='display:none'/></TH></TR>",
                                    "<TR><TD>Number of participants = <span class='cyan'>$participants</span></TD><TD>&nbsp;</TD>",
                                    "<TR><TD> Start at <span class='cyan'>".substr($start, 0, -3)."</span> </TD><TD> End at <span class='cyan'>".substr($end, 0, -3)."</span> </TD></TR>",
                                    "<TR><TD>&nbsp;</TD><TD><input class='button' id='remove$i' type='submit' value='Remove Reservation' style='margin-left: 20px;'/></TD></TR>";
                                    echo "</TABLE></form>";
                                    $row = mysqli_fetch_array($res);
                                    $i++;
                                }
                            }
                            mysqli_free_result($res);
                        endif;
                    }

                ?><br><h1>Today Conferences</h1>
                <?php
    /***********************************/
    /******* DISPLAY CONFERENCES *******/
    /***********************************/
                    $res = mysqli_query($conn, "SELECT * FROM booking WHERE username <> '$username' ORDER BY participants DESC, name ASC");
                    if (!$res):
                        echo "<p>Error during the download of the reservations!</p>";
                    else:
                        $row = mysqli_fetch_array($res);
                        if($row==NULL)
                            echo "<BLOCKQUOTE><p><span class='darkgray'>There are no reservations right now.</span></h3></p></BLOCKQUOTE>";
                        else {
                            while ($row != NULL) {
                                echo "<h2><span class='darkgray'>Conference: </span>".$row['name']."</h2>",
                                    "<p>Reserved by: <span class='cyan'>".$row['username']."</span><br>",
                                    "The conference starts at: <span class='cyan'>".$row['start_time']."</span> and end at: <span class='cyan'>".$row['end_time']."</span><br>",
                                    "Number of participants to the conference: <span class='cyan'>".$row['participants']."</span> </p><br>";
                                $row = mysqli_fetch_array($res);
                            }
                        }
                        mysqli_free_result($res);
                    endif;
                ?>
                <br>
                <h1>Reserve a Conference</h1>
<?php   /************************************/
        /******* RESERVE A CONFERENCE *******/
        /************************************/ ?>
                    <blockquote>
                    <form id='Reservation' action='./personalPage.php' method='post'>
                        <table class="tabella">
	        			    <tr>
                                <td><label for="Name"> Conference Title: </label></td>
                                <td><input type="text" id="Name" name="name" maxlength="36" placeholder="Insert a title for your conference" style="width: 220px;"></td>
                            </tr>
                            <tr>
                                <td><label for="Participants"> Number of Participants: </label></td>
                                <td><input type="number" id="Participants" name="participants" min="1" max="<?php echo ROOMSIZE ?>" placeholder="<?php echo 'between [1-'.ROOMSIZE.']'; ?>"
                                           style="text-align:center; width:135px;"></td>
                            </tr>
                            <tr>
                                <td><label for="StartHour" style="display: inline"> Starting Hour: </label>     <?php dropDownMenu(('StartHour'), 0, 23); ?> <span id='TooltipStartHour' class="tooltip"> Select the Starting Hour </span> </td>
                                <td><label for="StartMinute" style="display: inline"> Starting Minute: </label> <?php dropDownMenu(('StartMinute'), 0, 59); ?> <span id='TooltipStartMinute' class="tooltip"> Select the Starting Minute </span> </td>
                            </tr>
                            <tr>
                                <td><label for="EndHour" style="display: inline"> Ending Hour: </label> &nbsp;  <?php dropDownMenu(('EndHour'), 0, 23); ?> <span id='TooltipEndHour' class="tooltip"> Select the Ending Hour </span> </td>
                                <td><label for="EndMinute" style="display: inline"> Ending Minute: </label> &nbsp;&nbsp;<?php dropDownMenu(('EndMinute'), 0, 59); ?> <span id='TooltipEndMinute' class="tooltip"> Select the Ending Minute </span> </td></tr>
                            </tr>
                        </table>
                        <br>
	        			<button type="submit" class="button" onclick="javascript:return checkConferenceValues()"> Reserve </button>
                        <button type="button" class="button" onclick="resetForm()"> Reset </button>
     				</form>
     				</blockquote>
      			
      		<?php else: ?>
      			<h3>You can't see this page if you are not a registered user!</h3>
      			<h4>Go <strong><a href='signUp.php'>here</a></strong> for create a new account!</h4>
      			<h4>If you are already registered, please <strong><a href='login.php'>log in</a></strong>!</h4>
      		<?php endif; ?>
      		</div>
		</div>
  		
	  	<?php include_once './codePiece/footer.php'; ?>
	</div>

    <script type="text/javascript">
        setCurrent(document.getElementById("PersonalPage"));
        setSpan(document.getElementById("personalpage"), "Personal Page");

        function resetForm(){
            document.getElementById('Reservation').reset();
            document.getElementById("Name").focus();
        }

        function showTooltip(ObjId) {
            ObjId.style.visibility="visible";
        }

        function hideTooltip(ObjId) {
            ObjId.style.visibility="hidden";
        }
    </script>

	</body>
</html>