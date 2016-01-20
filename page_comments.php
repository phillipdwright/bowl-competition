<?

    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, name, games, maxpoints FROM b_bowls WHERE id = ? AND starttime <= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_name, $b_games, $b_maxpoints);

    if ($query->num_rows == 0) {
?>
    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">The requested bowl does not exist or is not eligible for enrollment because the bowl season has not already begun.  If you feel that this is an error, please contact the Bowl Contest administrator.</td>
        </tr>
    </table>
<?
        $query->close();
    } else {
        $query->fetch();
        $query->close();


?>
        <br><br>

        <table width="70%" align="center" border="1">
            <tr>
                <td align="left"><b> <? echo $b_name; ?> Discussion Board</b></td>
            </tr>
            <tr>
                <td align="left">This is a primitive discussion board for <? echo $b_name; ?> so that you may engage others in this bowl contest. Note that the text you enter will be displayed as you enter it, with the same formatting, line breaks, and such.  Smack talk is highly encouraged, but please refrain from personal attacks.  Comments are listed is the order that they were made, and you may enter your own comment below.</td>
            </tr>
        </table>

        <br><br>

<?

    $query = $mysqli->prepare("SELECT name, comment, posttime FROM b_comments WHERE bid = ? ORDER BY posttime ASC");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_cname, $b_comment, $b_posttime);
    while ($query->fetch()) {
?>
        <table width="70%" align="center" border="1">
            <tr>
                <td align="left">Posted by <b><? echo $b_cname; ?></b> on <? echo date('l, F j, Y @ g:i A', strtotime($b_posttime)); ?></td>
            </tr>
            <tr>
                <td align="left"><p style="white-space: pre-wrap;"><? echo $b_comment; ?></p></td>
            </tr>
        </table>
        <br>
<?
    }
    $query->close();
        $rand1 = rand(2,10);
        $rand2 = rand(3,9);
        $b_sum = $rand1 + $rand2;
?>
        <table width="70%" align="center" border="1">
            <form action="./index.php?action=postcomment" method="post">
                <input type="hidden" name="bid" value="<? echo $b_id; ?>">
                <input type="hidden" name="b_check" value="<? echo $b_sum; ?>">
                <tr>
                    <td align="left" colspan="2"><b>Post a New Comment</b></td>
                </tr>
                <tr>
                    <td align="right">
                        Display Name
                    </td>
                    <td align="center">
                        <input type="text" name="b_name" length="50">
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Comment:
                    </td>
                    <td align="center">
                        <textarea name="b_comment" cols="75" rows="8"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="right">
                        Humanity Check:
                    </td>
                    <td align="center">
                        What is <? echo $rand1; ?> added to <? echo $rand2; ?>? <input type="text" name="b_sum" length="10">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <input type="submit" value="Post Comment">
                    </td>
                </tr>
            </form>
        </table>

<?
    }
?> 