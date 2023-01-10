<?php
    require_once('includes.php');

    class coinbase_view{
        function redirectToPayment($url){
            header('location: '.$url);
            die('Redirecting.');
        }



    }
?>