<?php

require_once "db.php";
require_once "vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Utils
{
  static function ValidateTableName($tableName)
  {
    $db = DataBase::getConnection();
    $sql = "SELECT COUNT(TABLE_NAME) as 'table' FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName'";

    $stmt = $db->prepare($sql);

    $stmt->execute();

    $result = $stmt->get_result();
    $stmt->close();
    $db->close();

    return $result->fetch_assoc()['table'];
  }

  static public function JwtEncode($data)
  {
    $key = "Key_de_seguridad_para_usar_en_mi_App_con_caracteresYNumeros";
    $token = [
      "iat" => time(),
      "exp" => time() + (60 * 60 * 24 * 30),
      "data" => $data
    ];

    $jwt = JWT::encode($token, $key, 'HS512');
    // foreach ($data as $clave => $valor) {
    //   $token[$clave] = $valor;
    // }
    return ["jwt" => $jwt, "iat" => $token['iat'], "exp" => $token['exp']];
  }

  static function JwtDecode($tokenEncode)
  {
    $key = "Key_de_seguridad_para_usar_en_mi_App_con_caracteresYNumeros";

    // esto nos devuelve un objeto
    $decoded = JWT::decode($tokenEncode, new Key($key, 'HS512'));

    //  con esto lo convertimos a un array entendible para php
    $decoded_array = (array) $decoded;

    return $decoded_array;
  }

  static function JwtValidate()
  {
    // el token lo vamos a recibir de vuelta a travez del header authorization

    $headers = apache_request_headers();
    // print_r($headers['authorization']);

    // vamos a hacer el ejemplo de decode

    $decodedToken = Utils::JwtDecode($headers['authorization']);
    // se usa el (array) para convertir un objeto a un array entendible
    $decodedTokenData = (array) $decodedToken['data'];
    print_r($decodedTokenData['id_user']);
    print_r($decodedTokenData);

    return $decodedTokenData;
  }
}
