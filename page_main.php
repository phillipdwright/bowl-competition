        <table width="60%" align="center" border="1">
            <tr>
                <td>
                    Welcome to the Roanoke College NCAA Bowl Pool System!  On this page, you can submit your entry for the current year's pool up until the start of the first bowl game of this year (see below for open pools), view picks and standings for this year's pool (after the first bowl game has started), and also view picks and standings for previous pools (fall 2015 and beyond).
                </td>
            </tr>
        </table>

        <br><br>

        <table width="60%" border="1" align="center">
                <tr>
                    <td colspan="3" align="left"><b>open pools</b></td>
                </tr>
<?
        $query = $mysqli->prepare("SELECT id, name, starttime FROM b_bowls WHERE starttime >= NOW() ORDER BY starttime DESC");
        $query->execute();
        $query->store_result();
        $query->bind_result($b_id, $b_name, $b_startdate);

        if ($query->num_rows == 0) {
            ?>
                <tr>
                    <td colspan="3" align="center">no open pools</td>
                </tr>
            <?
        } else {

            while($query->fetch()) {
            ?>
                <tr>
                    <td align="left"><? echo $b_name; ?></td>
                    <td align="center">Enrollment Ends <? echo date('l, F j, Y @ g:i A', strtotime($b_startdate)); ?></td>
                    <td align="center"><a href="./index.php?action=enroll1&bid=<? echo $b_id; ?>">enroll now</a></td>
                </tr>
            <?
            }
        }

        $query->close();
?>
        </table>

        <br><br>

        <table width="60%" border="1" align="center">
            <tr>
                <td colspan="2" align="left"><b>active pools</b></td>
            </tr>
            <?
            $query = $mysqli->prepare("SELECT id, name, starttime FROM b_bowls WHERE (starttime <= NOW()) AND (NOW() <= endtime) ORDER BY starttime DESC");
            $query->execute();
            $query->store_result();
            $query->bind_result($b_id, $b_name, $b_startdate);

            if ($query->num_rows == 0) {
                ?>
                <tr>
                    <td colspan="2" align="center">no active pools</td>
                </tr>
                <?
            } else {

                while($query->fetch()) {
                    ?>
                    <tr>
                        <td align="left"><? echo $b_name; ?></td>
                        <td align="center"><a href="./index.php?action=viewpicks&bid=<? echo $b_id; ?>">view picks</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="./index.php?action=viewstandings&bid=<? echo $b_id; ?>">view standings</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="./index.php?action=comments&bid=<? echo $b_id; ?>">discussion board</a></td>
                    </tr>
                    <?
                }
            }

            $query->close();
            ?>
        </table>

        <br><br>

        <table width="60%" border="1" align="center">
            <tr>
                <td colspan="2" align="left"><b>past pools</b></td>
            </tr>
            <?
            $query = $mysqli->prepare("SELECT id, name, starttime FROM b_bowls WHERE NOW() >= endtime ORDER BY endtime DESC");
            $query->execute();
            $query->store_result();
            $query->bind_result($b_id, $b_name, $b_startdate);

            if ($query->num_rows == 0) {
                ?>
                <tr>
                    <td colspan="2" align="center">no older pools</td>
                </tr>
                <?
            } else {

                while($query->fetch()) {
                    ?>
                    <tr>
                        <td align="left"><? echo $b_name; ?></td>
                        <td align="center"><a href="./index.php?action=viewpicks&bid=<? echo $b_id; ?>">view picks</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="./index.php?action=viewstandings&bid=<? echo $b_id; ?>">view standings</a></td>
                    </tr>
                    <?
                }
            }

            $query->close();
            ?>
        </table>

        <br><br>
