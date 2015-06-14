<?php

  function getDatabaseHandle($pathStr) {
    //echo("Connecting to DB...");
    $dbh = new PDO("sqlite:".$pathStr."db/captions.db");
    //echo("Got DB handle.");
    return $dbh;
  }


?>
