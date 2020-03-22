<?php

error_reporting(65535);
error_reporting(0);
ini_set("error_reporting",1);


#why 8840 see https://github.com/pro-blockchain-com/minter-node-nginx-upstream-checker
$nodeUrl = 'http://127.0.0.1:8840';
# OR PRO-BLOCKCHAIN.COM public node
$nodeUrl = 'https://node-main.minter.su';

$devider = 1000000000000000000;

$def_coin = "BIP";

?>