<?php

require_once "db.php";
require_once "utils.php";

function getLocationByUser($userData)
{
  $userId = $userData['id_user'];
  $db = DataBase::getConnection();
  $sql = "CALL get_location_by_user(?)";
  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $userId);
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
        'statusText' => 'User only has one location'
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
        'statusText' => 'There are no categories'
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

function getProductsByUserAndCategory($userData)
{
  $userId = $userData['id_user'];
  $categoryId = $_GET['category'];
  $locationId = $_GET['location'];
  $db = DataBase::getConnection();

  $sql = "CALL get_products_by_user_and_category(?, ?, ?)";

  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("iii", $userId, $categoryId, $locationId);
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
        'total' => 0,
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

function getAllproductsbyCategory()
{
  $categoryId = $_GET['category'];
  $db = DataBase::getConnection();

  $sql = "CALL get_products_by_category(?)";

  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $categoryId);
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
        'total' => 0,
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

function getProductById()
{
  $productId = $_GET['id'];
  $db = DataBase::getConnection();

  $sql = "SELECT * FROM products p WHERE p.id_product = ? LIMIT 1";

  try {
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $productId);
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
        'total' => 0,
        'body' => [],
        'statusText' => 'Product inexistent'
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
