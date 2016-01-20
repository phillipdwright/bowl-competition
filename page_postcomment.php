<?

    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, name, games, maxpoints FROM b_bowls WHERE id = ? AND starttime <= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_games, $b_maxpoints);

    if ($query->num_rows > 0) {

        $b_name = $_REQUEST['b_name'];
        $b_comment = $_REQUEST['b_comment'];
        $b_check = $_REQUEST['b_check'];
        $b_sum = $_REQUEST['b_sum'];
        
        if (($b_check == $b_sum) && ($b_name != "") && ($b_comment != "")) {
        
            $query = $mysqli->prepare("INSERT INTO b_comments (bid, name, comment, posttime) VALUES (?, ?, ?, NOW())");
            $query->bind_param('sss',$bid,$b_name,$b_comment);
            $query->execute();
            $query->close();
            
        }
        
    }
    
    header("Location: index.php?action=comments&bid=" . $bid);
    die();

?>