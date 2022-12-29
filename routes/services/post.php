<?php

require_once "db.php";
require_once "get.php";
require_once "put.php";
require_once "utils.php";

function formRegister()
{
  // IMPORTANTEEE: la siguiente linea es de vital importancia para poder leer los datos que vienen en JSON.stringify desde el front
  $post = json_decode(file_get_contents("php://input"), true);

  // filter var: limpia cualquier cosa para que no inyecten codigo
  $email = filter_var(strtolower($post['email']), FILTER_SANITIZE_EMAIL);
  $password = $post['password'];
  $firstName = $post['first_name'];
  $lastName = $post['last_name'];
  $phone = $post['phone'];

  $userExist = getUserIsExist($email);

  if ($userExist == false) {
    if (isset($email) && isset($password) && isset($firstName) && isset($lastName)) {
      // encriptar el password

      // agregarle un suffix a las password para hacerlas mas seguras

      $crypt = password_hash($password, PASSWORD_BCRYPT);
      $db = DataBase::getConnection();
      $sql = "CALL new_user(?, ?, ?, ?, ?)";

      try {
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssss", $email, $crypt, $firstName, $lastName, $phone);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          $json = [
            'status' => 201,
            'body' => [],
            'statusText' => 'The user created successfully'
          ];
          echo json_encode($json, http_response_code($json["status"]));
        } else {
          $json = [
            'status' => 500,
            'body' => [],
            'statusText' => 'an error occurred while creating the user'
          ];
          echo json_encode($json, http_response_code($json["status"]));
        }
      } catch (mysqli_sql_exception $error) {
        $json = [
          'status' => 500,
          'body' => [],
          'statusText' => $error->getMessage()
        ];
        echo json_encode($json, http_response_code($json["status"]));
      }

      $stmt->close();
      $db->close();
    } else {
      $json = [
        'status' => 400,
        'body' => [],
        'statusText' => 'Some parameter is missing',
      ];
      echo json_encode($json, http_response_code($json["status"]));
    }
  } else {
    $json = [
      'status' => 409,
      'body' => [],
      'statusText' => 'Email already exists'
    ];
    echo json_encode($json, http_response_code($json["status"]));
  }
}

function formLogin()
{

  // $headers = apache_request_headers();
  // print_r($headers);

  // print_r($_COOKIE);

  $post = json_decode(file_get_contents("php://input"), true);

  // filter var: limpia cualquier cosa para que no inyecten codigo
  $email = filter_var(strtolower($post['email']), FILTER_SANITIZE_EMAIL);
  $password = $post['password'];
  $userExist = getUserIsExist($email);

  if ($userExist != false) {
    if (isset($email) && isset($password)) {
      // encriptar el password  
      $verify = password_verify($password, $userExist['password']);

      if ($verify) {
        $token = Utils::JwtEncode([
          'id_user' => $userExist['id_user'],
          'email' => $userExist['email'],
          'first_name' => $userExist['first_name'],
          'last_name' => $userExist['last_name']
        ]);

        // actualizamos el token y la expriracion del token del usuario

        updateTokenUser($userExist, $token);

        // header(setcookie("TestCookie", $token['jwt']));

        $json = [
          'status' => 200,
          'body' => [
            'first_name' => $userExist['first_name'],
            'last_name' => $userExist['last_name'],
            'token' => $token['jwt'],
            'exp_token' => $token['exp']
          ],
          'statusText' => 'Welcome'
        ];
        echo json_encode($json, http_response_code($json["status"]));
      } else {
        $json = [
          'status' => 401,
          'body' => [],
          'statusText' => 'The email or password is incorrect'
        ];
        echo json_encode($json, http_response_code($json["status"]));
      }
    } else {
      $json = [
        'status' => 404,
        'body' => [],
        'statusText' => 'Some parameter is missing'
      ];
      echo json_encode($json, http_response_code($json["status"]));
    }
  } else {
    $json = [
      'status' => 404,
      'body' => [],
      'statusText' => 'Email dont exists'
    ];
    echo json_encode($json, http_response_code($json["status"]));
  }
}


function postData($table)
{

  $table = htmlspecialchars($table);

  $columns = "";
  $values = [];
  $placeholder = "";
  $typevalue = "";

  foreach ($_POST as $key => $value) {
    $columns .= $key . ", ";
    array_push($values, $value);
    $placeholder .= "?, ";
    $typevalue .= "s";
  }

  $columns = substr($columns, 0, -2);
  $placeholder = substr($placeholder, 0, -2);

  $sql = "INSERT INTO $table ($columns) VALUES ($placeholder)";

  $validations = Utils::ValidateTableName($table);

  if ($validations == 0) {
    $json = [
      'status' => 404,
      'result' => 'tabla no encontrada'
    ];
    echo json_encode($json, http_response_code($json["status"]));
    die();
  }

  try {
    $db = DataBase::getConnection();
    $stmt = $db->prepare($sql);
    $stmt->bind_param($typevalue, ...$values);
    $stmt->execute();
    $json = [
      'status' => 200,
      'result' => 'The process was success'
    ];
    echo json_encode($json, http_response_code($json["status"]));
  } catch (mysqli_sql_exception $error) {
    $json = [
      'status' => 500,
      'result' => $error->getMessage()
    ];
    echo json_encode($json, http_response_code($json["status"]));
    die();
  }


  $stmt->close();
  $db->close();
}
