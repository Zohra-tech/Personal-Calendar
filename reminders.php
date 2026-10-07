<?php

$eventsFile = "data/events.json";

if (file_exists($eventsFile)) {

    $json = file_get_contents($eventsFile);

    $events = json_decode($json, true);

    if (!is_array($events)) {
        $events = [];
    }

} else {

    $events = [];

}

// Current time
$currentTime = time();

// Store upcoming reminders
$upcomingReminders = [];

foreach ($events as $event) {

    $eventTime = strtotime(
        $event["date"] . " " . $event["time"]
    );

    $reminderMinutes = isset($event["reminder"])
        ? (int)$event["reminder"]
        : 0;

    $reminderTime =
        $eventTime - ($reminderMinutes * 60);

    // Event hasn't happened yet
    if ($eventTime >= $currentTime) {

        $upcomingReminders[] = [
            "title" => $event["title"],
            "date" => $event["date"],
            "time" => $event["time"],
            "category" => $event["category"],
            "reminder" => $reminderMinutes,
            "reminderTime" => $reminderTime
        ];
    }
}

// Sort reminders
usort(
    $upcomingReminders,
    function ($a, $b) {
        return $a["reminderTime"] - $b["reminderTime"];
    }
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Reminders</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<div class="container">

    <header>

        <h1>🔔 My Reminders</h1>

        <p>
            Upcoming reminders for your personal calendar
        </p>

    </header>

    <div class="upcoming">

        <?php if (empty($upcomingReminders)): ?>

            <div class="empty">

                <h2>No Upcoming Reminders</h2>

                <p>
                    Add an event with a reminder to see it here.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($upcomingReminders as $reminder): ?>

                <div class="upcoming-event">

                    <div>

                        <h3>
                            🔔
                            <?php
                            echo htmlspecialchars(
                                $reminder["title"]
                            );
                            ?>
                        </h3>

                        <p>

                            📅
                            <?php
                            echo htmlspecialchars(
                                $reminder["date"]
                            );
                            ?>

                        </p>

                        <p>

                            ⏰
                            Event time:
                            <?php
                            echo htmlspecialchars(
                                $reminder["time"]
                            );
                            ?>

                        </p>

                        <p>

                            🔔
                            Reminder:
                            <?php

                            $minutes =
                                $reminder["reminder"];

                            if ($minutes >= 1440) {

                                echo "1 day before";

                            } elseif ($minutes >= 60) {

                                echo
                                    ($minutes / 60)
                                    . " hour(s) before";

                            } else {

                                echo
                                    $minutes
                                    . " minutes before";
                            }

                            ?>

                        </p>

                        <p>

                            📌
                            <?php
                            echo htmlspecialchars(
                                $reminder["category"]
                            );
                            ?>

                        </p>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <div class="calendar-actions">

        <a
            href="index.php"
            class="add-button"
        >
            ← Back to Calendar
        </a>

    </div>

</div>

</body>

</html>