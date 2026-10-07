<?php

$eventsFile = "data/events.json";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $category = $_POST["category"] ?? "";
    $date = $_POST["date"] ?? "";
    $time = $_POST["time"] ?? "";
    $reminder = $_POST["reminder"] ?? "";
    $description = trim($_POST["description"] ?? "");

    // Validation
    if ($title === "") {
        $errors[] = "Event title is required.";
    }

    if ($date === "") {
        $errors[] = "Date is required.";
    }

    if ($time === "") {
        $errors[] = "Time is required.";
    }

    if (empty($errors)) {

        // Load existing events
        if (file_exists($eventsFile)) {
            $events = json_decode(
                file_get_contents($eventsFile),
                true
            );
        } else {
            $events = [];
        }

        // Create new event
        $newEvent = [
            "id" => uniqid(),
            "title" => $title,
            "category" => $category,
            "date" => $date,
            "time" => $time,
            "reminder" => $reminder,
            "description" => $description
        ];

        // Add event
        $events[] = $newEvent;

        // Save event
        file_put_contents(
            $eventsFile,
            json_encode($events, JSON_PRETTY_PRINT)
        );

        // Return to calendar
        header("Location: index.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Event</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <header>

        <h1>➕ Add New Event</h1>

        <p>Create an academic or personal reminder</p>

    </header>

    <?php if (!empty($errors)): ?>

        <div class="errors">

            <?php foreach ($errors as $error): ?>

                <p>⚠️ <?php echo htmlspecialchars($error); ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <form method="POST" class="event-form">

        <label>
            Event Title
        </label>

        <input
            type="text"
            name="title"
            placeholder="Example: Database Assignment"
            required
        >

        <label>
            Category
        </label>

        <select name="category">

            <option value="Academic">
                📚 Academic
            </option>

            <option value="Work">
                💼 Work
            </option>

            <option value="Personal">
                👤 Personal
            </option>

            <option value="Health">
                🩺 Health
            </option>

            <option value="Appointment">
                📅 Appointment
            </option>

            <option value="Family">
                👨‍👩‍👧 Family
            </option>

            <option value="Other">
                📌 Other
            </option>

        </select>

        <label>
            Date
        </label>

        <input
            type="date"
            name="date"
            required
        >

        <label>
            Time
        </label>

        <input
            type="time"
            name="time"
            required
        >

        <label>
            Remind me
        </label>

        <select name="reminder">

            <option value="5">
                5 minutes before
            </option>

            <option value="15">
                15 minutes before
            </option>

            <option value="30">
                30 minutes before
            </option>

            <option value="60">
                1 hour before
            </option>

            <option value="1440">
                1 day before
            </option>

        </select>

        <label>
            Description
        </label>

        <textarea
            name="description"
            placeholder="Add additional information..."
        ></textarea>

        <button type="submit">
            Add Event
        </button>

    </form>

    <a href="index.php" class="back-link">
        ← Back to Calendar
    </a>

</div>

</body>

</html>