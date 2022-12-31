<?php
include_once 'cors.php';
include_once 'utils.php';

// obtenermos la url y la divinimos segun el /
$routesArray = explode("/", $_SERVER['REQUEST_URI']);

// limpiamos el array de indices vacios
$routesArray = array_filter($routesArray);

// en este caso le aplico un slice xq necesito cortar el primer indice del array ya que estoy trabajando en localhost y me envia el nombre de la carpeta y en este caso no lo necesito
$routesArray = array_slice($routesArray, 1);

// es importante manejar si el array es vacio con count() o con empty() ej,

// if (count($routesArray) == 0) {}
// if (empty($routesArray)) {}

function methodDefault($table)
{
  $table = htmlspecialchars($table);
  $json = [
    'status' => 404,
    'result' => "The table $table not exist"
  ];
  echo json_encode($json, http_response_code($json["status"]));
}

if (empty($routesArray)) {
  $json = [
    'status' => 404,
    'body' => [],
    'statusText' => 'Error al formular la solicitud,'
  ];
  // pasamos el status aqui mismo como 2do parametro
  echo json_encode($json, http_response_code($json["status"]));
  die();
}

if (!empty($routesArray) && isset($_SERVER['REQUEST_METHOD'])) {
  if ($routesArray[0] != 'login' && $routesArray[0] != 'register') {
    Utils::JwtValidate();
  };
  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      include_once 'services/get.php';
      if ($routesArray[0] == 'categories') {
        getCategories();
      } else if (str_contains($routesArray[0], 'user_products')) {
        getProductsByUserAndCategory();
      } else if (str_contains($routesArray[0], 'all_products')) {
        getAllproductsbyCategory();
      } else if (str_contains($routesArray[0], 'detail_product')) {
        getProductById();
      } else {
        methodDefault($routesArray[0]);
      }
      break;
    case 'POST':
      include_once 'services/post.php';
      if ($routesArray[0] == 'login') {
        formLogin($routesArray[0]);
      } else if ($routesArray[0] == 'register') {
        formRegister($routesArray[0]);
      } else if ($routesArray[0] == 'prueba-post') {
        pruebaPost($routesArray[0]);
      } else {
        methodDefault($routesArray[0]);
      }
      break;
    case 'PUT':
      include_once 'services/put.php';
      putData($routesArray[0]);
      break;
    case 'DELETE':
      include_once 'services/delete.php';
      deleteData($routesArray[0]);
      break;
  }
}
