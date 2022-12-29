<?php

// todas las refecrencias de mysqli en https://www.w3schools.com/php/php_ref_mysqli.asp

class DataBase
{
  static public function getConnection()
  {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $namedb = 'market_test';

    $db = new mysqli($host, $user, $password, $namedb);

    if ($db->connect_error) {
      die("Error failed to connect to MySQL: " . $db->connect_error);
    } else {
      return $db;
    }
  }
}
