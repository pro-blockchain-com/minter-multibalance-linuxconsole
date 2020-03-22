#!/usr/bin/php
<?php

error_reporting(0);
//$debug = 1;

include "conf.php";
include "BigInteger.php";


$a = "wallets.txt";
$a = file_get_contents($a);
$a = trim($a);
$mas = explode("\n",$a);
foreach($mas as $line)
{
    $line = trim($line);
    if($line[0]=="#")continue;
    $t = explode(":",$line);
    $wal = substr($t[0],0,42);
    $wname = $t[1];
    if(!$wname)$wname = $wal;
    $wals[$wname][wal] = $wal;
}


$kuda = $nodeUrl."/status";
$a = file_get_contents($kuda);
$a = json_decode($a,1);

$time_adder = 3600*3;
$blk = $a[result][latest_block_height];
$t = $a[result][latest_block_time];
$t2 = explode(".",$t);
$t = $t2[0];
$t = str_replace("T"," ",$t);
//$t
$t = strtotime($t);
$t += $time_adder;
$delta = time()-$t;
$t = date("Y-m-d H:i:s",$t);

print "-- block $blk -- ". date("Y-m-d H:i:s")." -- delta: $delta sec ------ $blk [$t] ---\n";


foreach($wals as $wname=>$wal2)
{
//    print_r($wal2);
	$wals2[$wal2[wal]] = $wname;
	$wals3[] = $wal2[wal];  
}
    $t = json_encode($wals3);
    $url = $nodeUrl."/addresses?addresses=$t";
    if($debug)print $url."\n";
    $a = file_get_contents($url);
    $const = 0;
    $const += JSON_BIGINT_AS_STRING;
    $a = json_decode($a,1,512,$const);
	if($debug)print_r($a);
	foreach($a[result] as $v2)
	{
	    $wal = $v2[address];
	    if($debug)print_r($v2);
	    foreach($v2[balance] as $coin=>$amount)
	    {
	    unset($bb,$bb2);
	    $v = $coins[$coin];
	    if(!$v)$v = 0;
	    if($debug)print "v = $v\n";;
	    $bb = new Math_BigInteger("$v");
	    $bb2 = new Math_BigInteger("$amount");
	    $c = $bb->add($bb2);
	   //echo $c->toString(); // outputs 5
	    $v = $c->toString();
	    $coins[$coin] = $v;

		if($coin != "BIP")
		{
		$kuda = $nodeUrl."/estimate_coin_sell?coin_to_sell=$coin&coin_to_buy=BIP&value_to_sell=$amount";
		if($debug)print $kuda."\n";
		$a2 = file_get_contents($kuda);
		$a2 = json_decode($a2,1);
		    $r = $a2[result][will_get];
		    $r /= $devider;
		}
		else
		$r = $amount/$devider;
		if($debug)print "\tr = $r\n";
		$out[$wal] += $r;
	    }
	}

ksort($coins);
if($debug)print_r($coins);
foreach($coins as $coin=>$amount)
{
    if($coin != "BIP")
    {
    $kuda = $nodeUrl."/estimate_coin_sell?coin_to_sell=$coin&coin_to_buy=BIP&value_to_sell=$amount";
    if($debug)print $kuda."\n";
    $a = file_get_contents($kuda);
    $a = json_decode($a,1);
    if($debug)print_r($a);
    $r = $a[result][will_get];
//    $r /= $devider;
    if($debug)print "r = $r\n";
    $v = $r/$devider;
    $coins2[coin][$coin] = $v;
    $coins2[all] += $v;
    }

}
foreach($out as $wal=>$v)
{
    $wname = $wals2[$wal];
    $l = strlen($wname);
    for($i=0;$i<(42-$l);$i++)
    $wname .= " ";
    print "\033[01;32m $wname \t[$wal] \033[00m \t$def_coin\t";
    $amount = round($v,4);
    print "\033[01;35m $amount \033[00m";
    print "\n";
}
print "AMOUNT: ".$coins2[all]." BIP\n";
foreach($coins2[coin] as $coin=>$v)
{
    $c = $coin;
    $l = strlen($coin);
    for($i=0;$i<(10-$l);$i++)$c .= " ";
    print "\033[01;32m $c \033[00m\t";
    $amount = round($v,4);
    print "\033[01;35m $amount \033[00m";
    print "\n";

}

?>