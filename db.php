<?php

// todas las refecrencias de mysqli en https://www.w3schools.com/php/php_ref_mysqli.asp

// user y pass en hosting
// dwebsco6_dwebsco6_alex
// Nv!DJp3V)yPh
// nombre de DB dwebsco6_market_test

// produccion
// class DataBase
// {
//   static public function getConnection()
//   {
//      $host = 'localhost';
// $user = 'dwebsco6_wp267';
// $password = 'k43R-4@2[)bSep.7';
// $namedb = 'dwebsco6_market_test';

//     $db = new mysqli($host, $user, $password, $namedb);

//     if ($db->connect_error) {
//       die("Error failed to connect to MySQL: " . $db->connect_error);
//     } else {
//       return $db;
//     }
//   }
// }

// Local
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
