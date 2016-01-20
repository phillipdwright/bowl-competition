<?

        $f_name = $_REQUEST["f_name"];
        $f_code = $_REQUEST["f_code"];
        $f_pass = $_REQUEST["f_pass"];

        $query = $mysqli->prepare("SELECT username, code FROM b_users WHERE username = ?");
        $query->bind_param('s',$f_name);
        $query->execute();
        $query->store_result();
        $query->bind_result($username, $code);

        $query->fetch();
        $query->close();

        if (($code == $f_code) && ($code != '')) {

                $hashpass = hash('md5', $f_pass);

                $query2 = $mysqli->prepare("UPDATE b_users SET password = ?, code = '' WHERE username = ?");
                $query2->bind_param('ss', $hashpass, $username);
                $query2->execute();
                $query2->close();

        }

        header("Location: index.php?action=admin");
?>