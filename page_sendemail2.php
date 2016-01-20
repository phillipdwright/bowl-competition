<?

    $bid = $_REQUEST['bid'];
    $subject = $_REQUEST['e_subject'];
    $greeting = $_REQUEST['e_greeting'];
    $incl = $_REQUEST['e_include'];
    $message = $_REQUEST['e_message'];
    $board = $_REQUEST['e_board'];

    $msg1 = "<html><body>";
    $msg2 = '';
    $msg3 = $message;
    $msg35 = '';
    $msg4 = '';
    $msg5 = "</body></html>";

    if ($board == 1) { $msg35 = "<br><br>View and post on the <a href=\"http://math.roanoke.edu/bowl/index.php?action=comments&bid=" . $bid . "\">discussion board here</a>."; }

    if ($incl == 1) {
        $query = $mysqli->prepare("SELECT id, name, lastupdate, games FROM b_bowls WHERE id = ?");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_id, $b_name, $b_updated, $b_games);
        $query->fetch();
        $query->close();

        $msg4 .= "<br><br><table border=\"1\"><tr><td></td>";

        $query = $mysqli->prepare("SELECT name, gametime FROM b_games WHERE bid = ? ORDER BY gid ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($g_name, $g_gametime);
        while ($query->fetch()) {
            $msg4 .= "<td>" . $g_name . "<br>" . date('n/j/y @ g:i A', strtotime($g_gametime)) ."</td><td></td>";
        }
        $query->close();
        $msg4 .= "</tr>";

        $query = $mysqli->prepare("SELECT displayname, id FROM b_entries WHERE bid = ? ORDER BY displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_dname, $e_id);
        while ($query->fetch()) {
            $msg4 .= "<tr><td>" . $b_dname . "</td>";
            $query2 = $mysqli->prepare("SELECT g.team1, g.team2, e.team, e.correct, g.winner, e.points FROM b_entries_data e, b_games g WHERE e.bid = g.bid AND e.gid = g.gid AND e.eid = ? ORDER BY g.gid ASC");
            $query2->bind_param('s', $e_id);
            $query2->execute();
            $query2->store_result();
            $query2->bind_result($g_team1, $g_team2, $e_team, $e_correct, $g_winner, $e_points);

            while ($query2->fetch()) {
                if ($e_team == 1) {
                    $teamname = $g_team1;
                } else {
                    $teamname = $g_team2;
                }
                $msg4 .= "<td>" . $teamname . "</td><td>" . $e_points . "</td>";
            }
            $query2->close();
            $msg4 .= "</tr>";
        }
        $query->close();

    } else if ($incl == 2) {

        $query = $mysqli->prepare("SELECT id, name, lastupdate FROM b_bowls WHERE id = ?");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_id, $b_name, $b_updated);
        $query->fetch();
        $query->close();

        $msg4 .= "<br><br><table width=\"90%\" align=\"center\" border=\"2\"><tr><td align=\"left\" colspan=\"3\"><b>Current Standings for " . $b_name . ", last updated " . date('l, F j, Y @ g:i A', strtotime($b_updated)) . "</b></td></tr><tr><td align=\"left\" colspan=\"3\">This page has three big columns, standings as of the last updated date and time shown above; the first contains the current standings sorted by display name.  The second is sorted by current point totals, and the last is sorted by points possible.</td></tr><tr><td><table width=\"100%\" align=\"center\" border=\"1\"><tr><td colspan=\"4\"><b>Standings by Username</b></td></tr><tr><td><b>Name</b></td><td><b>Points</b></td><td><b>Remaining</b></td><td><b>Possible</b></td></tr>";

        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible FROM b_entries WHERE bid = ? ORDER BY displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible);

        while ($query->fetch()) {
            $msg4 .= "<tr><td>" . $b_name . "</td><td align=\"center\">" . $b_points . "</td><td align=\"center\">" . $b_remaining . "</td><td align=\"center\">" . $b_possible . "</td></tr>";
        }
        $query->close();

        $msg4 .= "</table></td><td><table width=\"100%\" align=\"center\" border=\"1\"><tr><td colspan=\"5\"><b>Standings by Current Points</b></td></tr><tr><td></td><td><b>Name</b></td><td><b>Points</b></td><td><b>Remaining</b></td><td><b>Possible</b></td></tr>";

        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible, pos_points FROM b_entries WHERE bid = ? ORDER BY points DESC, displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible, $b_pos);

        $prev = -1;
        $pos_dis = "";
        while ($query->fetch()) {
            if ($prev == $b_pos) { $pos_dis = ""; } else { $pos_dis = $b_pos; $prev = $b_pos; }
            $msg4 .= "<tr><td align=\"center\">" . $pos_dis . "</td><td>" . $b_name . "</td><td align=\"center\">" . $b_points . "</td><td align=\"center\">" . $b_remaining . "</td><td align=\"center\">" . $b_possible . "</td></tr>";
        }
        $query->close();

        $msg4 .= "</table></td><td><table width=\"100%\" align=\"center\" border=\"1\"><tr><td colspan=\"5\"><b>Standings by Points Possible</b></td></tr><tr><td></td><td><b>Name</b></td><td><b>Points</b></td><td><b>Remaining</b></td><td><b>Possible</b></td></tr>";

        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible, pos_possible FROM b_entries WHERE bid = ? ORDER BY possible DESC, displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible, $b_pos);

        $prev = -1;
        $pos_dis = "";
        while ($query->fetch()) {
            if ($prev == $b_pos) { $pos_dis = ""; } else { $pos_dis = $b_pos; $prev = $b_pos; }
            $msg4 .= "<tr><td align=\"center\">" . $pos_dis . "</td><td>" . $b_name . "</td><td align=\"center\">" . $b_points . "</td><td align=\"center\">" . $b_remaining . "</td><td align=\"center\">" . $b_possible . "</td></tr>";
            $cur++;
        }
        $query->close();

        $msg4 .= "</table></td></tr></table>";

    }

    // time to send emails!

    require '/etc/PHPMail/PHPMailer-master/PHPMailerAutoload.php';

    $currentemail = '';

    $query = $mysqli->prepare("SELECT firstname, lastname, email FROM b_entries WHERE bid = ? ORDER BY email ASC");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($e_fname, $e_lname, $e_email);

    while ($query->fetch()) {

        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'receive.roanoke.edu';  // Specify main and backup server
        $mail->SMTPAuth = false;                               // Enable SMTP authentication

        $mail->From = 'taylor@roanoke.edu';
        $mail->FromName = 'RC NCAA Bowl System';

        $mail->addAddress($e_email, $e_fname . " " . $e_lname);  // Add a recipient

        $mail->addReplyTo('taylor@roanoke.edu', 'RC NCAA Bowl System');

        $mail->WordWrap = 80;                                 // Set word wrap to 50 characters
        $mail->isHTML(true);                                  // Set email format to HTML

        if ($greeting == 1) { $msg2 = "Dear " . $e_fname . ":<br><br>"; }
        $message = $msg1 . $msg2 . $msg3 . $msg35 . $msg4 . $msg5;

        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $message;

        if ($currentemail != $e_email) {
            $mail->send();
            $currentemail = $e_email;
        }

    }

    header("Location: index.php?action=admin");
    die();

?> 