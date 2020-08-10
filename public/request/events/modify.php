<?php

use RA\Permissions;

function CheckValue($key)
{
    $val = seekPOSTorGET($key, null);
    if (empty($val)) {
        return null;
    }
    return $val;
}

function CheckTimeValue($key)
{
    $val = CheckValue($key);
    if ($val == "____-__-__ __:__:__") {
        return null;
    }
    return $val;
}

require_once __DIR__ . '/../../../lib/bootstrap.php';
RA_ReadCookieCredentials($user, $points, $truePoints, $unreadMessageCount, $permissions, \RA\Permissions::Admin);

if ($permissions < Permissions::Admin) {
    echo "FAILED";
    return;
}

/*
if (!ValidatePOSTorGETChars("ndplstmfh123")) {
    echo "FAILED";
    return;
}*/
$id = seekPOSTorGET('id', -1);
$name = CheckValue('n');
$nameShort = CheckValue('ns');
$description = CheckValue('d');
$payLoad = CheckValue('p');
$link = CheckValue('l');
$status = seekPOSTorGET('s', -1);
$main = seekPOSTorGET('m', 0);
if ($main == "") {
    $main = 1;
}

$host1Name = CheckValue('h1');
$host2Name = CheckValue('h2');
$host3Name = CheckValue('h3');

$start = CheckTimeValue('st');
$end = CheckTimeValue('f');

if ($id < 0) {
    $result = AddEvent($name, $nameShort, $description, $payLoad, $link,
        $status, $main, $start, $end, $host1Name, $host2Name, $host3Name);
} else {
    $result = EditEvent($id, $name, $nameShort, $description, $payLoad, $link,
        $status, $main, $start, $end, $host1Name, $host2Name, $host3Name);
}

if (!$result) {
    echo "FAILED";
    return;
}

header("location: " . getenv('APP_URL') . "/events.php");
