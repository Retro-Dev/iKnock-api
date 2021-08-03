<?php
$username = "root";
$password = "";
$hostname = "localhost"; 

$con=mysqli_connect($hostname,$username,$password);



mysqli_select_db($con,'cubes_iknock'); 
//To select the database

session_start(); //To start the session

$query=mysqli_query($con,'SELECT * FROM LEAD'); 
print_r($query);
//made query after establishing connection with database.
?>