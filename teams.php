<?php
session_start();

// Fetch registered students
$students = $_SESSION['students'] ?? [];

// Group students by their group ID (if any)
$grouped_students = [];
$individual_students = [];

foreach ($students as $student) {
    if (!empty($student['group_id'])) {
        $grouped_students[$student['group_id']][] = $student;
    } else {
        $individual_students[] = $student;
    }
}

// Team formation logic
$teams = [];
$current_team = ['boys' => [], 'girls' => []];

// First, try to complete existing groups
foreach ($grouped_students as $group_id => $group) {
    $boys = array_filter($group, fn($s) => $s['gender'] === 'Male');
    $girls = array_filter($group, fn($s) => $s['gender'] === 'Female');

    // If there are boys or girls missing, try to complete the group with individual students
    while (count($boys) < 5 && !empty($individual_students)) {
        $student = array_shift($individual_students);
        if ($student['gender'] === 'Male') {
            $boys[] = $student;
        } elseif ($student['gender'] === 'Female' && count($girls) < 3) {
            $girls[] = $student;
        }
    }

    // If the group reaches the required number of boys and girls, add it to the team list
    if (count($boys) === 5 && count($girls) === 3) {
        $teams[] = ['boys' => $boys, 'girls' => $girls];
    } else {
        // Add remaining members back to individual students for further grouping
        $individual_students = array_merge($individual_students, $boys, $girls);
    }
}

// Now, fill in teams with remaining individual students
foreach ($individual_students as $student) {
    if ($student['gender'] === 'Male' && count($current_team['boys']) < 5) {
        $current_team['boys'][] = $student;
    } elseif ($student['gender'] === 'Female' && count($current_team['girls']) < 3) {
        $current_team['girls'][] = $student;
    }

    // When the team is complete
    if (count($current_team['boys']) === 5 && count($current_team['girls']) === 3) {
        $teams[] = $current_team;
        $current_team = ['boys' => [], 'girls' => []];
    }
}

// Display teams
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teams</title>
</head>
<body>
    <h1>Rafting Teams</h1>
    <?php if (empty($teams)): ?>
        <p>No complete teams formed yet.</p>
    <?php else: ?>
        <?php foreach ($teams as $index => $team): ?>
            <h2>Team <?php echo $index + 1; ?></h2>
            <ul>
                <?php foreach ($team['boys'] as $boy): ?>
                    <li><?php echo htmlspecialchars($boy['name']); ?> (Male, Section <?php echo htmlspecialchars($boy['section']); ?>)</li>
                <?php endforeach; ?>
                <?php foreach ($team['girls'] as $girl): ?>
                    <li><?php echo htmlspecialchars($girl['name']); ?> (Female, Section <?php echo htmlspecialchars($girl['section']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="index.php">Register More Students</a>
</body>
</html>
