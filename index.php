<?php
session_start();

// Initialize the student data storage
if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $section = $_POST['section'];
    $group_id = $_POST['group_id']; // Track group ID for pre-existing groups

    // Generate a unique session ID for each student
    $session_id = session_id();

    // Save student data to the session
    $_SESSION['students'][] = [
        'name' => $name,
        'gender' => $gender,
        'section' => $section,
        'group_id' => $group_id, // Store the group ID
        'session_id' => $session_id // Store the session ID
    ];

    // Redirect to avoid duplicate submissions
    header('Location: teams.php');
    exit;
}

// Process reset action (only for the current user's data)
if (isset($_POST['reset'])) {
    // Remove only the current user's data from the session
    if (isset($_SESSION['students'])) {
        foreach ($_SESSION['students'] as $index => $student) {
            if ($student['session_id'] === session_id()) {
                unset($_SESSION['students'][$index]);
                break;
            }
        }
    }

    // Redirect to clear the form and show updated session data
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rafting Team Registration</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Confirm the submission action
        function confirmSubmission() {
            return confirm("Are you sure you want to submit your registration?");
        }

        // Confirm the reset action
        function confirmReset() {
            return confirm("Are you sure you want to reset your data? This action cannot be undone.");
        }
    </script>
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .button-container button {
            width: 48%;
            padding: 10px;
            font-size: 16px;
        }

        .reset-btn {
            background-color: red;
            color: white;
            border: none;
        }

        .submit-btn {
            background-color: green;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <h1>Rafting Team Registration</h1>
    <form action="index.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select><br><br>

        <label for="section">Section:</label>
        <select id="section" name="section" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
            <option value="F">F</option>
            <option value="G">G</option>
            <option value="H">H</option>
            <option value="I">I</option>
        </select><br><br>

        <label for="group_id">Group ID (leave blank if individual):</label>
        <input type="text" id="group_id" name="group_id" placeholder="e.g., Group1"><br><br>

        <div class="button-container">
            <button type="submit" name="submit" class="submit-btn" onclick="return confirmSubmission()">Submit</button>
            <button type="submit" name="reset" class="reset-btn" onclick="return confirmReset()">Reset Data</button>
        </div>
    </form>
</body>
</html>
