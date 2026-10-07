<?php

$eventsFile = "data/events.json";

$id = $_GET["id"] ?? "";

if (file_exists($eventsFile)) {

    $events = json_decode(
        file_get_contents($eventsFile),
        true
    );

    $events = array_filter(
        $events,
        function ($event) use ($id) {
            return $event["id"] !== $id;
        }
    );

    $events = array_values($events);

    file_put_contents(
        $eventsFile,
        json_encode($events, JSON_PRETTY_PRINT)
    );
}

header("Location: index.php");
exit;

?>