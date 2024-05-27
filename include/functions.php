<?php
session_start();
include('dbConnect.php');


function generateId($prefix,$table,$id,$con){
    $query = "select * FROM  $table  order by $id desc limit 1";
    $result = mysqli_query($con,$query) or die("can't select");

    $last_row = mysqli_fetch_assoc($result);
    $last_id = $last_row[$id];

        if($last_id==" "){
            $new_id = 1;
        }else{
           
            $new_id = intval($last_id);
        
        }
    return $new_id;
}


function message($url, $message)
{
    header("Location: $url?msg=" . $message);
}

function error($url, $err)
{
    header("Location: $url?error=" . $err);
}


?>