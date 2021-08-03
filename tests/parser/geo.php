<?php

$username = "cubes_iknock";
$password = "iknock_password";
$hostname = "127.0.0.1"; 

$conn=mysqli_connect($hostname,$username,$password);


mysqli_select_db($conn,'cubes_iknock'); 

$query = "SELECT * FROM lead  WHERE id>112";
$data=mysqli_query($conn,$query);   

while($row=mysqli_fetch_array($data)){


$address =$row['address'];
$id =$row['id'];
//$url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($address);
$url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&key=AIzaSyB6D_44n_ZL_llSGghUIRDgWOx1ucPATtc";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    
$responseJson = curl_exec($ch);
curl_close($ch);

$response = json_decode($responseJson);

if ($response->status == 'OK') {
    $latitude = $response->results[0]->geometry->location->lat;
    $longitude = $response->results[0]->geometry->location->lng;

    echo 'Latitude: ' . $latitude;
    echo '<br />';
    echo 'Longitude: ' . $longitude;

$sql = "UPDATE lead SET latitude='". $latitude."' ,longitude='". $longitude."'  WHERE id=".$id;

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}


} else {
    echo $response->status;
    var_dump($response);
}   

 }
?>