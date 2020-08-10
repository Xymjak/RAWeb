<?php

use RA\Permissions;

class RenderEvents
{
    private function __construct()
    {
    } //static class

    private static function GetStatusNameByValue($id)
    {
        $name = "unknown";
        switch ($id) {
            case -1:
                $name = "Hidden";
                break;
            case 0:
                $name = "Ended";
                break;
            case 1:
                $name = "Active (Closed)"; //Need some better name
                break;
            case 2:
                $name = "Active (Open)";
                break;
            case 3:
                $name = "Upcoming";
                break;
            case 4:
                $name = "Upcoming (Registration)";
                break;
        }

        return $name;
    }

    private static function IsTimeUnknown($time)
    {
        return (empty($time)
            || (date("Y", strtotime($time)) < 2015));
        //Because I don't know if the site's DB configured to allow NULL for dates
    }

//========================================
// The Site's Main Page Render (index.php)
//========================================

    private static function OneMain($event)
    {
        echo "<div id='event$event->ID' style='text-align:left;'>"; //DO CSS!
        if (empty($event->Name) || empty($event->Payload)) {
            return;
        }

        $hasLink = !empty($event->Link);
        if ($hasLink) {
            echo "<a href='$event->Link'>";
        }
        $renderName = empty($event->NameShort) ? $event->Name : $event->NameShort;
        echo "<h4>$renderName</h4>";
        if ($hasLink) {
            echo "</a>";
        }

        echo parseTopicCommentPHPBB(nl2br($event->Payload));
        echo "</div>";
    }

    public static function SitesMain($eventsData)
    {
        if (empty($eventsData)) {
            return;
        }
        //TODO: Scrolling block
        echo "<div class='component'>";
        $eventsPage = getEnv("APP_URL") . "/events.php";
        echo "<a href='$eventsPage'><h3>Events</h3></a>";
        $eventsCount = count($eventsData);
        for ($i = 0; $i < $eventsCount; $i++) {
            if ($i > 0) {
                echo "<br><br>";
            }
            RenderEvents::OneMain($eventsData[$i]);
        }
        echo "</div>";
    }

//========================================
// The Create / Edit Page Render
//========================================

    private static function LabelBlock($id, $labelText)
    {
        echo "<td class='text-nowrap'><label for='$id'>$labelText</label></td>";
    }

    private static function EditTextRow($id, $requestName, $labelText, $value, $maxLength, $rows)
    {
        echo "<tr>";
        RenderEvents::LabelBlock($id, $labelText . " (" . $maxLength . " max)");
        echo "<td colspan='3'>";
        if ($rows > 1) {
            RenderPHPBBIcons($id);
            echo "<textarea id='$id' class='fullwidth' name='$requestName' rows='$rows' maxlength='$maxLength'>$value</textarea>";
        } else {
            echo "<input id='$id' class='fullwidth' name='$requestName' maxlength='$maxLength' value='$value'>";
        }
        echo "</td></tr>";
    }

    private static function HostsBlock($eventData, $maxNameLength)
    {
        echo "<tr>";
        RenderEvents::LabelBlock("Host1", "Hosts (User Names)");
        echo "<td><input id='Host1' class='fullwidth' name='h1' maxlength=$maxNameLength value=$eventData->Host1></td>";
        echo "<td><input id='Host2' class='fullwidth' name='h2' maxlength=$maxNameLength value=$eventData->Host2></td>";
        echo "<td><input id='Host3' class='fullwidth' name='h3' maxlength=$maxNameLength value=$eventData->Host3></td>";
        echo "</tr>";
    }

    public static function DateTimePicker($id, $defaultDateTime)
    {
        //this must be in utils?
        echo "<script>";
        echo "jQuery('#$id').datetimepicker({";
        echo "format: 'Y-m-d H:i:s',";
        echo "mask: true,";
        echo "})\n";
        echo "</script>";
    }

    private static function DateTimeBlock($requestName, $labelText, $eventData, $eventTime)
    {
        $currentTimeSet = "";
        if ($eventData->ID > -1) {

            $unknown = RenderEvents::IsTimeUnknown($eventTime);
                //BUG: Because can't set default time for DateTimePicker, writhing the currently set below
                $currentTimeSet = "<br>(Set to: " . ($unknown ? "unknown" : $eventTime) . ")";


        }
        RenderEvents::LabelBlock("edit$labelText", "$labelText$currentTimeSet");
        echo "<td><input id='edit$labelText' class='fullwidth' name='$requestName' value ='$eventTime'></td>";
        RenderEvents::DateTimePicker("edit$labelText", null);
    }

