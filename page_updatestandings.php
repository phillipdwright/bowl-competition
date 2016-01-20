<?

    $bid = $_REQUEST['bid'];

    // get bowl data/information

    $query = $mysqli->prepare("SELECT id, name, games, maxpoints FROM b_bowls WHERE id = ?");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_games, $b_maxpoints);
    $query->fetch();
    $query->close();

    // zero out the current player scores and such

    $query = $mysqli->prepare("UPDATE b_entries SET points = 0, remaining = ?, possible = ? WHERE bid = ?");
    $query->bind_param('sss',$b_maxpoints,$b_maxpoints,$bid);
    $query->execute();
    $query->close();

    $query = $mysqli->prepare("UPDATE b_entries_data SET correct = 0 WHERE bid = ?");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->close();

    // set correct variable for entries_data

    $query = $mysqli->prepare("UPDATE b_entries_data e JOIN b_games g ON e.gid = g.gid SET e.correct = 1 WHERE e.bid = ? AND e.team = g.winner");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->close();

    // now iterate through each entry and process the results

    $query = $mysqli->prepare("SELECT id FROM b_entries WHERE bid = ?");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($e_id);

    while ($query->fetch()) {

        $query2 = $mysqli->prepare("SELECT g.winner, e.team, e.points FROM b_entries_data e, b_games g WHERE e.gid = g.gid AND e.bid = ? AND e.eid = ?");
        $query2->bind_param('ss',$bid,$e_id);
        $query2->execute();
        $query2->store_result();
        $query2->bind_result($g_winner, $e_team, $e_points);

        $remaining = $b_maxpoints;
        $currenttotal = 0;

        while ($query2->fetch()) {

                if ($e_team == $g_winner) { $currenttotal += $e_points; }
                if ($g_winner > 0) { $remaining -= $e_points; }
        }

        $query2->close();

        $query2 = $mysqli->prepare("UPDATE b_entries SET points = ?, remaining = ? WHERE bid = ? AND id = ?");
        $query2->bind_param('iiss',$currenttotal,$remaining,$bid,$e_id);
        $query2->execute();
        $query2->close();

        $query2 = $mysqli->prepare("UPDATE b_entries SET possible = points + remaining WHERE bid = ? AND id = ?");
        $query2->bind_param('ss',$bid,$e_id);
        $query2->execute();
        $query2->close();
    }

    $query->close();

    // now update position data for POINTS first
    
    $query = $mysqli->prepare("SELECT id, points FROM b_entries WHERE bid = ? ORDER BY points DESC");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($e_id, $e_points);

    $cur = 1; $prev = -1;
    while ($query->fetch()) {

        if ($e_points != $prev) { 
        
            $prev = $e_points;
            $query2 = $mysqli->prepare("UPDATE b_entries SET pos_points = ? WHERE bid = ? AND points = ?");
            $query2->bind_param('iis',$cur,$bid,$e_points);
            $query2->execute();
            $query2->close();
            
        }

        $cur++;
        
    }            
            
    // now update position data for POSSIBLE
    
    // now update position data for POINTS first

    $query = $mysqli->prepare("SELECT id, possible FROM b_entries WHERE bid = ? ORDER BY possible DESC");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($e_id, $e_points);

    $cur = 1; $prev = -1;
    while ($query->fetch()) {

        if ($e_points != $prev) {

            $prev = $e_points;
            $query2 = $mysqli->prepare("UPDATE b_entries SET pos_possible = ? WHERE bid = ? AND possible = ?");
            $query2->bind_param('iis',$cur,$bid,$e_points);
            $query2->execute();
            $query2->close();

        }

        $cur++;

    }
                
    $query = $mysqli->prepare("UPDATE b_bowls SET lastupdate = NOW() WHERE id = ?");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->close();

    header("Location: index.php?action=admin");
    die();

?>
