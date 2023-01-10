<?php
    require_once('includes.php');

    class coinbase_control{
        protected $apiKey;

        function __construct($apiKey) {
            $this->apiKey = $apiKey;
        }


        function getOrderInfo($filePath){
            if(!file_exists($filePath))
                return false;

            $fileHandle = fopen($filePath, "r") or die("Unable to open payment file!");
            $fileData = fread($fileHandle, filesize($filePath));
            fclose($fileHandle);


            return $fileData;
        }


        function saveFile($filePath, $createdDate, $expireDate, $coinbaseID, $orderID, $itemPrice, $quantity, $productName, $productDescription, $paymentURL){
            $totalPrice = ($itemPrice * $quantity);


            $unixTimeCreated = strtotime($createdDate);
            $unixTimeExpires = strtotime($expireDate);


            $filePath = sys_get_temp_dir().'/'.$coinbaseID.'.txt';
            $fileHandle = fopen($filePath, 'w') or die("Can't create temporary payment file.");

            $fileData = [];
            $fileData['ID']                    = $orderID;
            $fileData['coinbaseID']            = $coinbaseID;
            $fileData['totalPrice']            = $totalPrice;
            $fileData['itemPrice']             = $itemPrice;
            $fileData['itemQuantity']          = $quantity;
            $fileData['productName']           = $productName;
            $fileData['productDescription']    = $productDescription;
            $fileData['paymentURL']            = $paymentURL;

            $fileData['date']['createdString'] = $createdDate;
            $fileData['date']['expireString']  = $expireDate;
            $fileData['date']['createdUnix']   = $unixTimeCreated;
            $fileData['date']['expireUnix']    = $unixTimeExpires;

            fwrite($fileHandle, json_encode($fileData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            fclose($fileHandle);

        }


        function makeOrder($productName, $productDescription, $productPrice, $quantity){

            $totalPrice = ($productPrice * $quantity);

            $jsonArray = array(
                'name' => $productName,
                'description' => $productDescription,
                'pricing_type' => 'fixed_price',
                'brand_color' => '#fff',
                'brand_logo_url' => '',
                'logo_url' => '',
                'local_price' => array(
                    "amount" => $totalPrice,
                    'currency' => 'USD'
                )
            );


            $cURL = curl_init();
            curl_setopt($cURL, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/");
            curl_setopt($cURL, CURLOPT_POSTFIELDS, json_encode($jsonArray, true));
            curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'x-cc-api-key:'.$this->apiKey, 'x-cc-version:2018-03-22'));
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

            //the following should be set to TRUE on production builds
            curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);


            $output = curl_exec($cURL);
            curl_close($cURL); 

            if($output === false)
                return false;

            $blob = json_decode($output, true);

            $expirationDate = $blob['data']['expires_at'];
            $createdDate = $blob['data']['created_at'];
            $cb_code = $blob['data']['code'];
            $paymentURL = $blob['data']['hosted_url'];
            $orderID = $blob['data']['id'];

            $filePath = sys_get_temp_dir().'/'.$cb_code.'.txt';

            $this->saveFile($filePath, $createdDate, $expirationDate, $cb_code, $orderID, $productPrice, $quantity, $productName, $productDescription, $paymentURL);

            if(is_file($filePath))
                return $filePath;
            else
                return false;
        }

    }
?>