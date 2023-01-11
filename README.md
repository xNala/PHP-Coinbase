
# PHP Coinbase Commerce Payment Library




A simple and small library for integrating Coinbase payments in to any system.  
No database connection is made, for easy adaptability in to any system, Postgres, MySQL, SQLite, or any other database.

## Authors

- [xNala](https://github.com/xNala/)


## Features

- Webhook Notifications for all CHARGE events on Coinbase.
- No built in database connection
## Deployment

Generate an API Key from   
```Settings -> Security -> API Keys```  
Generate a Webhook Secret from  
```Settings -> Notifications -> Show Shared Secret```  

Set both variables in  
 ```cfg/includes.php```

Add a Webhook Endpoint pointing to the ```webhook.php``` file on your domain in  
```Settings -> Notifications -> Add Endpoint```

Enable required events. Enabling all "CHARGE" events is suggested.  


Modify ```webhook.php``` to edit what is done when a payment update is sent from Coinbase


Demonstration of functionality.  

```php
  <?php
    //Include our coinbase functionality. Feel free to rename this file.
    require_once('cfg/includes.php');

    /**
     *
     * makes an order, returns the Coinbase OrderID
     *
     * @param productName - Name of The Product
     * @param productDescription - Description of The Product
     * @param productPrice - Price of The Individual Item
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
```

