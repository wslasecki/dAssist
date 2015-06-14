<?php

  function getDatabaseHandle() {
    //echo("Connecting to DB...");
    $dbh = new PDO("sqlite:../db/devLog.db");
    //echo("Got DB handle.");
    return $dbh;
  }


?>
