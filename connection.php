<?php
//use requred_once nalang. 
$servername = 'localhost';
$username = "root";
$password = "";
$dbase = "barangayrecord";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbase);

// Check connection  -----------------------------------------

// if ($conn->connect_error) {
//     die("Connection Unsuccessfull");  // di natuutloy process

// } else {
//     echo "Connection Successfull";
// }
