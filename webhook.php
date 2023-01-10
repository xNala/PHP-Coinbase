<?php 
    //Include our coinbase functionality. Feel free to rename this file.
    require_once('cfg/includes.php');


    $headerSave = "";
    foreach (getallheaders() as $name => $value) {
        if(strtolower($name) == "x-cc-webhook-signature"){
            $headerSet = true;
            $sentHMAC = $value;
            $headerSave = $name.' | '.$value;
            break;
        }
    }

    if(!$headerSet){
        http_response_code(401);
        die('401 Unauthorized. | No HMAC Provided.');
    }else{
        $input = file_get_contents('php://input');
        $blob = json_decode($input,true);
        $hmacVerify = hash_hmac('sha256', $input, $webhookSecret);

        if($hmacVerify === $sentHMAC){
            //valid secret
            if($blob["event"]["type"] == "charge:created"){
                //Order Created
                $filePath = sys_get_temp_dir().'/'.$blob["event"]["data"]["code"].'.txt';
                $orderInfo = $coinbaseHandler_control->getOrderInfo($filePath);
                if($orderInfo === false){
                    http_response_code(404);
                    die('404 Not Found. | Order Not Found!.');
                }else{
                    echo 'Order Created.';
                    //Order has been created, log in to database if you want




                }
            }elseif($blob["event"]["type"] == "charge:failed"){
                //Order Expired
                $filePath = sys_get_temp_dir().'/'.$blob["event"]["data"]["code"].'.txt';
                $orderInfo = $coinbaseHandler_control->getOrderInfo($filePath);
                if($orderInfo === false){
                    http_response_code(404);
                    die('404 Not Found. | Order Not Found!.');
                }else{
                    echo 'Time Expired.';
                    //Order payment period EXPIRED.
                    //Do not send product.




                }
            }elseif($blob["event"]["type"] == "charge:pending"){
                //Payment Pending
                $filePath = sys_get_temp_dir().'/'.$blob["event"]["data"]["code"].'.txt';
                $orderInfo = $coinbaseHandler_control->getOrderInfo($filePath);
                if($orderInfo === false){
                    http_response_code(404);
                    die('404 Not Found. | Order Not Found!.');
                }else{
                    echo 'Pending Payment.';
                    //Payment sent, waiting on confirmation.




                }
            }elseif($blob["event"]["type"] == "charge:delayed"){
                //paid after expiration.
                $filePath = sys_get_temp_dir().'/'.$blob["event"]["data"]["code"].'.txt';
                $orderInfo = $coinbaseHandler_control->getOrderInfo($filePath);
                if($orderInfo === false){
                    http_response_code(404);
                    die('404 Not Found. | Order Not Found!.');
                }else{
                    echo 'Delayed Payment.';
                    //Payment has been sent after the 1 hour payment window.



                    
                }

            }elseif($blob["event"]["type"] == "charge:confirmed"){
                //completed!
                $filePath = sys_get_temp_dir().'/'.$blob["event"]["data"]["code"].'.txt';
                $orderInfo = $coinbaseHandler_control->getOrderInfo($filePath);
                if($orderInfo === false){
                    http_response_code(404);
                    die('404 Not Found. | Order Not Found!.');
                }else{
                    echo 'Order Completed.';
                    //Send Product, mark sale as complete



                    
                }
            }
        }else{
            http_response_code(401);
            die('401 Unauthorized. | Incorrect HMAC Provided.'.$hmacVerify);
        }
    }


?>