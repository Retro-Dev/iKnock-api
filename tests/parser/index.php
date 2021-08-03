<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
require_once './SimpleXLSX.php';
$username = "cubes_iknock";
$password = "iknock_password";
$hostname = "127.0.0.1"; 

$conn=mysqli_connect($hostname,$username,$password);


mysqli_select_db($conn,'cubes_iknock'); 

echo '<h1>Parse IKnock.xslx</h1><pre>';
if ( $xlsx = SimpleXLSX::parse('IKnock.xlsx') ) {
    $data = $xlsx->rows();
    if($data){

     for($a=1;$a<count($data);$a++){

        $title = ($data[$a][1]) ? $data[$a][1] : "";
        $address = ($data[$a][2]) ? $data[$a][2]: "";
        $city = ($data[$a][3]) ? $data[$a][3]: "";
        $zip_code = ($data[$a][4]) ? $data[$a][4]: "";
        $latitude = ($data[$a][17]) ? $data[$a][17] : 0;
        $longitude = ($data[$a][18]) ? $data[$a][18] : 0;
        $title = str_replace("'"," ", $title);


        $leadCustom['leadType'] = ($data[$a][0]) ? $data[$a][0] : '';
        $leadCustom['mobile'] = ($data[$a][5]) ? $data[$a][5] : '';
        $leadCustom['email'] = ($data[$a][6]) ? $data[$a][6] : '';
        $leadCustom['square_footage'] = ($data[$a][7]) ? $data[$a][7] : '';
        $leadCustom['year_built'] = ($data[$a][8]) ? $data[$a][8] : '';
        $leadCustom['bedrooms'] = ($data[$a][9]) ? $data[$a][9] : '';
        $leadCustom['bathrooms'] = ($data[$a][10]) ? $data[$a][10] : '';
        $leadCustom['auction_date'] = ($data[$a][11]) ? $data[$a][11] : '';
        $leadCustom['appraised_value'] = ($data[$a][12]) ? $data[$a][12] : 0;
        $leadCustom['original_loan_value'] = ($data[$a][13]) ? $data[$a][13] : 0;
        $leadCustom['equity'] = ($data[$a][14]) ? $data[$a][14] : 0;
        $leadCustom['loan_original_month'] = ($data[$a][15]) ? $data[$a][15] : 0;
        $leadCustom['loan_origination_year'] = ($data[$a][16]) ? $data[$a][16] : '';

         $sql = "INSERT INTO lead (`title`,`address`,`zip_code` , `city`, `latitude` ,`longitude`) 
         VALUES ('".$title."','".$address."','".$zip_code."','".$city."','".$latitude."' ,'".$longitude."')";

            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
                echo "------------success-----------<br>".$last_id;
                if($last_id){
                    foreach ($leadCustom as $key => $value){
                        $sql2 = "INSERT INTO lead_custom_field (`lead_id`,`key`,`value`) 
                        VALUES ('".$last_id."','".$key."','".$value."')";
                        if ($conn->query($sql2) === TRUE) {
                            $last_id = $conn->insert_id;
                            
                        }
                    }   
                }
                //echo "New record created successfully. Last inserted ID is: " . $last_id;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
       
     }

    


    }

} else {
	echo SimpleXLSX::parseError();
}
echo '<pre>';