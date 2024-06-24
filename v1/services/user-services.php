<?php
function getUserName($id, $con)
{
  $selectQuery = "SELECT name FROM users WHERE id='{$id}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    $row = mysqli_fetch_assoc($searchResults);
    return $row['name'];
  }
}

function isEmailExists($email, $con, $getWy_id)
{
  $selectQuery = "SELECT email FROM user_data WHERE email='{$email}' AND wy_id='{$getWy_id}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}

function isNICExists($nic, $con)
{
  $selectQuery = "SELECT nic FROM users WHERE nic='{$nic}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}
function isMobileExists($mobile, $con)
{
  $selectQuery = "SELECT mobile FROM users WHERE mobile='{$mobile}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}
function isTelephoneExists($telephone, $con)
{
  $selectQuery = "SELECT telephone FROM users WHERE telephone='{$telephone}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}
function isUserIdExists($user_id, $con, $getWy_id)
{
  $selectQuery = "SELECT user_id FROM user_data WHERE user_id='{$user_id}' AND wy_id='{$getWy_id}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}
function checkIsAdminAccount($email, $con)
{
  $selectQuery = "SELECT * FROM user_data WHERE email='{$email}' AND wy_id = 0";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}

function checkIsAdminAccountByUserId($id, $con)
{
  $selectQuery = "SELECT * FROM user_data WHERE user_id='{$id}' AND wy_id = 0";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}


function isUserCodeExists($code, $wy_id, $con)
{
  $selectQuery = "SELECT * FROM user_data WHERE code='{$code}' AND wy_id='{$wy_id}' LIMIT 1";
  if ($searchResults = mysqli_query($con, $selectQuery)) {
    return mysqli_num_rows($searchResults);
  }
}
