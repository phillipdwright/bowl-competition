<?

        $b_name = $_REQUEST['b_name'];
        $b_starttime = $_REQUEST['b_starttime'];
        $b_endtime = $_REQUEST['b_endtime'];
        $b_games = $_REQUEST['b_games'];
        $b_max = $_REQUEST['b_max'];

        $query = $mysqli->prepare("INSERT INTO b_bowls (name, games, maxpoints, starttime, endtime) VALUES (?, ?, ?, ?, ?)");
        $query->bind_param('sssss',$b_name,$b_games,$b_max,$b_starttime,$b_endtime);
        $query->execute();
        $query->close();

        $query = $mysqli->prepare("SELECT id FROM b_bowls WHERE starttime = ? AND endtime = ?");
        $query->bind_param('ss',$b_starttime,$b_endtime);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_id);
        $query->fetch();
        $query->close();



        for ($i = 1; $i <= $b_games; $i++) {

            $req_string = 'b_name_' . $i;
            $b_game_name = $_REQUEST[$req_string];
            $req_string = 'b_datetime_' . $i;
            $b_game_datetime = $_REQUEST[$req_string];
            $req_string = 'b_team1_' . $i;
            $b_game_team1 = $_REQUEST[$req_string];
            $req_string = 'b_team2_' . $i;
            $b_game_team2 = $_REQUEST[$req_string];

            $query = $mysqli->prepare("INSERT INTO b_games (bid, gid, name, gametime, team1, team2) VALUES (?, ?, ?, ?, ?, ?)");
            $query->bind_param('ssssss', $b_id, $i, $b_game_name, $b_game_datetime, $b_game_team1, $b_game_team2);
            $query->execute();
            $query->close();

        }

        header("Location: index.php?action=admin");
?>