<?

        $bid = $_REQUEST['bid'];
?>
        <table width="80%" align="center" border="1">
            <tr>
                <td><b>Game</b></td>
                <td><b>Date/Time</b></td>
                <td><b>Set/Update Results</b></td>
            </tr>
<?
        $query = $mysqli->prepare("SELECT gid, name, gametime, team1, team2, score1, score2, winner FROM b_games WHERE bid = ? ORDER BY gid ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($gid, $g_name, $g_gametime, $g_team1, $g_team2, $g_score1, $g_score2, $g_winner);

        while ($query->fetch()) {

            if ($g_winner == 1) { $g_team1 = "<b><u>" . $g_team1 . "</u></b>"; }
            if ($g_winner == 2) { $g_team2 = "<b><u>" . $g_team2 . "</u></b>"; }
?>
            <tr>
                <td><? echo $g_name; ?></td>
                <td><? echo date('l, F j, Y @ g:i A', strtotime($g_gametime)); ?></td>
                <td>
                    <form action="./index.php?action=enterresult2" method="post">
                        <input type="hidden" name="bid" value="<? echo $bid; ?>">
                        <input type="hidden" name="gid" value="<? echo $gid; ?>">
                        <table width="100%">
                            <tr>
                                <td><? echo $g_team1; ?></td><td><input type="text" name="score1" value="<? echo $g_score1; ?>" width="5"></td>
                                <td rowspan="2"><input type="submit" value="Update"></td>
                            </tr>
                            <tr>
                                <td><? echo $g_team2; ?></td><td><input type="text" name="score2" value="<? echo $g_score2; ?>" width="5"></td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
<?
        }
        $query->close();
?>
        </table>

        <br><br>
