<?
    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, name, lastupdate FROM b_bowls WHERE id = ? AND starttime <= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_updated);

    if ($query->num_rows == 0) {
    ?>
    <br><br>

    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">The requested bowl does not exist or is not eligible for standings because the bowl season has not yet begun.  If you feel that this is an error, please contact the Bowl Contest administrator.</td>
        </tr>
    </table>
    <?
        $query->close();
    } else {
        $query->fetch();
        $query->close();
?>
        <br><br>

        <table width="90%" align="center" border="2">
                <tr>
                        <td align="left" colspan="3"><b>Current Standings for <? echo $b_name; ?>, last updated <? echo date('l, F j, Y @ g:i A', strtotime($b_updated)); ?></b></td>
                </tr>
                <tr>
                    <td align="left" colspan="3">This page has three big columns, standings as of the last updated date and time shown above; the first contains the current standings sorted by display name.  The second is sorted by current point totals, and the last is sorted by points possible.</td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" align="center" border="1">
                            <tr><td colspan="4"><b>Standings by Username</b></td></tr>
                            <tr>
                                <td><b>Name</b></td>
                                <td><b>Points</b></td>
                                <td><b>Remaining</b></td>
                                <td><b>Possible</b></td>
                            </tr>
<?
        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible FROM b_entries WHERE bid = ? ORDER BY displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible);

        while ($query->fetch()) {
?>
                            <tr>
                                <td><? echo $b_name; ?></td>
                                <td align="center"><? echo $b_points; ?></td>
                                <td align="center"><? echo $b_remaining; ?></td>
                                <td align="center"><? echo $b_possible; ?></td>
                            </tr>
<?
        }
        $query->close();
?>
                        </table>
                    </td>
                    <td>
                        <table width="100%" align="center" border="1">
                            <tr><td colspan="5"><b>Standings by Current Points</b></td></tr>
                            <tr>
                                <td></td>
                                <td><b>Name</b></td>
                                <td><b>Points</b></td>
                                <td><b>Remaining</b></td>
                                <td><b>Possible</b></td>
                            </tr>
<?
        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible, pos_points FROM b_entries WHERE bid = ? ORDER BY points DESC, displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible, $b_pos);

        $prev = -1;
        $pos_dis = "";
        while ($query->fetch()) {

            if ($prev == $b_pos) { $pos_dis = ""; } else { $pos_dis = $b_pos; $prev = $b_pos; }
?>
                            <tr>
                                <td align="center"><? echo $pos_dis; ?></td>
                                <td><? echo $b_name; ?></td>
                                <td align="center"><? echo $b_points; ?></td>
                                <td align="center"><? echo $b_remaining; ?></td>
                                <td align="center"><? echo $b_possible; ?></td>
                            </tr>
<?
            $cur++;
        }
        $query->close();
?>
                        </table>
                    </td>
                    <td>
                        <table width="100%" align="center" border="1">
                            <tr><td colspan="5"><b>Standings by Points Possible</b></td></tr>
                            <tr>
                                <td></td>
                                <td><b>Name</b></td>
                                <td><b>Points</b></td>
                                <td><b>Remaining</b></td>
                                <td><b>Possible</b></td>
                            </tr>
<?
        $query = $mysqli->prepare("SELECT displayname, points, remaining, possible, pos_possible FROM b_entries WHERE bid = ? ORDER BY possible DESC, displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($b_name, $b_points, $b_remaining, $b_possible, $b_pos);

        $prev = -1;
        $pos_dis = "";
        while ($query->fetch()) {
            if ($prev == $b_pos) { $pos_dis = ""; } else { $pos_dis = $b_pos; $prev = $b_pos; }
?>
                            <tr>
                                <td align="center"><? echo $pos_dis; ?></td>
                                <td><? echo $b_name; ?></td>
                                <td align="center"><? echo $b_points; ?></td>
                                <td align="center"><? echo $b_remaining; ?></td>
                                <td align="center"><? echo $b_possible; ?></td>
                            </tr>
<?
            $cur++;
        }
        $query->close();
?>
                        </table>
                    </td>
                </tr>
            </table>

            <br><br>
<?
    } 