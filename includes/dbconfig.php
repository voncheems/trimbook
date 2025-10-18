<?php

$dbserver = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "trimbookdb";

try{
    $conn = mysqli_connect($dbserver, 
                            $dbuser, 
                            $dbpass, 
                            $dbname);
                            
}
catch(mysqli_mysql_exception){
    
}



?>
