<?php

    //Include our coinbase functionality. Feel free to rename this file.
    require_once('cfg/includes.php');

    /**
     *
     * makes an order, returns the Coinbase OrderID
     *
     * @param productName - Name of The Product
     * @param productDescription - Description of The Product
     * @param productPrice - Price of The Individual Item (Integer with up to 2 decimals, max: 99999.99)
     * @param quantity - Order Quantity
     * 
     * @return bool
     *
     */
    $result = $coinbaseHandler_control->makeOrder('Product Name', 'Product Description', 5, 5);
    if($result !== false){
        //Parse File
        $fileData = $coinbaseHandler_control->getOrderInfo($result);

        $fileDecoded = json_decode($fileData, true);
        /*
        *
        * The following values can be stored in your database if you'd like

        $paymentURL           = $fileDecoded['paymentURL']
        $coinbaseID           = $fileDecoded['coinbaseID']
        $orderID              = $fileDecoded['ID']
        $totalPrice           = $fileDecoded['totalPrice']
        $itemPrice            = $fileDecoded['itemPrice']
        $itemQuantity         = $fileDecoded['itemQuantity']
        $productName          = $fileDecoded['productName']
        $productDescription   = $fileDecoded['productName']
        $dateCreatedString    = $fileDecoded['date']['createdString']
        $dateExpireString     = $fileDecoded['date']['expireString']
        $dateCreatedTimestamp = $fileDecoded['date']['createdUnix']
        $dateExpireTimestamp  = $fileDecoded['date']['expireUnix']
        
        */

        $coinbaseHandler_view->redirectToPayment($fileDecoded['paymentURL']);
        die('Redirecting');
    }else{
        echo 'failure';
    }

?>