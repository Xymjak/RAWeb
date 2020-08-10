<?php
require_once __DIR__ . '/../lib/bootstrap.php';
require_once(__DIR__ . '/../lib/database/events.php');

function AddTestEventsQuick() {
    if (GetEventsCount() > 0)
        return;

    $result = AddEvent("Achievement of the Week", "AOTW", "Meme of the week",
                       "[b]Weekly[/b]: Get [ach=3181] from [game=745]<br>[b]Monthly[/b]: Any one of [game=1435]s 4 ending achievements",
                       "any link here", 1, true, "2020-01-01 00:00:00", "2020-12-31 00:00:00",
                       "Flara", "ikki", null );

    $result = AddEvent("The Big Achievement", "TBA", "~Bonus~ AOTW",
                       "[b]Bi-Weekly[/b]: Get [ach=108577] from [game=14379] or [ach=63880] from [game=11703]<BR>[b]May[/b]: Test line <br>Test line 2",
                       "any", 1, true, "2020-01-01 00:00:00", "2020-12-31 00:00:00",
                       'televandalist', null, null );

    $result = AddEvent("Leap Frog 4", "LF4", "The Suffering",
                       "Bla-bla-bla some test text",
                       "link", 0, false, "2020-02-01 00:00:00", "2020-05-20 00:00:00",
                       'Boldewin', 'televandalist', null );

    $result = AddEvent("Retro Points Master", "RPM2", "The suffering, light edition",
                       null,
                       null, 2, false, null, null,
                       'JAM', null, null );

    $result = AddEvent("Some Hidden Event", null, null,
                       null,
                       null, 2, false, null,null,
                       null, null, null );

    return;
}

AddTestEventsQuick();
