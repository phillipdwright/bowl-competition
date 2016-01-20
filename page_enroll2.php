<?

    $bid = $_REQUEST['bid'];

    $query = $mysqli->prepare("SELECT id, games, maxpoints FROM b_bowls WHERE id = ? AND starttime >= NOW()");
    $query->bind_param('s',$bid);
    $query->execute();
    $query->store_result();
    $query->bind_result($b_id, $b_games, $b_maxpoints);

    if ($query->num_rows == 0) {
        include "incl_header.php";
        include "incl_body.php";
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

        $b_fname = $_REQUEST['b_fname'];
        $b_lname = $_REQUEST['b_lname'];
        $b_dname = $_REQUEST['b_dname'];
        $b_email = $_REQUEST['b_email'];

        if ($b_dname == '') { $b_dname = $b_fname . ' ' . $b_lname; }

        $query = $mysqli->prepare("SELECT id  FROM b_entries WHERE bid = ? AND displayname = ?");
        $query->bind_param('ss',$bid, $b_dname);
        $query->execute();
        $query->store_result();
        $query->bind_result($e_id);
        $qnr = $query->num_rows;
        $query->close();

        if ($qnr > 0) {
            include "incl_header.php";
            include "incl_body.php";
            ?>
            <br><br>

            <table width="70%" align="center" border="1">
                <tr>
                    <td align="left">You may only have one entry for this bowl contest per display name; if you wish to have multiple entries, please change your display name to something else for this entry on the previous page.
                    </td>
                </tr>
            </table>
            <?

        } else if (($b_fname == '') || ($b_lname == '') || ($b_email == '')) {
            include "incl_header.php";
            include "incl_body.php";
            ?>
            <br><br>

            <table width="70%" align="center" border="1">
                <tr>
                    <td align="left">One or more required fields on the previous page were not entered; please return to
                        that page using your browser's back button and try again. Note that only the display name is
                        optional.
                    </td>
                </tr>
            </table>
            <?
        } else {
            include "incl_header.php";
?>
<script>
    function checkTeam(value, pts) {
        var name = 'game_';
        var name1 = name.concat(value,'_1r');
        var name2 = name.concat(value,'_2r');
        if (!((document.getElementById(name1).checked) || (document.getElementById(name2).checked))) {
            var nname1 = name.concat(value,'_1');
            var nname2 = name.concat(value,'_2');
            document.getElementById(nname1).style.backgroundColor='#ff6347';
            document.getElementById(nname2).style.backgroundColor='#ff6347';
        }
        var radio = name.concat(value,'_',pts.toString(),'p');
        document.getElementById(radio).checked = true;
        totalValues();
    }

    function activateTeam(value, team) {
        var name = 'game_';
        var otherteam = 2;
        if (team == 2) {
            otherteam = 1;
        }
        document.getElementById('topbar').innerHTML = 'In function.';
        var name1 = name.concat(value.toString(),'_',team.toString(),'r');
        var name2 = name.concat(value.toString(),'_',otherteam.toString(),'r');
        var radio1 = name.concat(value.toString(),'_',team.toString());
        var radio2 = name.concat(value.toString(),'_',otherteam.toString());
        document.getElementById('topbar').innerHTML = radio2;
        document.getElementById(name1).checked = true;
        document.getElementById(name2).checked = false;
        document.getElementById(radio1).style.backgroundColor = 'lightgreen';
        document.getElementById(radio2).style.backgroundColor = 'white';
        totalValues();
    }

    function checkScroll() {
        if (document.body.scrollTop >= 5) {
            document.getElementById('topbar').style.position = 'fixed';
            document.getElementById('topbar').style.top = '10px';
        } else {
            document.getElementById('topbar').style.position = 'absolute';
            document.getElementById('topbar').style.top = '10px';
        }
    }

    function totalValues() {
        var name = 'game_';
        var total = 0;
        var totalGames = 0;
        for (i = 1; i <= <? echo $b_games; ?>; i++) {
            var name1 = name.concat(i.toString(),'_1r');
            var name2 = name.concat(i.toString(),'_2r');
            if ((document.getElementById(name1).checked) || (document.getElementById(name2).checked)) {
                totalGames++;
                var pt1 = name.concat(i.toString(),'_1p');
                var pt2 = name.concat(i.toString(),'_2p');
                var pt3 = name.concat(i.toString(),'_3p');
                var pt4 = name.concat(i.toString(),'_4p');
                var pts1 = name.concat(i.toString(),'_1ps');
                var pts2 = name.concat(i.toString(),'_2ps');
                var pts3 = name.concat(i.toString(),'_3ps');
                var pts4 = name.concat(i.toString(),'_4ps');
                if (document.getElementById(pt1).checked) {
                    total += 1;
                    document.getElementById(pts1).style.backgroundColor = 'lightgreen';
                    document.getElementById(pts2).style.backgroundColor = 'white';
                    document.getElementById(pts3).style.backgroundColor = 'white';
                    document.getElementById(pts4).style.backgroundColor = 'white';
                } else if (document.getElementById(pt2).checked) {
                    total += 2;
                    document.getElementById(pts2).style.backgroundColor = 'lightgreen';
                    document.getElementById(pts1).style.backgroundColor = 'white';
                    document.getElementById(pts3).style.backgroundColor = 'white';
                    document.getElementById(pts4).style.backgroundColor = 'white';
                } else if (document.getElementById(pt3).checked) {
                    total += 3;
                    document.getElementById(pts3).style.backgroundColor = 'lightgreen';
                    document.getElementById(pts2).style.backgroundColor = 'white';
                    document.getElementById(pts1).style.backgroundColor = 'white';
                    document.getElementById(pts4).style.backgroundColor = 'white';
                } else if (document.getElementById(pt4).checked) {
                    total += 4;
                    document.getElementById(pts4).style.backgroundColor = 'lightgreen';
                    document.getElementById(pts2).style.backgroundColor = 'white';
                    document.getElementById(pts3).style.backgroundColor = 'white';
                    document.getElementById(pts1).style.backgroundColor = 'white';
                } else {
                    totalGames--;
                    document.getElementById(pts1).style.backgroundColor = '#ff6347';
                    document.getElementById(pts2).style.backgroundColor = '#ff6347';
                    document.getElementById(pts3).style.backgroundColor = '#ff6347';
                    document.getElementById(pts4).style.backgroundColor = '#ff6347';
                }
            }
        }
        var startString = 'Your current point total is ';
        var displayString = startString.concat(total.toString(),' of <? echo $b_maxpoints; ?> points total required.<br>Your current number of teams with points selected is ',totalGames.toString(),' of <? echo $b_games; ?> required selections.');
        document.getElementById('topbar').innerHTML = displayString;
        if ((total == <? echo $b_maxpoints; ?>) && (totalGames == <? echo $b_games; ?>)) {
            document.getElementById('topbar').style.backgroundColor='lightgreen';
            document.getElementById('submit').disabled = false;
        } else {
            document.getElementById('topbar').style.backgroundColor='#ff6347';
            document.getElementById('submit').disabled = true;
        }
    }

</script>

<style type="text/css">

    #topbar{
        float: right;
        position: absolute;
        border: 1px solid black;
        padding: 2px;
        background-color: #ff6347;
        width: 40%;
        left: 58%;
        top: 10px;
    }

</style>

</head>
<body onload="totalValues();" onscroll="checkScroll();">

<div id="topbar"></div>

<h1><a href="./index.php">Roanoke College NCAA Bowl Pool</a></h1>

<br><br>

    <table width="70%" align="center" border="1">
        <tr>
            <td align="left">On this page, you will make your bowl selections.  For each of the following <? echo $b_games; ?> games, please select one team to be the winner; afterwards, you must allot either 1, 2, 3, or 4 points to the game.  When your selected team wins the bowl, you will receive the number of points allotted to that game; no points are awarded for a bowl if you do not select the winner.  In total, the number of points you allot must be <? echo $b_maxpoints;?>.  In the upper-right corner of this page, a box appears that will show you the number of games for which you have picked a winner and the total number of points allotted so far.  This box will be highlighted in red until you have picked winners for all games and reached the point total needed.  As you make selections, selections will turn green, and items will be highlighted in red for each game when an item is missing (when you select a winning team first, the point options will highlight red; when you select a point total first, the teams will be highlighted red).  Once you've selected winners for all games and reached <? echo $b_maxpoints; ?> points, the submission button at the bottom will become active.  Note that your selections will be confirmed on the next page and also by an email.</td>
        </tr>
    </table>

    <br><br>

    <form action="./index.php?action=enroll3" method="post">
        <input type="hidden" name="bid" value="<? echo $b_id; ?>">
        <input type="hidden" name="b_fname" value="<? echo $b_fname; ?>">
        <input type="hidden" name="b_lname" value="<? echo $b_lname; ?>">
        <input type="hidden" name="b_dname" value="<? echo $b_dname; ?>">
        <input type="hidden" name="b_email" value="<? echo $b_email; ?>">

                <table width="90%" align="center" border="0">
<?

    for ($i = 1; $i <= $b_games; $i++) {

        $query = $mysqli->prepare("SELECT name, gametime, team1, team2 FROM b_games WHERE bid = ? AND gid = ?");
        $query->bind_param('si',$b_id, $i);
        $query->execute();
        $query->store_result();
        $query->bind_result($g_name, $g_time, $g_team1, $g_team2);
        $query->fetch();
        $query->close();

        if (($i % 3) == 1) {
?>
                        <tr>
<?
        }
?>
                                <td>
                                        <table width="90%" align="left" border="1">
                                                <tr><td align="left"><b><? echo $g_name; ?></b></td></tr>
                                                <tr><td align="left"><? echo date('l, F j, Y @ g:i A', strtotime($g_time)); ?></td></tr>
                                                <tr><td align="left">
                                                        <table width="100%" align="left" border="0">
                                                                <tr><td id="game_<? echo $i; ?>_1" style="background-color: white;" onclick="activateTeam(<? echo $i; ?>,1);">
                                                                        <input id="game_<? echo $i; ?>_1r" type="radio" name="game_<? echo $i; ?>" value="1" onClick="activateTeam(<? echo $i; ?>,1);"><? echo $g_team1; ?>
                                                                </td></tr>
                                                                <tr><td id="game_<? echo $i; ?>_2" style="background-color: white;" onclick="activateTeam(<? echo $i; ?>,2);">
                                                                        <input id="game_<? echo $i; ?>_2r" type="radio" name="game_<? echo $i; ?>" value="2" onClick="activateTeam(<? echo $i; ?>,2);"><? echo $g_team2; ?>
                                                                </td></tr>
                                                        </table>
                                                </td></tr>
                                                <tr><td align="center">
                                                        <span id="game_<? echo $i; ?>_1ps" style="background-color: white;" onclick="checkTeam('<? echo $i; ?>',1);"><input type="radio" id="game_<? echo $i; ?>_1p" name="game_<? echo $i; ?>_pts" onclick="checkTeam('<? echo $i; ?>',1);" value="1"> 1</span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="game_<? echo $i; ?>_2ps" style="background-color: white;" onclick="checkTeam('<? echo $i; ?>',2);"><input type="radio" id="game_<? echo $i; ?>_2p" name="game_<? echo $i; ?>_pts" onclick="checkTeam('<? echo $i; ?>',2);" value="2"> 2</span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="game_<? echo $i; ?>_3ps" style="background-color: white;" onclick="checkTeam('<? echo $i; ?>',3);"><input type="radio" id="game_<? echo $i; ?>_3p" name="game_<? echo $i; ?>_pts" onclick="checkTeam('<? echo $i; ?>',3);" value="3"> 3</span>&nbsp;&nbsp;&nbsp;&nbsp;<span id="game_<? echo $i; ?>_4ps" style="background-color: white;" onclick="checkTeam('<? echo $i; ?>',4);"><input type="radio" id="game_<? echo $i; ?>_4p" name="game_<? echo $i; ?>_pts" onclick="checkTeam('<? echo $i; ?>',4);" value="4"> 4</span>
                                                </td></tr>
                                        </table>
                                </td>
<?
        if (($i % 3) == 0) {
?>
                        </tr><tr><td><br></td></tr>
<?
        }
    }
?>
                </table>

<br><br>    <center><input id="submit" type="submit" value="Finish Enrollment"></center>

    </form>

        <br><br>
<?
        }
    }
?>