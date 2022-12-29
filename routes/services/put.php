<?php

require_once "db.php";
require_once "utils.php";
require_once "get.php";


function updateTokenUser($user, $token)
{
  // $sql = "UPDATE users SET user_token='$token[jwt]', exp_token='$token[exp]' WHERE id_user = ? ";
  $sql = "CALL update_token_user(?,?,?)";

  $db = DataBase::getConnection();

  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isi", $user['id_user'], $token['jwt'], $token['exp']);
    $stmt->execute();

    return $stmt->affected_rows > 0;

    // if ($stmt->affected_rows > 0) {
    //   $json = [
    //     'status' => 201,
    //     'body' => [],
    //     'statusText' => 'The user created successfully'
    //   ];
    //   echo json_encode($json, http_response_code($json["status"]));
    // }
  } catch (mysqli_sql_exception $error) {
    $json = [
      'status' => 500,
      'body' => [],
      'statusText' => $error->getMessage()
    ];
    echo json_encode($json, http_response_code($json["status"]));
    die();
  }
}

function putData($table)
{
  $table = htmlspecialchars($table);
  $table = explode("?", $table)[0];
  $column = htmlspecialchars($_GET['column']);
  $id = htmlspecialchars($_GET["id"]);

  $validations = Utils::ValidateTableName($table);

  if ($validations == 0) {
    $json = [
      'status' => 404,
      'result' => 'tabla no encontrada'
    ];
    echo json_encode($json, http_response_code($json["status"]));
    die();
  }

  if (isset($id) && isset($column)) {
    $data = [];
    parse_str(file_get_contents('php://input'), $data);

    $set = "";

    foreach ($data as $key => $value) {
      $set .= "$key='$value', ";
    };

    $set = substr($set, 0, -2);

    $sql = "UPDATE $table SET $set WHERE $column = ?";

    try {
      $db = DataBase::getConnection();
      $stmt = $db->prepare($sql);
      $stmt->bind_param("s", $id);
      $stmt->execute();
      $result = $stmt->affected_rows;
      if ($result > 0) {
        $json = [
          'status' => 200,
          'result' => 'The process was success'
        ];
      } else {
        $json = [
          'status' => 404,
          'result' => 'ID not found'
        ];
      }
      echo json_encode($json, http_response_code($json["status"]));
    } catch (mysqli_sql_exception $error) {
      $json = [
        'status' => 500,
        'result' => $error->getMessage()
      ];
      echo json_encode($json, http_response_code($json["status"]));
    }

    $stmt->close();
    $db->close();
  } else {
    $json = [
      'status' => 500,
      'result' => 'ID o column no valido'
    ];
    echo json_encode($json, http_response_code($json["status"]));
  }
}
