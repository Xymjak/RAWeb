<?php
$debug = false; //TODO: Remove

use RA\Permissions;

require_once __DIR__ . '/../lib/bootstrap.php';

RA_ReadCookieCredentials($user, $points, $truePoints, $unreadMessageCount, $permissions);

$count = 50;
$offset = 0;
$editId = -1;
$modify = false;
$groupByType = true;
$eventData = null;

$errorCode = seekGET('e');
$groupByType = seekGET('g', $groupByType);
$decreasedPermission = seekGET('p', $permissions);
$jsEdit = seekGET('js', true);
$jsEdit = false; //not implemented yet
$permissions = $debug ? $decreasedPermission : min($decreasedPermission, $permissions);

$eventData = null;

if($permissions >= Permissions::Admin)  {
    //If not admin, new and edit(id) flags simply will not work.
    $new = seekGET('new', false);
    $edit = false;
    if($new) {
        $eventData = GetEmptyEventData();
    }
    else {
        //new flag has priority over edit
        $editId = seekGET('edit', $editId);
        $edit = $editId > -1;
        if ($edit) {
            $eventData = GetEventData($editId);
            if ($eventData == null) {
                header("location: " . getenv('APP_URL') . "/events.php?e=unknownEventId");
                exit;
            }
        }
    }

    $modify = $new || $edit;
}

RenderHtmlStart();
RenderHtmlHead("Events");
echo "<body>";
RenderTitleBar($user, $points, $truePoints, $unreadMessageCount, $errorCode, $permissions);
RenderToolbar($user, $permissions);

echo "<div id='mainpage'><div id='fullcontainer'>";
if($modify) {
    //admins only
    RenderEvents::EditPage($eventData);
} else {
    //TODO: Pages?
    $showHidden = $permissions >= Permissions::Admin;
    $evensAllData = GetAllEvents($showHidden, $groupByType);
    RenderEvents::Page($evensAllData, $groupByType, $permissions, $jsEdit);
}
echo "</div></div>";
RenderFooter();
echo "</body>";
RenderHtmlEnd();
