<?

        $b_name = $_REQUEST['b_name'];
        $b_starttime = $_REQUEST['b_starttime'];
        $b_endtime = $_REQUEST['b_endtime'];
        $b_games = $_REQUEST['b_games'];
        $b_max = $_REQUEST['b_max'];

//        $query = $mysqli->prepare("INSERT INTO b_bowls (name, games, starttime, endtime) VALUES (?, ?, ?, ?)");
//        $query->bind_param('ssss',$b_name,$b_games,$b_starttime,$b_endtime);
//        $query->execute();
//        $query->close();

?>
        <form action="./index.php?action=newbowl3" method="post">
            <input type="hidden" name="b_name" value="<? echo $b_name; ?>">
            <input type="hidden" name="b_starttime" value="<? echo $b_starttime; ?>">
            <input type="hidden" name="b_endtime" value="<? echo $b_endtime; ?>">
            <input type="hidden" name="b_games" value="<? echo $b_games; ?>">
            <input type="hidden" name="b_max" value="<? echo $b_max; ?>">

        <table width="90%" align="center" border="1">
            <tr>
                    <td>Num</td>
                    <td>Bowl Name</td>
                    <td>Date/Time<br>YYYY-MM-DD<br>HH:MM:SS</td>
                    <td>Team 1</td>
                    <td>Team 2</td>
            </tr>
            <?
                for ($i = 1; $i <= $b_games; $i++) {
            ?>
            <tr>
                <td align="center"><? echo $i; ?></td>
                <td align="center"><input type="text" name="b_name_<? echo $i; ?>" length="25"></td>
                <td align="center"><input type="text" name="b_datetime_<? echo $i; ?>" length="20"></td>
                <td align="center"><input type="text" name="b_team1_<? echo $i; ?>" length="25"></td>
                <td align="center"><input type="text" name="b_team2_<? echo $i; ?>" length="25"></td>
            </tr>
            <?
                }
            ?>
            <tr>
                <td colspan="5" align="center"><input type="submit" value="Create New Bowl"></td>
            </tr>



        </table>

        </form>

        <br><br>
