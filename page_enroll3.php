<?

    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, name, games, maxpoints FROM b_bowls WHERE id = ? AND starttime >= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_games, $b_maxpoints);

    if ($query->num_rows == 0) {
?>
<br><br>

    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">The requested bowl does not exist or is not eligible for enrollment because the bowl season has already begun.  If you feel that this is an error, please contact the Bowl Contest administrator.</td>
        </tr>
    </table>
<?
        $query->close();
    } else {
        $query->fetch();
        $query->close();

        $b_fname = $_REQUEST['b_fname'];
        $b_lname = $_REQUEST['b_lname'];
        $b_dname = $_REQUEST['b_dname'];
        $b_email = $_REQUEST['b_email'];

        $query = $mysqli->prepare("INSERT INTO b_entries (bid, firstname, lastname, displayname, email, enrolltime, remaining, possible) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
        $query->bind_param('sssssss',$b_id,$b_fname,$b_lname,$b_dname,$b_email,$b_maxpoints,$b_maxpoints);
        $query->execute();
        $query->close();

        $query = $mysqli->prepare("SELECT id FROM b_entries WHERE bid = ? AND displayname = ?");
        $query->bind_param('ss',$b_id,$b_dname);
        $query->execute();
        $query->store_result();
        $query->bind_result($e_id);
        $query->fetch();
        $query->close();

        $total_games = 0;
        $total_points = 0;

        for ($i = 1; $i <= $b_games; $i++) {

            $req_string = 'game_' . $i;
            $game_winner = $_REQUEST[$req_string];
            $req_string = 'game_' . $i . '_pts';
            $game_points = $_REQUEST[$req_string];

            if ($game_winner > 0) {
                $total_games++;
            }
            $total_points += $game_points;

            $query = $mysqli->prepare("INSERT INTO b_entries_data (bid, eid, gid, team, points) VALUES (?, ?, ?, ?, ?)");
            $query->bind_param('sssss', $b_id, $e_id, $i, $game_winner, $game_points);
            $query->execute();
            $query->close();

        }

        // apparently we've processed the data -- let's see what we get here to display the confirmation.  check things

        $error = 0;
        if ($total_games != $b_games) { $error += 1; }
        if ($total_points != $b_maxpoints) { $error += 2; }
?>
        <br><br>

        <table width="70%" align="center" border="1">
            <tr>
                <td align="left">Your data has been recorded in the system and your confirmation appears below; you will also receive a copy of your confirmation and picks via email at <? echo $b_email; ?>.  If you do not receive this email soon, please contact the Bowl Contest administrator.<br><br><b>At this time, please send your $1.00 donation to David Taylor, MCSP Department (if sending from off-campus, 221 College Lane, Salem, VA 24153 is the mailing address).  Donations will be spread out amongst the first, second, and last place finishers.</b><br><br>If you wish to make any changes to this entry (as opposed to creating a second entry), please reply to the email confirmation with the changes you need made before enrollment closes.</td>
            </tr>
        </table>

        <br><br>

        <table width="70%" align="center" border="1">
            <tr>
                <td align="center" colspan="4"><b>Bowl Entry Confirmation for <? echo $b_name; ?></b></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><b>Name / Display Name:</b></td>
                <td colspan="2" align="left"><? echo $b_fname . ' ' . $b_lname . ' / ' . $b_dname; ?></td>
            </tr>
            <tr>
                <td colspan="2" align="right"><b>Email Address:</b></td>
                <td colspan="2" align="left"><? echo $b_email; ?></td>
            </tr>
            <?
            $error_msg = '';
            if ($error > 0) {
                ?>
            <tr>
                <td colspan="4" align="left">An error code of <? echo $error; ?> has be reported and sent to the Bowl System administrator; you do not need to do anything at this time except be on the lookout for an email from the administrator.</td>
            </tr>
                <?
                $error_msg = "<tr><td colspan=\"4\" align=\"left\">An error code of " .$error . " has be reported and sent to the Bowl System administrator; you do not need to do anything at this time except be on the lookout for an email from the administrator.</td></tr>";
            }
            ?>
            <tr>
                <td align="center"><b>Game</b></td>
                <td align="center"><b>Date/Time</b></td>
                <td align="center"><b>Teams (Choice Underlined/Bolded)</b></td>
                <td align="center"><b>Points</b></td>
             </tr>
<?
        $message_table = "<table width=\"80%\" align=\"center\" border=\"1\"><tr><td align=\"center\" colspan=\"4\"><b>Bowl Entry Confirmation for " . $b_name . "</b></td></tr><tr><td colspan=\"2\" align=\"right\"><b>Name / Display Name:</b></td><td colspan=\"2\" align=\"left\">" . $b_fname . " " . $b_lname . " / " . $b_dname . "</td></tr><tr><td colspan=\"2\" align=\"right\"><b>Email Address:</b></td><td colspan=\"2\" align=\"left\">" . $b_email . "</td></tr>" . $error_msg . "<tr><td align=\"center\"><b>Game</b></td><td align=\"center\"><b>Date/Time</b></td><td align=\"center\"><b>Teams (Choice Underlined/Bolded)</b></td><td align=\"center\"><b>Points</b></td></tr>";

        $query = $mysqli->prepare("SELECT g.name, g.gametime, g.team1, g.team2, e.team, e.points FROM b_entries_data as e, b_games as g WHERE g.bid = e.bid AND e.bid = ? AND g.gid = e.gid AND e.eid = ? ORDER BY g.id ASC");
        $query->bind_param('ss',$b_id, $e_id);
        $query->execute();
        $query->store_result();
        $query->bind_result($gg_name, $gg_gametime, $gg_team1, $gg_team2, $gg_pick, $gg_points);

        while ($query->fetch()) {
            if ($gg_pick == 1) { $gg_team1 = "<u><b>" . $gg_team1 . "</b></u>"; } else { $gg_team2 = "<u><b>" . $gg_team2 . "</b></u>"; }
?>
            <tr>
                <td align="center"><? echo $gg_name; ?></td>
                <td align="center"><? echo date('l, F j, Y', strtotime($gg_gametime)) . '<br>' . date('g:i A', strtotime($gg_gametime)); ?></td>
                <td align="center"><? echo $gg_team1; ?><br><? echo $gg_team2; ?></td>
                <td align="center"><? echo $gg_points; ?></td>
            </tr>
<?
            $message_table .= "<tr><td align=\"center\">" . $gg_name . "</td><td align=\"center\">" . date('l, F j, Y', strtotime($gg_gametime)) . "<br>" . date('g:i A', strtotime($gg_gametime)) . "</td><td align=\"center\">" . $gg_team1 . "<br>" . $gg_team2 . "</td><td align=\"center\">" . $gg_points . "</td></tr>";

        }
        $query->close();

        $message_table .= "</table>";
?>
        </table><br><br>
<?

        $message = "<html><body>Dear " . $b_fname . ":<br><br>You recently created an entry for the " . $b_name . " bowl contest and your entry has been received successfully.  Your selected teams and point allotments are below.  You will receive updates to the contest at this email address as the contest proceeds, beginning with a list of all picks for participants once the bowls get underway.<br><br>Note that you should send your $1.00 donation to David Taylor, MCSP Department, as soon as possible (from off-campus, donations can be sent to him at 221 College Lane, Salem, VA 24153).<br><br>" . $message_table . "<br>If you wish to make any changes to this entry (as opposed to creating a second entry), please reply to this email with the changes you need made before enrollment closes.</body></html>";

        require '/etc/PHPMail/PHPMailer-master/PHPMailerAutoload.php';

        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'receive.roanoke.edu';  // Specify main and backup server
        $mail->SMTPAuth = false;                               // Enable SMTP authentication

        $mail->From = 'taylor@roanoke.edu';
        $mail->FromName = 'RC NCAA Bowl System';

        $mail->addAddress($b_email, $b_fname . " " . $b_lname);  // Add a recipient
        $mail->addCC('taylor@roanoke.edu', 'David Taylor');

        $mail->addReplyTo('taylor@roanoke.edu', 'RC NCAA Bowl System');

        $mail->WordWrap = 80;                                 // Set word wrap to 50 characters
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'RC NCAA Bowl System - Entry Confirmation';
        $mail->Body    = $message;
        $mail->AltBody = $message;

        $mail->send();

    }


?>



