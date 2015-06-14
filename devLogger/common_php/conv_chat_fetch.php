<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');

$time_window = 10000000*60;

if( isset($_REQUEST['session']) ) {

  $session = $_REQUEST['session'];

  $lastid = -1;
  if( isset($_REQUEST['lastid']) ) {
    $lastid = $_REQUEST['lastid'];
  }

  $id = session_id();

  try {
    $dbh = getDatabaseHandle();
  } catch(PDOException $e) {
    echo $e->getMessage();
  }

  if($dbh) {
    // use this for multiple-chats-per-round mode
    //$round += 1;

    // For MySql version:
    $sth = $dbh->prepare ("SELECT done, role, user, users.id AS user_id, chat, time, c.id AS chat_id FROM msgs c, sessions, users WHERE users.id = c.user_id AND sessions.session = :session AND sessions.id = c.session_id AND c.id > :lastid ORDER BY time");
    $sth->execute(array(':session'=>$session, ':lastid'=>$lastid));
    $first_time=true;


    while( $row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT) ) {
      if($first_time) { 
	print '{"done": ' . $row['done'] . ', "chats":[';
      }	else {
	print ",";
      }
      $first_time = false;

      $role = "unset";
      if( $row["role"] == "requester" ) {
          $role = "you";
      }
      else {
          $role = $row["role"];
      }

      print '{"id":' . $row["chat_id"] . ',"chat":' . json_encode($row["chat"]) . ',"time":"' . $row["time"] . '", "user": ' . json_encode($row["user"]) . ', "role":"' . $role . '"}';
    }

    if( $first_time ) {
      print '{"done": 0, "chats":[';
    }

    print '], "valid_ids": [';

    // For MySql version:
    $sth = $dbh->prepare ("SELECT done, role, user, users.id AS user_id, chat, time, c.id AS chat_id FROM msgs c, sessions, users WHERE users.id = c.user_id AND sessions.session = :session AND sessions.id = c.session_id AND role != 'requester' AND julianday('now') - time < :time_window ORDER BY time");
    $sth->execute(array(':session'=>$session, ':time_window'=>$time_window));

    $first_time=true;
    while( $row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT) ) {
      if(!$first_time) {
        print ",";
      }
      $first_time = false;

      $votes = 10;
      print '{"chat_id":' . $row["chat_id"] . ',"votes":' . $votes . '}';
    }
    print "]";

    
    //

    $cnth = $dbh->prepare("SELECT COUNT(*) AS numQuestions FROM msgs c, sessions s, users u WHERE role=:role AND s.id = c.session_id AND s.session = :session AND u.id = c.user_id");
    $cnth->execute(array(':role'=>"user", ':session'=>$session));
    $countRow = $cnth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
    print(', "qCount": ' . $countRow["numQuestions"]);

    print "}";
  }

}
else{
  echo 'Failing on ARGS';
}
?>
