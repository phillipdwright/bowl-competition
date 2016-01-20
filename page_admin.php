<?
    if (isset($l_username)) {
?>
        <table align="center" border="1">
                <tr>
                        <td>
                                You are logged in as <? echo $l_username; ?>.  You may log out by <a href="./index.php?action=logout">clicking here</a>.
                        </td>
                </tr>
        </table>

        <br><br>

        <table align="center" width="70%" border="1">
            <tr>
                <td colspan="3"><b>Administrator Actions</b></td>
            </tr>
            <tr>
                <td align="center" colspan="3">
                    <a href="./index.php?action=newbowl1">create new bowl</a>
                </td>
            </tr>

            <?

                $query = $mysqli->prepare("SELECT id, name, starttime, endtime FROM b_bowls ORDER BY endtime DESC");
                $query->execute();
                $query->store_result();
                $query->bind_result($b_id, $b_name, $b_startdate, $b_endtime);

                while($query->fetch()) {
                    ?>
                    <tr>
                        <td align="left"><? echo $b_name; ?></td>
                        <td align="center"><? echo date('l, F j, Y @ g:i A', strtotime($b_startdate)) . "<br>" . date('l, F j, Y @ g:i A', strtotime($b_endtime)); ?></td>
                        <td align="center"><a href="./index.php?action=enterresult1&bid=<? echo $b_id; ?>">enter game results</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=updatestandings&bid=<? echo $b_id; ?>">update standings</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=sendemail1&bid=<? echo $b_id; ?>">send email</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=donations&bid=<? echo $b_id; ?>">manage donations</a></td>
                    </tr>
                    <?
                }

                $query->close();

            ?>
        </table>

        <br><br>
<?
    } else {
?>

        <table width="70%" align="center" border="1">
        <form action="./index.php?action=login" method="post">
            <tr>
                <td>
                    Administrative Login
                </td>
                <td align=""center">
                    Username: <input type="text" name="login_name" length="15">
                </td>
                <td align="center">
                    Password: <input type="password" name="login_password" length="15">
                </td>
                <td align="center">
                    <input type="submit" value="Login">
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">
                        <a href="index.php?action=forgotpassword">Forgotten Password?</a>
                </td>
            </tr>
        </form>
        </table>
<?

    }
?> 