    private static function StatusOption($value, $defaultValue)
    {
        $valueName = RenderEvents::GetStatusNameByValue($value);
        echo "<option value='$value'";
        if ($value == $defaultValue) {
            echo "selected='selected'";
        }
        echo ">$valueName</option>";
    }

    public static function EditPage($eventData)
    {
        echo "<script src=\"./vendor/jquery.datetimepicker.full.min.js\"></script>";
        echo "<link rel=\"stylesheet\" href=\"./vendor/jquery.datetimepicker.min.css\">";

        $header = ($eventData->ID < 0) ? "Create New Event" : "Edit \"" . $eventData->Name . "\" Event";

        $maxNameShortLength = 16;
        $maxUserNameLength = 50;
        $maxNameLength = 64;
        $maxLinkLength = 256;
        $maxDescLength = 1024;
        $maxPayloadLength = 16384;
        //Get the limits with the sql query?

        echo "<h2 class='longheader'>$header</h2>";
        echo "<div style='color:#ff0000'>Warning. Don't enter special words (INSERT, UPDATE, etc) or \"'\",\"%\" or \"_\" symbols or the result will not be saved</div>";
        //until fixed

        $requestPage = getEnv("APP_URL") . "/request/events/modify.php";
        echo "<form method='post' action='$requestPage'>";
        echo "<table class=\"mb-1\">";
        echo "<colgroup><col class='eventseditcol1'><col><col><col></colgroup>";
        echo "<tbody>";

        echo "<input type='hidden' id='eventId' name='id' value='$eventData->ID'>";

        RenderEvents::EditTextRow("editName", "n", "Name",
                                    $eventData->Name, $maxNameLength,1);
        RenderEvents::EditTextRow("editNameShort","ns","NameShort",
                                    $eventData->NameShort, $maxNameShortLength, 1);
        RenderEvents::EditTextRow("editLink", "l", "Link",
                                    $eventData->Link, $maxLinkLength, 1);
        RenderEvents::EditTextRow("eventDescription", "d", "Description",
                                    $eventData->Description, $maxDescLength, 2);
        RenderEvents::EditTextRow("eventPayload", "p", "Text",
                                    $eventData->Payload, $maxPayloadLength, 5);

        RenderEvents::HostsBlock($eventData, $maxUserNameLength);

        echo "<tr>";
        RenderEvents::DateTimeBlock("st", "Start", $eventData, $eventData->Start);
        RenderEvents::DateTimeBlock("f", "End", $eventData, $eventData->End);

        echo "</tr>";

        echo "<tr>";
        RenderEvents::LabelBlock("editStatus", "Status");
        echo "<td><select id='editStatus' name='s'>";
        for ($i = 4; $i > -2; $i--) {
            RenderEvents::StatusOption($i, $eventData->Status);
        }
        echo "</select></td>";
        RenderEvents::LabelBlock("editMain", "Show on the main page?");
        echo "<td><input type='checkbox' id='editMain' style='align-content: normal' name='m' value=$eventData->Main></td>";
        echo "</tr>";
        echo "</tbody></table>";

        $submitText = ($eventData->ID < 0) ? "Create New" : "Save Edits";
        $style = "style='font-size : 15px'"; //TO CSS?
        echo "<div style='text-align:center'><input type='submit' $style value='$submitText'></div>";
        echo "</form>";
    }

//========================================
// The Events Page Render
//========================================

