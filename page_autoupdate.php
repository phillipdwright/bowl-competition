<?

    ob_start();

    // get bowl data and see what happens

    $query = $mysqli->prepare("SELECT id, games, maxpoints FROM b_bowls WHERE starttime <= NOW() AND NOW() <= endtime");
    $query->execute();
    $query->store_result();
    $query->bind_result($bid, $b_games, $b_maxpoints);

    if ($query->num_rows == 0) {
        // no bowl found
        echo "No bowl found.";
        die();
    }

    $query->fetch();
    $query->close();

    // pull and decode data

    $web_string = file_get_contents('http://espn.go.com/college-football/scoreboard');

    $start_pos = strpos($web_string,'window.espn.scoreboardData');
    $start_pos += 30;

    $length = strpos($web_string,';window.espn.scoreboardSettings') - $start_pos;
    $data = substr($web_string,$start_pos,$length);

    $parsed_data = json_decode($data, TRUE);

    $sendemail = 0;
    $doupdate = 0;

    for ($i = 1; $i <= $b_games; $i++) {

        $query = $mysqli->prepare("SELECT name, team1, team2, score1, score2, winner, espn FROM b_games WHERE bid = ? AND gid = ?");
        $query->bind_param('ss',$bid, $i);
        $query->execute();
        $query->store_result();
        $query->bind_result($g_name, $g_team1, $g_team2, $g_score1, $g_score2, $g_winner, $g_espn);
        $query->fetch();
        $query->close();

        $events = $parsed_data['events'];

        $j_found = 0;

        foreach ($events as $event) {

            if ($event['id'] == $g_espn) {

                // found game, let's check what's up!
                $j_found = 1;

                foreach ($event['competitions'] as $competition) {

                    $j = 0;
                    $j_score = array("0","0");
                    $j_teams = array("","");
                    $j_winner = array("0","0");

                    foreach ($competition['competitors'] as $team) {

                        $j_score[$j] = $team['score'];
                        $j_winner[$j] = $team['winner'];
                        $j_teams[$j] = strtoupper($team['team']['displayName']);
                        $j++;

                    }

                    // data recorded for this game, let's see what's up!

                    $firstfirst = 0;
                    $firstsecond = 0;

                    if ((strncmp(strtoupper($g_team1),$j_teams[0],3) == 0) & (strncmp(strtoupper($g_team2),$j_teams[1],3) == 0)) {

                        // ordering is correct and found (team 1 in database is listed first in JSON)
                        $firstfirst = 1;

                    }

                    if ((strncmp(strtoupper($g_team1),$j_teams[1],3) == 0) & (strncmp(strtoupper($g_team2),$j_teams[0],3) == 0)) {

                        // ordering is opposite but found (team 1 in database is listed second in JSON)
                        $firstsecond = 1;

                    }

                    if ((($firstfirst == 1) && ($firstsecond == 1)) || (($firstfirst == 0) && ($firstsecond == 0))) {

                        // unable to accurately discern teams based on name
                        echo "Game found -- $g_name -- but unable to determine team ordering because both teams start with the same three characters or can't find both -- $g_team1 $g_team2 in database, " . $j_teams[0] . " " . $j_teams[1] . " in JSON.<br>\n";

                    } else {

                        if ($firstsecond == 1) {

                            // if needed, switch to get first first to make the following easier

                            $tjs = $j_score[1];
                            $j_score[1] = $j_score[0];
                            $j_score[0] = $tjs;
                            $tjw = $j_winner[1];
                            $j_winner[1] = $j_winner[0];
                            $j_winner[0] = $tjw;
                            $tjn = $j_teams[1];
                            $j_teams[1] = $j_teams[0];
                            $j_teams[0] = $tjn;

                        }

                        if (($j_score[0] > 0) || ($j_score[1] > 0)) {

                            // game started or over

                            if (($j_winner[0] == 1) || ($j_winner[1] == 1)) {

                                // game is over, let's see how we match

                                if ($g_winner > 0) {

                                    echo "Game found, completed, and already in database -- $g_name -- $g_team1 $g_score1 and $g_team2 $g_score2 versus " . $j_teams[0] . " " . $j_score[0] . " and " . $j_teams[1] . " " . $j_score[1] . "<br>\n";

                                } else {

                                    // need to update

                                    $sendemail = 1;
                                    $doupdate = 1;

                                    echo "Game found, completed, and <b>updating</b> -- $g_name -- $g_team1 $g_score1 and $g_team2 $g_score2 versus " . $j_teams[0] . " " . $j_score[0] . " and " . $j_teams[1] . " " . $j_score[1] . "<br>\n";

                                    $winner = 1;
                                    if ($j_score[1] > $j_score[0]) { $winner = 2; }

                                    $query = $mysqli->prepare("UPDATE b_games SET score1 = ?, score2 = ?, winner = ? WHERE bid = ? AND gid = ?");
                                    $query->bind_param('iiiss',$j_score[0],$j_score[1],$winner,$bid,$i);
                                    $query->execute();
                                    $query->close();

                                }

                            } else {

                                    echo "Game found, in progress -- $g_name -- $g_team1 $g_score1 and $g_team2 $g_score2 versus " . $j_teams[0] . " " . $j_score[0] . " and " . $j_teams[1] . " " . $j_score[1] . "<br>\n";

                            }

                        } else {

                            echo "Game found, not in progress -- $g_name -- $g_team1 $g_score1 and $g_team2 $g_score2 versus " . $j_teams[0] . " " . $j_score[0] . " and " . $j_teams[1] . " " . $j_score[1] . "<br>\n";

                        }

                    }

                }

            }

        }

        if ($j_found == 0) {

            echo "Game not found -- $g_name -- manually update please!<br>\n";

        }

    }

    $content = ob_get_clean();

    echo $content;

    if ($doupdate == 1) {

        $url = 'http://math.roanoke.edu/bowl/index.php?action=updatestandings&bid=' . $bid;
        $web_string2 = file_get_contents($url);
        $content = $content . "<br><br>" . $web_string2;

    }

    if ($sendemail == 1) {

        require '/etc/PHPMail/PHPMailer-master/PHPMailerAutoload.php';

        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'receive.roanoke.edu';  // Specify main and backup server
        $mail->SMTPAuth = false;                               // Enable SMTP authentication

        $mail->From = 'taylor@roanoke.edu';
        $mail->FromName = 'RC NCAA Bowl System';

        $mail->addAddress('taylor@roanoke.edu', 'David Taylor');  // Add a recipient

        $mail->addReplyTo('taylor@roanoke.edu', 'RC NCAA Bowl System');

        $mail->WordWrap = 80;                                 // Set word wrap to 50 characters
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'RC NCAA Bowl System - Auto Update Message';
        $mail->Body    = $content;
        $mail->AltBody = $content;

        $mail->send();


    }

?>
