<?

    // pull data

    $web_string = file_get_contents('http://espn.go.com/college-football/scoreboard');

    $start_pos = strpos($web_string,'window.espn.scoreboardData');
    $start_pos += 30;

    $length = strpos($web_string,';window.espn.scoreboardSettings') - $start_pos;

    $data = substr($web_string,$start_pos,$length);

//    echo $data;

    $parsed_data = json_decode($data, TRUE);

    $events = $parsed_data['events'];

    foreach ($events as $event) {

        echo $event['id'] . "<br>\n";

        foreach ($event['competitions'] as $competition) {

            // echo " " . $competition['uid'] . "<br>\n";

            $bowlname = "";
            
            foreach ($competition['notes'] as $note) {
            
                $bowlname = $note['headline'];
                echo "&nbsp;Bowl: $bowlname<br>\n";
                
            }

            foreach ($competition['competitors'] as $team) {

                $score1 = $team['score'];
                $winner1 = $team['winner'];
                $name1 = $team['team']['displayName'];

                echo "&nbsp;&nbsp;Details: $name1, score $score1, winner $winner1<br>\n";

            }

        }

    }

    $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($data, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

    foreach ($jsonIterator as $key => $val) {
        if(is_array($val)) {
            echo "$key:<br>\n";
        } else {
            echo "$key => $val<br>\n";
        }
    }


?>
