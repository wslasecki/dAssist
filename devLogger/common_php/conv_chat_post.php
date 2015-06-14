<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include('_db.php');

if( isset($_REQUEST['worker']) && isset($_REQUEST['session']) && isset($_REQUEST['chat']) && isset($_REQUEST['round']) ) {

  $worker = $_REQUEST['worker'];
  $session = $_REQUEST['session'];
  $chat = $_REQUEST['chat'];
  $type = $_REQUEST['type'];
  $round = intval($_REQUEST['round']);
  #print $worker;
  #print $session;

  $role = "crowd";
  if( isset($_REQUEST['role']) ) {
    $role = $_REQUEST['role'];
  }
  #print $role;


  $id = session_id();

  $dbh = null;
  try {
    $dbh = getDatabaseHandle();
  } catch( PDOException $e ) {
    echo $e->getMessage();
  }

  if( $dbh ) {
    // check worker, insert if necessary
    // For SqlServer version:
    //$sth = $dbh->prepare("SELECT id FROM users WHERE user_name = :worker");
    // For MySql version:
    $sth = $dbh->prepare("SELECT id FROM users WHERE user = :worker");
    $sth->execute(array(':worker'=>$worker));
    $row = $sth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);

    if( $row ) {
      $worker_id = $row["id"];
    } else {
      // For SqlServer version:
      //$sth = $dbh->prepare("INSERT INTO users (user_name) VALUES(:worker)");
      // For MySql version:
      $sth = $dbh->prepare("INSERT INTO users (user) VALUES(:worker)");
      $sth->execute(array(':worker'=>$worker));
      $worker_id = $dbh->lastInsertId();
    }

    // insert session if necessary

    $sth = $dbh->prepare("SELECT id FROM sessions WHERE session = :session");
    $sth->execute(array(':session'=>$session));
    $row = $sth->fetch();

    if( $row ) {
      $session_id = $row[0];
    } else {
      $sth = $dbh->query("INSERT INTO sessions (session, done) VALUES('$session', 0)");
      $session_id = $dbh->lastInsertId();
    }

    $sth = $dbh->prepare("INSERT INTO msgs (user_id, session_id, round, chat, role) VALUES(:worker_id, :session_id, :round, :chat, :role)");
    $succVal = $sth->execute(array(':worker_id'=>$worker_id, ':session_id'=>$session_id, ':round'=>$round, ':chat'=>$chat, ':role'=>$role));

    $chat_id = $dbh->lastInsertId();

    $logh = $dbh->prepare("INSERT INTO mistLog (message, user_id, session_id, type, round) VALUES(:chat, :worker_id, :session_id, :type, :round)");
    $logh->execute(array(':chat'=>$chat, ':worker_id'=>$worker, ':session_id'=>$session, ':type'=>$type, ':round'=>$round));


    //echo "{" . json_encode($chat_id) . "}";
    

    $cnth = $dbh->prepare("SELECT COUNT(*) AS numQuestions FROM msgs WHERE user_id=:worker_id AND session_id=:session_id AND role=:role");
    $cnth->execute(array(':worker_id'=>$worker_id, ':session_id'=>$session_id, ':role'=>$role));
    $countRow = $cnth->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
    echo($countRow["numQuestions"]);

    //
  }
}
?>
