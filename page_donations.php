<?
    $bid = $_REQUEST['bid'];
?>
        <br><br>

        <table width="60%" align="center" border="1">
                <tr>
                        <td align="left" colspan="4"><b>Enrollments</b></td>
                </tr>
                <tr>
                        <td align="center">Name</td>
                        <td align="center">Display Name</td>
                        <td align="center">Donated?</td>
                        <td align="center">Toggle Donation</td>
                </tr>
<?

        $query = $mysqli->prepare("SELECT id, firstname, lastname, displayname, paid FROM b_entries WHERE bid = ? ORDER BY paid ASC, lastname ASC, firstname ASC, displayname ASC");
        $query->bind_param('s',$bid);
        $query->execute();
        $query->store_result();
        $query->bind_result($user_id,$firstname,$lastname,$displayname,$paid);

        while($query->fetch()) {
?>
                <tr>
                        <td align="left"><? echo $lastname . ", " . $firstname; ?></td>
                        <td align="center"><? echo $displayname; ?></td>
                        <td align="center"><? if ($paid == 1) { echo "Y"; } ?></td>
                        <td align="center"><a href="./index.php?action=toggledonation&eid=<? echo $user_id; ?>">toggle donation</a></td>
                </tr>
<?
        }

        $query->close();
?>
        </table>

        <br><br>
