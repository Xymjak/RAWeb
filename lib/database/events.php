<?php
require_once(__DIR__ . '/../util/database.php');

function GetUserIdByName($userName)
{
    //There already should be something like this function somewhere in the site's code, but I didn't found
    //UPD: Okay, we don't need this...
    $query = "SELECT ID FROM UserAccounts WHERE user = '$userName'";
    $result = s_mysql_query($query);

    if ($result != true) {
        return null;
    }

    $user = mysqli_fetch_object($result);
    return $user->ID;
}


function PrepareVarToSql($var)
{
    //there should be code that allows "forbidden" words without SQL-injection
    return empty($var) ? 'NULL' : "'$var'";
}

function PrepareVarsToSql($name, $nameShort, $description, $payLoad, $link,
    $main, $host1, $host2, $host3,
    &$nameS, &$nameShortS, &$descriptionS, &$payLoadS, &$linkS,
    &$mainS, &$host1S, &$host2S, &$host3S)
{
    //is there a better way?
    $nameS = PrepareVarToSql($name);
    $nameShortS = PrepareVarToSql($nameShort);
    $descriptionS = PrepareVarToSql($description);
    $payLoadS = PrepareVarToSql($payLoad);
    $linkS = PrepareVarToSql($link);
    $host1S = PrepareVarToSql($host1);
    $host2S = PrepareVarToSql($host2);
    $host3S = PrepareVarToSql($host3);
    $mainS = (int)$main;
}

function GetEventsCount()
{
    $countRowsQuery = "SELECT ID FROM events WHERE ID IS NOT NULL";
    $result = s_mysql_query($countRowsQuery);
    return mysqli_num_rows($result);
}


function AddEvent($name, $nameShort, $description, $payLoad, $link,
    $status, $main, $start, $end, $host1Name, $host2Name, $host3Name)
{
    $displayOrder = GetEventsCount() + 1;//Start from 1
    //May be get the biggest order number instead?

    PrepareVarsToSql($name, $nameShort, $description, $payLoad, $link,
        $main, $host1Name, $host2Name, $host3Name,
        $nameS,$nameShortS,$descriptionS,$payLoadS,$linkS,
        $mainS,$host1S,$host2S,$host3S);
    $insertQuery = "INSERT INTO events
    (`Name`, `NameShort`, `Description`, `Payload`, `Link`, 
     `Status`, `Main`, `Start`, `End`, 
     `Host1`, `Host2`, `Host3`, `DisplayOrder`) 
     VALUES 
            ($nameS, $nameShortS, $descriptionS, $payLoadS, $linkS, 
             $status, $mainS, '$start', '$end', 
             $host1S, $host2S, $host3S, $displayOrder)";
    return s_mysql_query($insertQuery);
}

function EditEvent($id, $name, $nameShort, $description, $payLoad, $link,
    $status, $main, $start, $end, $host1Name, $host2Name, $host3Name)
{
    PrepareVarsToSql($name, $nameShort, $description, $payLoad, $link,
                     $main, $host1Name, $host2Name, $host3Name,
                     $nameS,$nameShortS,$descriptionS,$payLoadS,$linkS,
                     $mainS,$host1S,$host2S,$host3S);

    $updateQuery = "UPDATE events
        SET Name = $nameS, NameShort = $nameShortS, Description = $descriptionS, Payload = $payLoadS, 
            Link = $linkS, Status = $status, Main = $mainS, 
            Start = '$start', End = '$end', 
            Host1 = $host1S, Host2 = $host2S, Host3 = $host3S
            WHERE `events`.`Id` = $id";
    return s_mysql_query($updateQuery);
}

function GetEmptyEventData()
{
    // Didn't want to do a separate page or checking every value for create/edit page
    // How to create an empty object with mysql request?!
    return (object)[
        'ID' => -1,
        'Name' => "",
        'NameShort' => "",
        'Description' => "",
        'Payload' => "",
        'Link' => "",
        'Status' => 4,
        'Main' => false,
        'Start' => null,
        'End' => null,
        'Host1' => "",
        'Host2' => "",
        'Host3' => "",
        'DisplayOrder' => null
    ];
}

function GetEventData($eventId)
{
    $selectQuery = "SELECT * FROM events WHERE id=$eventId";
    $result = s_mysql_query($selectQuery);
    $event = mysqli_fetch_object($result);
    return $event;
}

function GetMainEvents()
{
    $selectQuery = "SELECT * FROM events WHERE (Status>=0 AND Main=1) ORDER BY DisplayOrder DESC";
    $result = s_mysql_query($selectQuery);

    $events = null;
    while ($nextData = mysqli_fetch_object($result)) {
        $events[] = $nextData;
    }
    return $events;
}

function GetAllEvents($showHidden, $groupByType)
{
    $statusLimit = ($showHidden) ? -1 : 0;
    $selectQueryStatusPart = $groupByType ? " Status DESC," : "";
    $selectQuery = "SELECT * FROM events WHERE Status>=$statusLimit 
    ORDER BY $selectQueryStatusPart DisplayOrder DESC";
    $result = s_mysql_query($selectQuery);
    $events = null;
    while ($nextData = mysqli_fetch_object($result)) {
        $events[] = $nextData;
    }
    return $events;
}