    private static function Controls($groupByType, $permissions, $jsEditing)
    {
        echo "<div class='rightfloat'>";
        if ($permissions >= Permissions::Admin) {
            echo "<a href='/events.php?p=1'>View the page as a user</a>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<a href='/events.php?new=1'>Create New Event</a>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        //jsEditing will be used for "create".

        echo "<a href='/events.php" .
            ($groupByType ? "?g=0'>Don't group</a>" : "'>Group by status</a>");
        //g=1 not needed because default
        echo "</div>";
    }


    private static function GroupHeader($status)
    {
        $statusName = RenderEvents::GetStatusNameByValue($status);
        echo "<h2 class='longheader'>$statusName Events</h2>";
    }

    private static function EventHeader($event, $groupByType)
    {
        echo "<div class='eventsdescription'>";
        echo "<h4>";
        $hasLink = !empty($event->Link);
        if ($hasLink) {
            echo "<a href='$event->Link'>";
        }

        echo $event->Name;
        if (!empty($event->NameShort)) {
            echo " ($event->NameShort)";
        }
        if ($hasLink) {
            echo "</a>";
        }

        if (!$groupByType) {
            $status = RenderEvents::GetStatusNameByValue($event->Status);
            echo " - $status";
        }

        echo "</h4>";
        echo "</div>";
    }

    private static function EventHosts($event)
    {
        echo "<div class='eventHosts rightfloat smalltext'>";

        $eventHosts = [];
        if (!empty($event->Host1)) {
            $eventHosts[] = $event->Host1;
        }
        if (!empty($event->Host2)) {
            $eventHosts[] = $event->Host2;
        }
        if (!empty($event->Host3)) {
            $eventHosts[] = $event->Host3;
        }

        $eventHostsCount = count($eventHosts);
        if ($eventHostsCount > 0) {
            echo "Event Runner";
            if ($eventHostsCount > 1) {
                echo "s";
            }
            echo ": ";
            for ($i = 0; $i < count($eventHosts); $i++) {
                if ($i > 0) {
                    echo ", ";
                }
                echo GetUserAndTooltipDiv($eventHosts[$i], false, null, 64);
            }
        }

        echo "</div>";
    }

    private static function ConvertEventTimeStampToStr($timeStamp)
    {
        return RenderEvents::IsTimeUnknown($timeStamp)
            ? "unknown"
            : date("d M, Y H:i", strtotime($timeStamp));
    }

    private static function EventTimes($event)
    {
        echo "<div class='text-right'>";

        //echo "<div id='eventStartTime'>";
        $dateStartS = RenderEvents::ConvertEventTimeStampToStr($event->Start);
        echo "Starts:&nbsp;$dateStartS";
        //echo "</div>";
        echo "<br>";
        //echo "<div id='eventEndTime'>";
        $dateEndS = RenderEvents::ConvertEventTimeStampToStr($event->End);
        echo "Ends:&nbsp;$dateEndS";
        //echo "</div>";

        echo "</div>";
    }

    private static function EditBlock($event)
    {
        echo "<div class='smalltext rightfloat'>";
        echo "<input id='$event->ID' type='text' 
        class='rightfloat'
            value='$event->DisplayOrder' 
             onchange=\"updateEventDisplayOrder('$event->ID')\" size='3' />";
        echo "<br><a class='rightfloat' href='/events.php?edit=$event->ID'>Edit Info</a>";
        echo "</div>";
    }

    private static function RightBlock($event, $permissions, $jsEditing)
    {
        echo "<div class='smalltext rightfloat'>";
        RenderEvents::EventHosts($event);
        RenderEvents::EventTimes($event);

        if ($permissions >= Permissions::Admin) {
            RenderEvents::EditBlock($event);
        }
        echo "</div>";
    }

    private static function MainBlock($event, $groupByType)
    {
        echo "<div>";
        RenderEvents::EventHeader($event, $groupByType);

        $description = empty($event->Description)
            ? "No&nbsp;Description"
            : parseTopicCommentPHPBB(nl2br($event->Description));

        echo "<div class='smalltext eventsinfo eventsdescription'>$description</div>";

        if (!empty($event->Payload)) {
            $payLoad = parseTopicCommentPHPBB(nl2br($event->Payload));
            echo "<div class='eventPayload eventsinfo'>$payLoad</div>";
        }

        echo "</div>";
    }

    public static function Page($events, $groupByType, $permissions, $jsEditing)
    {
        if (empty($events)) {
            //Something to display?
            return;
        }

        $jsEditing = false;
        //TODO: Create/Edit on this page with JS, not on separate.
        RenderEvents::Controls($groupByType, $permissions, $jsEditing);
        echo '<table>';
        $status = null;
        foreach ($events as $event) {
            if ($groupByType && ($event->Status != $status)) {
                $notFirstLine = !is_null($status);
                if ($notFirstLine) {
                    echo '</table>';
                }
                $status = $event->Status;
                RenderEvents::GroupHeader($status);
                if ($notFirstLine) {
                    echo '<table>';
                }
            }

            echo "<tr><td>";
            RenderEvents::RightBlock($event, $permissions, $jsEditing);
            RenderEvents::MainBlock($event, $groupByType);
            echo "</td></tr>";
        }
        echo '</table>';
    }
}
