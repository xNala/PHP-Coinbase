<?php
    require_once('coinbase.view.conf.php');
    require_once('coinbase.control.conf.php');

    $apiKey = '';        //Settings -> Security      -> API Keys
    $webhookSecret = ''; //Settings -> Notifications -> Show Shared Secret
    //Ensure you create a webhook in there, and point it to https://yourdomain.tld/path/to/webhook.php
    //Enable any CHARGE event you see fit. confirmed and failed should both be used at the very minimum.

    //Initialize Control
    $coinbaseHandler_control = new coinbase_control($apiKey);

    //Initialize View
    $coinbaseHandler_view = new coinbase_view();



?>