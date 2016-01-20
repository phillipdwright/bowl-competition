<?

        $user_id = $_REQUEST['eid'];

        $query = $mysqli->prepare("SELECT bid, paid FROM b_entries WHERE id = ?");
        $query->bind_param('s',$user_id);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {

                $query->bind_param('s',$user_id);
                $query->execute();
                $query->store_result();
                $query->bind_result($bid,$paid);
                $query->fetch();
                $query->close();
                        
                if ($paid == 0) { $paid = 1; } else { $paid = 0; }
                        
                $query2 = $mysqli->prepare("UPDATE b_entries SET paid = ? WHERE id = ?");
                $query2->bind_param('ss',$paid,$user_id);
                $query2->execute();
                $query2->close();

        }
        
        header("Location: index.php?action=donations&bid=" . $bid);
        die();

?>
