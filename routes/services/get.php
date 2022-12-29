<?php

require_once "db.php";
require_once "utils.php";

function getUserIsExist($user)
{
  $db = DataBase::getConnection();
  $sql = "select * from users u where u.email = ? limit 1";
  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $user);
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
  }
  $stmt->close();
  $db->close();
}

function getCategories()
{
  $db = DataBase::getConnection();
  $sql = "CALL get_categories()";
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $json = ['status' => 200, 'body' => [], 'statusText' => 'success', 'total' => $result->num_rows];
      while ($row = $result->fetch_assoc()) {
        array_push($json['body'], $row);
      }
      echo json_encode($json, http_response_code($json["status"]));
    } else {
      $json = [
        'status' => 404,
        'body' => [],
        'statusText' => 'No existen categorias'
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
    die();
  }

  $stmt->close();
  $db->close();
}

function getProductByUserAndCategory($table)
{
  $userId = $_GET['user'];
  $categoryId = $_GET['category'];
  $db = DataBase::getConnection();

  $sql = "CALL get_product_by_user_and_category(?, ?)";

  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $userId, $categoryId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $json = ['status' => 200, 'body' => [], 'statusText' => 'success', 'total' => $result->num_rows];
      while ($row = $result->fetch_assoc()) {
        array_push($json['body'], $row);
      }
      echo json_encode($json, http_response_code($json["status"]));
    } else {
      $json = [
        'status' => 404,
        'body' => [],
        'statusText' => 'Sin productos en existencia para esta categoria'
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
}
function getProductByUserAndCategory1($table)
{

  $table = explode("?", $table)[0];
  $columns = $_GET['columns'] ?? '*';
  $db = DataBase::getConnection();
  $tableName = htmlspecialchars($table);

  $validations = Utils::ValidateTableName($table);

  if ($validations == 0) {
    $json = [
      'status' => 404,
      'result' => 'tabla no encontrada'
    ];
    echo json_encode($json, http_response_code($json["status"]));
    die();
  }

  // $sql = "SELECT $columns FROM $tableName";
  $sql = "CALL get_product_by_user_and_category(3, 1)";

  try {
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $json = ['status' => 200, 'body' => [], 'total' => $result->num_rows];
      while ($row = $result->fetch_assoc()) {
        array_push($json['body'], $row);
      }
      echo json_encode($json, http_response_code($json["status"]));
    } else {
      $json = [
        'status' => 404,
        'result' => 'la tabla se encuentra vacia'
      ];
      echo json_encode($json, http_response_code($json["status"]));
    }
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
