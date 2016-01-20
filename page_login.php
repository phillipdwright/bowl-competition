<?

        $l_name = $_REQUEST["login_name"];
        $l_pass = $_REQUEST["login_password"];
        
        $query = $mysqli->prepare("SELECT username, password FROM b_users WHERE username = ?");
        $query->bind_param('s',$l_name);
        $query->execute();
        $query->store_result();
        
        if ($query->num_rows > 0) {
        
                $query->bind_result($username, $r_pass);
                $query->fetch();
                $query->close();
                
                if (hash('md5',$l_pass) == $r_pass) {
                
                        $good = 0;
                        $sid = 0;
                        
                        while ($good == 0) { 

                                $sid = rand(10000000,99999999);
                                
                                $query = $mysqli->prepare("SELECT username FROM b_users WHERE sid = ?");
                                $query->bind_param('s',$sid);
                                $query->execute();
                                $query->store_result();

                                if ($query->num_rows == 0) { $good = 1; }
                                
                                $query->close();
                                
                        }
        
                        $query2 = $mysqli->prepare("UPDATE b_users SET sid = ? WHERE username = ?");
                        $query2->bind_param('ss',$q1,$q2);
                        $q1 = $sid;
                        $q2 = $username;
                        $query2->execute();
                        $query2->close();
        
                        // logged in and SID set, set cookie
                        
                        setcookie("sid",$sid,time()+31536000);
                        
                        header("Location: index.php?action=admin");
                        die();
                        
                }
                
        }

        // REDIRECT DONE, HEADER SAFE
        
        include "incl_header.php";

?>
        <table width="90%" align="center" border="1">
            <tr>
                <td>
                        <font color="red">The username and password combination you entered did not match our records; please return to the previous page and try logging in again, or use the Forgotten Password link if you forgot your password.</font>
                </td>
            </tr>
        </table>

