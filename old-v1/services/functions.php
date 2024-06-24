<?php
function mealLimit($empNumber,$con){
    $isOverride = 0;
    $mealLimit = 0;
    $userGroupQuery = "SELECT U.Fname,U.Lname,UG.* FROM Users U LEFT JOIN UserGroups UG ON UG.UgId = U.UserGroup WHERE U.EmpNumber = '$empNumber'";
    if ($result = mysqli_query($con, $userGroupQuery)) {
        while ($rows = mysqli_fetch_assoc($result)) {
            $isOverride = $rows['IsOverride'];
            $mealLimit = $rows['MealLimit'];
        }
    }
    if($isOverride == 0){
        $generalMealLimitQuery = "SELECT * FROM SiteData WHERE SettingType = 'GEN_MEAL_LIMIT'";
        if ($result = mysqli_query($con, $generalMealLimitQuery)) {
            while ($rows = mysqli_fetch_assoc($result)) {
                $mealLimit = $rows['SettingValue'];
            }
        }
    }
    return $mealLimit;
}

function myAllMealOrders($empNumber,$con){
    $totalOrders = 0;
    $userGroupQuery = "SELECT COUNT(O.OrderId) AS TotalOrders FROM Orders O LEFT JOIN Users U ON U.EmpNumber = O.EmployeeId WHERE O.EmployeeId = '$empNumber'";
    if ($result = mysqli_query($con, $userGroupQuery)) {
        while ($rows = mysqli_fetch_assoc($result)) {
            $totalOrders = $rows['TotalOrders'];
        }
    }
    return $totalOrders;
}

function isOrderExists($employeeId, $date, $timeOption, $con){
    $query = "SELECT * FROM Orders WHERE OrderDate = '$date' AND EmployeeId = '$employeeId' AND OrderType = '$timeOption'";
    if ($result = mysqli_query($con, $query)) {
       if(mysqli_num_rows($result) != 0){
        return true;
       }else{
        return false;
       }
    }
}

?>