<?php

require_once "db.php";
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
  $phone = $post['phone'] ?? null;

  $userExist = Utils::IsUserExist($email);

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
  $post = json_decode(file_get_contents("php://input"), true);

  // filter var: limpia cualquier cosa para que no inyecten codigo
  $email = filter_var(strtolower($post['email']), FILTER_SANITIZE_EMAIL);
  $password = $post['password'];
  $userExist = Utils::IsUserExist($email);

  if ($userExist != false) {
    if (isset($email) && isset($password)) {
      // desencriptar el password  
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

        // nota el antepenultimo valor quere decir si es https seguro, cuando este en produccion se pasa a true

        $tokenOptions = [
          'expires' => time() + 60 * 60 * 24 * 60,
          'path' => '/',
          'domain' => 'localhost', // leading dot for compatibility or use subdomain
          'secure' => false,     // or false
          'httponly' => true,    // or false
          'samesite' => 'None' // None || Lax  || Strict
        ];

        header(setcookie("MyMarketTok3nHttp0lnt", $token['jwt'], $tokenOptions));
        // token expira a los 2 meses
        // header(setcookie("MyMarketTok3nHttp0lnt", $token['jwt'], time() + 60 * 60 * 24 * 60, '/', null, false, true));

        // para eliminar cookkies solo setea la misma cokkie con un tiempo en pasado
        // header(setcookie("MyMarketTok3nHttp0lnt", '', time() - 3600, '/', null, null, true));

        $json = [
          'status' => 200,
          'body' => [
            'user_id' => $userExist['id_user'],
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

function createProduct($userData)
{

  $post = json_decode(file_get_contents("php://input"), true, 512, JSON_BIGINT_AS_STRING);
  // $user_id = $userData['id_user'];

  $user_id = 1;
  $product_name = $post['product_name'];
  $product_barcode = explode(" ", $post['product_barcode'])[1];
  $category = $post['id_category'];
  $brand = $post['id_brand'];
  $unit = $post['id_unit'];
  $quantity = $post['quantity'];
  $location = $post['location'];

  var_dump($product_barcode);

  if (isset($product_name) && isset($product_barcode) && isset($category) && isset($brand) && isset($unit) && isset($location)) {
    $db = DataBase::getConnection();
    $sql = "CALL new_product(?, ?, ?, ?, ?, ?, ?, ?)";

    try {
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ssiiiiii", $product_name, $product_barcode, $category, $brand, $unit, $quantity, $user_id, $location);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
        $json = [
          'status' => 201,
          'body' => [],
          'statusText' => 'Product created successfully'
        ];
        echo json_encode($json, http_response_code($json["status"]));
      } else {
        $json = [
          'status' => 500,
          'body' => [],
          'statusText' => $stmt->error
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
}

function pruebaPost()
{
  // para eliminar cookkies solo setea la misma cokkie con un tiempo en pasado
  //   header(setcookie("MyMarketTok3nHttp0lnt", '', time() - 3600, '/', null, null, true));

  //   $tokenOptions = [
  //           'expires' => time() - 3600,
  //           'path' => '/',
  //           'domain' => 'd4webs.com', // leading dot for compatibility or use subdomain
  //           'secure' => false,     // or false
  //           'httponly' => true,    // or false
  //           'samesite' => 'None' // None || Lax  || Strict
  //         ];

  //         header(setcookie("MyMarketTok3nHttp0lnt", $token['jwt'], $tokenOptions));

  $post = json_decode(file_get_contents("php://input"), true);


  $email = filter_var(strtolower($post['email']), FILTER_SANITIZE_EMAIL);
  $password = $post['password'];

  $json = [
    'status' => 200,
    'body' => $email,
    'statusText' => 'errroooorrrrrr'
  ];
  echo json_encode($json, http_response_code($json["status"]));

  // $ver = Utils::JwtValidate();
  // print_r($ver);


  // // $headers = apache_request_headers();
  // // print_r($headers);

  // if ($userExist != false) {
  //   $mycookie = $_COOKIE["MyMarketTok3nHttp0lnt"] ?? 'vacio';
  //   // print_r($mycookie);
  //   $mycookieDecode = $mycookie != 'vacio' ? Utils::JwtDecode($mycookie) : 'vacio';
  //   // print_r($mycookieDecode);
  //   // echo '<br \>';
  //   // echo '<br \>';
  //   // echo '<br \>';
  //   // echo '<br \>';
  //   // print_r($email);
  //   // print_r($password);

  //   $variable = $mycookieDecode != 'vacio' ? $mycookieDecode["exp"] : 0;
  //   if ($variable > 0) {
  //     $json = [
  //       'status' => 200,
  //       'body' => $mycookie,
  //       'statusText' => 'yeah'
  //     ];
  //     echo json_encode($json, http_response_code($json["status"]));
  //   } else {
  //     $json = [
  //       'status' => 200,
  //       'body' => $mycookie,
  //       'statusText' => 'errroooorrrrrr'
  //     ];
  //     echo json_encode($json, http_response_code($json["status"]));
  //   }
  // }
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
