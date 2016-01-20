<?

        $bid = $_REQUEST['bid'];
        $gid = $_REQUEST['gid'];

        $score1 = $_REQUEST['score1'];
        $score2 = $_REQUEST['score2'];

        $winner = 1;
        if ($score1 < $score2) { $winner = 2; }

        $query = $mysqli->prepare("UPDATE b_games SET score1 = ?, score2 = ?, winner = ? WHERE bid = ? AND gid = ?");
        $query->bind_param('iiiss',$score1,$score2,$winner,$bid,$gid);
        $query->execute();
        $query->close();

        header("Location: index.php?action=enterresult1&bid=" . $bid);
        die();

?>
