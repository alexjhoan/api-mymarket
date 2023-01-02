<?php

$dominioPermitido = "http://localhost:3000";

// print_r($_SERVER);

// if ($_SERVER['SERVER_NAME'] == "d4webs.com") {
//   $dominioPermitido = "https://d4webs.com";
// }

header("Access-Control-Allow-Origin: $dominioPermitido");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,PUT,POST,DELETE");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, credentials, X-Requested-With");
