<?php

$eventsFile = "data/events.json";

if (file_exists($eventsFile)) {
    $events = json_decode(file_get_contents($eventsFile), true);
} else {
    $events = [];
}

// Get selected month/year
$month = isset($_GET["month"]) ? (int)$_GET["month"] : date("n");
$year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

// Fix previous/next year
if ($month < 1) {
    $month = 12;
    $year--;
}

if ($month > 12) {
    $month = 1;
    $year++;
}

// Number of days in month
$daysInMonth = cal_days_in_month(
    CAL_GREGORIAN,
    $month,
    $year
);

// First day of month
$firstDay = date(
    "w",
    strtotime("$year-$month-01")
);

// Month name
$monthName = date(
    "F",
    strtotime("$year-$month-01")
);

// Organize events by date
$eventsByDate = [];

foreach ($events as $event) {

    $date = $event["date"];

    if (!isset($eventsByDate[$date])) {
        $eventsByDate[$date] = [];
    }

    $eventsByDate[$date][] = $event;
}

// Today
$today = date("Y-m-d");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Personal Smart Calendar</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <header>

        <h1>📅 My Personal Calendar</h1>

        <p>
            Organize your academic and personal life
        </p>

    </header>

    <div class="calendar-header">

        <a
            href="?month=<?php echo $month - 1; ?>&year=<?php echo $year; ?>"
            class="nav-button"
        >
            ←
        </a>

        <h2>
            <?php echo $monthName . " " . $year; ?>
        </h2>

        <a
            href="?month=<?php echo $month + 1; ?>&year=<?php echo $year; ?>"
            class="nav-button"
        >
            →
        </a>

    </div>

    <div class="calendar">

        <!-- Days of week -->

        <div class="day-name">Sun</div>
        <div class="day-name">Mon</div>
        <div class="day-name">Tue</div>
        <div class="day-name">Wed</div>
        <div class="day-name">Thu</div>
        <div class="day-name">Fri</div>
        <div class="day-name">Sat</div>

        <!-- Empty spaces before first day -->

        <?php for ($i = 0; $i < $firstDay; $i++): ?>

            <div class="calendar-day empty-day"></div>

        <?php endfor; ?>


        <!-- Calendar days -->

        <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>

            <?php

            $date = sprintf(
                "%04d-%02d-%02d",
                $year,
                $month,
                $day
            );

            $isToday = ($date === $today);

            ?>

            <div
                class="calendar-day
                <?php echo $isToday ? 'today' : ''; ?>"
            >

                <div class="date-number">

                    <?php echo $day; ?>

                </div>


                <?php if (isset($eventsByDate[$date])): ?>

                    <?php foreach ($eventsByDate[$date] as $event): ?>

                        <div class="calendar-event">

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $event["title"]
                                );
                                ?>
                            </strong>

                            <small>

                                ⏰
                                <?php
                                echo htmlspecialchars(
                                    $event["time"]
                                );
                                ?>

                            </small>

                            <small>

                                📌
                                <?php
                                echo htmlspecialchars(
                                    $event["category"]
                                );
                                ?>

                            </small>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        <?php endfor; ?>

    </div>


    <div class="calendar-actions">

        <a href="add_event.php" class="add-button">
            + Add New Event
        </a>
        <a href="reminders.php" class="add-button">
            🔔 Reminders
        </a>

    </div>


    <!-- Upcoming Events -->

    <section class="upcoming">

        <h2>🔔 Upcoming Events</h2>

        <?php

        $upcomingEvents = [];

        $currentDateTime = time();

        foreach ($events as $event) {

            $eventTime = strtotime(
                $event["date"] . " " . $event["time"]
            );

            if ($eventTime >= $currentDateTime) {

                $upcomingEvents[] = $event;

            }
        }

        usort(
            $upcomingEvents,
            function ($a, $b) {

                return strtotime(
                    $a["date"] . " " . $a["time"]
                )
                -
                strtotime(
                    $b["date"] . " " . $b["time"]
                );

            }
        );

        ?>

        <?php if (empty($upcomingEvents)): ?>

            <p>No upcoming events.</p>

        <?php else: ?>

            <?php foreach (array_slice($upcomingEvents, 0, 5) as $event): ?>

                <div class="upcoming-event">

                    <div>

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $event["title"]
                            );
                            ?>
                        </strong>

                        <p>

                            📅
                            <?php
                            echo htmlspecialchars(
                                $event["date"]
                            );
                            ?>

                            &nbsp;

                            ⏰
                            <?php
                            echo htmlspecialchars(
                                $event["time"]
                            );
                            ?>

                        </p>

                    </div>

                    <a
                        href="delete_event.php?id=<?php echo $event["id"]; ?>"
                        class="delete-button"
                        onclick="return confirm('Delete this event?');"
                    >
                        Delete
                    </a>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</div>

</body>

</html>