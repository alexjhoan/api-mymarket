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

  static function IsUserExist($email, $user_id = null)
  {
    $db = DataBase::getConnection();
    $sql = $user_id ? "select * from users u where u.id_user = ? limit 1" : "select * from users u where u.email = ? limit 1";
    try {
      $stmt = $db->prepare($sql);
      if ($user_id) {
        $stmt->bind_param("i", $user_id);
      } else {
        $stmt->bind_param("s", $email);
      }
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        return $result->fetch_assoc();
      } else {
        return false;
      }
    } catch (mysqli_sql_exception $error) {
      $json = [
        'status' => 500,
        'body' => [],
        'statusText' => $error->getMessage()
      ];
      echo json_encode($json, http_response_code($json["status"]));
      die();
    }
    $stmt->close();
    $db->close();
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
    // 1. recibimos el token desde las cookies y si existe la decodificamos

    $mycookie = $_COOKIE["MyMarketTok3nHttp0lnt"] ?? 'empty';
    $mycookieDecode = $mycookie != 'empty' ? Utils::JwtDecode($mycookie) : 'empty';


    if ($mycookieDecode != 'empty') {
      // se usa el (array) para convertir un objeto a un array entendible
      $decodedTokenData = (array) $mycookieDecode["data"];

      // 2. extraemos de la decodificacion el id_user para buscarlo en la DB y si existe que nos devuelva toda la info del usuario

      $userData = Utils::IsUserExist(null, $decodedTokenData['id_user']);

      // 3. validamos que la cookie recibida es la misma que esta en la DB y verificamos que no alla expirado

      if ($userData["user_token"] == $mycookie && $userData['exp_token'] > Time()) {
        return true;
      } else {
        $json = [
          'status' => 401,
          'body' => [],
          'statusText' => 'The token is invalid or expired, please log in again'
        ];
        echo json_encode($json, http_response_code($json["status"]));
        die();
      }
    } else {
      $json = [
        'status' => 401,
        'body' => [],
        'statusText' => 'Token is invalid please log in again'
      ];
      echo json_encode($json, http_response_code($json["status"]));
      die();
    }
  }
}
