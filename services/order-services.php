<?php

function getAmouts($index, $orderCountForThisMonth, $amountResult, $orderType, $isVeg)
{
  $genLimit = 50;
  $i = 0;
  echo 'order type: '.$orderType.', ';
  while ($amountRow = mysqli_fetch_assoc($amountResult)) {
    echo 'TimeOptionId: '.$amountRow["TimeOptionId"].', ';
    // echo ($orderType == $amountRow["TimeOptionId"]);
    if ($orderType == $amountRow["TimeOptionId"] && $isVeg == $amountRow["VegOptionId"]) {
      // echo $orderCountForThisMonth.', ';
      if ($genLimit < $orderCountForThisMonth && $genLimit < $index) {
        return $amountRow["AfterLimitAmount"];
      } else {
        return $amountRow["EmpAmount"];
      }
    } else {
      continue;
    }
  }
}


function calculateAmounts()
{

}










?>