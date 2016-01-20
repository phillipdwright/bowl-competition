<?

    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, name FROM b_bowls WHERE id = ? AND starttime >= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name);

    if ($query->num_rows == 0) {
?>
<br><br>

    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">The requested bowl does not exist or is not eligible for enrollment because the bowl season has already begun.  If you feel that this is an error, please contact the Bowl Contest administrator.</td>
        </tr>
    </table>
<?
        $query->close();
    } else {
        $query->fetch();
        $query->close();
?>
<br><br>

    <table width="50%" align="center" border="1">
        <tr>
            <td align="left">Welcome to the enrollment system for the <? echo $b_name; ?>.  The form below asks you for general information; your real first and last name is requested so that the Bowl Contest administrator knows who you are.  This name will not be displayed anywhere, and instead, you may choose a "Display Name" that will show up externally to other players.  Your email address is required so that you will receive updates from the system.  On the next page, you will make your selections.  If at any point you are having difficulty, please contact the Bowl Contest administrator and they will be happy to help!</td>
        </tr>
    </table>

    <br><br>

        <table width="50%" align="center" border="1">
            <form action="./index.php?action=enroll2" method="post">
                <input type="hidden" name="bid" value="<? echo $b_id; ?>">
                <tr>
                    <td align="right">
                        First Name:
                    </td>
                    <td align="center">
                        <input type="text" name="b_fname" length="50">
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Last Name:
                    </td>
                    <td align="center">
                        <input type="text" name="b_lname" length="50">
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Display Name (Optional):
                    </td>
                    <td align="center">
                        <input type="text" name="b_dname" length="50">
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Email Address:
                    </td>
                    <td align="center">
                        <input type="text" name="b_email" length="50">
                    </td>
                </tr>
              <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="Continue Enrollment">
                    </td>
                </tr>
            </form>
        </table>

        <br><br>
<?
    }
?>