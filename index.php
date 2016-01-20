<?

    include "incl_sql.php";
//    include "incl_functions.php";

    global $l_username, $l_firstname, $l_lastname;

    if ((isset($_COOKIE['sid'])) && ($_COOKIE['sid'] != '')) {

        $query = $mysqli->prepare("SELECT username FROM b_users WHERE sid = ?");
        $query->bind_param('s',$_COOKIE['sid']);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {

            $query->bind_result($l_username);
            $query->fetch();
            $query->close();

        }

    }

    $page_action = $_REQUEST["action"];
    $special = 0;

    // DEAL WITH PAGES THAT HAVE A POSSIBLE HEADER OUTPUT (THEY MUST INCLUDE HEADER)

    if ($page_action == "setpassword") {

        include "page_setpassword.php";
        $special = 1;
        $smallfooter = 1;

    } else if ($page_action == "newbowl3") {

        include "page_newbowl3.php";
        $special = 1;

    } else if ($page_action == "updatestandings") {

        include "page_updatestandings.php";
        $special = 1;

    } else if ($page_action == "enterresult2") {

        include "page_enterresult2.php";
        $special = 1;

    } else if ($page_action == "postcomment") {

        include "page_postcomment.php";
        $special = 1;

    } else if ($page_action == "login") {

        include "page_login.php";
        $special = 1;
        $smallfooter = 1;

    } else if ($page_action == "logout") {

        include "page_logout.php";
        $special = 1;
        $smallfooter = 1;

    } else if ($page_action == "json_test") {

        include "page_jsontest.php";
        $special = 1;
        die();

    } else if ($page_action == "autoupdate") {

        include "page_autoupdate.php";
        $special = 1;
        die();

    } else if ($page_action == "enroll2") {

        include "page_enroll2.php";
        $special = 1;

    } else if ($page_action == "sendemail2") {

        include "page_sendemail2.php";
        $special = 1;

    } else if ($page_action == "toggledonation") {

        include "page_toggledonation.php";
        $special = 1;

    }

    if ($special == 0) {
        include "incl_header.php";
        include "incl_body.php";
    }

    // REGULAR PAGES

    if ($page_action == "forgotpassword") {

        include "page_forgotpassword.php";

    } else if ($page_action == "sendemail1") {

        include "page_sendemail1.php";

    } else if ($page_action == "viewpicks") {

        include "page_viewpicks.php";

    } else if ($page_action == "viewstandings") {

        include "page_viewstandings.php";

    } else if ($page_action == "enterresult1") {

        include "page_enterresult1.php";

    } else if ($page_action == "donations") {

        include "page_donations.php";

    } else if ($page_action == "enroll1") {

        include "page_enroll1.php";

    } else if ($page_action == "enroll3") {

        include "page_enroll3.php";

    } else if ($page_action == "comments") {
    
        include "page_comments.php";

    } else if ($page_action == "admin") {

        include "page_admin.php";

    } else if ($page_action == "newbowl1") {

        include "page_newbowl1.php";

    } else if ($page_action == "newbowl2") {

        include "page_newbowl2.php";

    } else {

        if ($special == 0) { include "page_main.php"; }

    }

    include "incl_footer.php";

?>
