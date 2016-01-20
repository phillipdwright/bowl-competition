<?
    $bid = $_REQUEST['bid'];
    $sort = $_REQUEST['sort'];
    
    $query = $mysqli->prepare("SELECT id, name, lastupdate, games FROM b_bowls WHERE id = ? AND starttime <= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_updated, $b_games);

    if ($query->num_rows == 0) {
    ?>
    <br><br>

    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">The requested bowl does not exist or is not eligible for viewing picks because the bowl season has not yet begun.  If you feel that this is an error, please contact the Bowl Contest administrator.</td>
        </tr>
    </table>
    <?
        $query->close();
    } else {
        $query->fetch();
        $query->close();

        $cols = $b_games*2+1-5;
?>
        <br><br>

        <table width="70%" align="center" border="1">
                <tr>
                        <td align="left"><b>All Picks for <? echo $b_name; ?>, win/loss information last updated <? echo date('l, F j, Y @ g:i A', strtotime($b_updated)); ?></b></td>
                </tr>
                <tr>
                    <td align="left">This page has all picks for this bowl contest!  On this page, you can see each person's selection for each of the <? echo $b_games; ?> bowl games.  Once games are completed, correct picks are highlighted in green and incorrect picks are highlighted in red.  The points that each person allotted to each game are also listed.</td>
                </tr>
                <tr>
                    <td align="center"><a href="./index.php?action=viewpicks&bid=<? echo $bid; ?>&sort=1">sort by username</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=viewpicks&bid=<? echo $bid; ?>&sort=2">sort by current points</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=viewpicks&bid=<? echo $bid; ?>&sort=3">sort by points possible</a></td></tr>
            </table><br><br>
            <table border="1">
                <tr><td>Username</td>
                <td>Current Points</td>
                <td>Points Possible</td>
<?
        $query = $mysqli->prepare("SELECT name, gametime FROM b_games WHERE bid = ? ORDER BY gid ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($g_name, $g_gametime);
        while ($query->fetch()) {
?>
                <td><? echo $g_name; ?><br><? echo date('n/j/y @ g:i A', strtotime($g_gametime)); ?></td>
                <td></td>
<?
        }
        $query->close();
?>
                    </tr>
<?
        if ($sort == 2) {
                $query = $mysqli->prepare("SELECT displayname, id, points, possible FROM b_entries WHERE bid = ? ORDER BY points DESC, displayname ASC");
        } else if ($sort == 3) { 
                $query = $mysqli->prepare("SELECT displayname, id, points, possible FROM b_entries WHERE bid = ? ORDER BY possible DESC, displayname ASC");
        } else {
                $query = $mysqli->prepare("SELECT displayname, id, points, possible FROM b_entries WHERE bid = ? ORDER BY displayname ASC");
        }
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_dname, $e_id, $e_points, $e_possible);
        while ($query->fetch()) {
?>
            <tr><td><? echo $b_dname; ?></td>
            <td align="center"><? echo $e_points; ?></td>
            <td align="center"><? echo $e_possible; ?></td>
<?
            $query2 = $mysqli->prepare("SELECT g.team1, g.team2, e.team, e.correct, g.winner, e.points FROM b_entries_data e, b_games g WHERE e.bid = g.bid AND e.gid = g.gid AND e.eid = ? ORDER BY g.gid ASC");
            $query2->bind_param('s',$e_id);
            $query2->execute();
            $query2->store_result();
            $query2->bind_result($g_team1, $g_team2, $e_team, $e_correct, $g_winner, $e_points);

            while ($query2->fetch()) {

                $td = '';
                if ($e_correct == 1) {
                    $td = " style=\"background-color: lightgreen\"";
                } else if (($e_correct == 0) && ($g_winner > 0)) {
                    $td = " style=\"background-color: #ff6347\"";
                } else {
                    $td = '';
                }
                $teamname = '';
                if ($e_team == 1) { $teamname = $g_team1; } else { $teamname = $g_team2; }
?>
                <td<? echo $td; ?>><? echo $teamname; ?></td><td<? echo $td; ?>><? echo $e_points; ?></td>
<?

            }
            $query2->close();
?>
                </tr>
<?
        }
?>
            </table>

            <br><br>

<?
    } 