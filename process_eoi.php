<?php

// Blocks direct access, ensures POST request has core fields.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_POST['jobref'], $_POST['firstname'],
             $_POST['lastname'], $_POST['dob'])) {
    header('Location: apply.php');
    exit;
}

$pageTitle = 'Application Result | FABWEB';

// protects against potentially malicious input
function post_string($key) {
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function escapeHtml(string $value): string {
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// ----------- Collect raw values -------------
$raw = [
    'jobref'      => strtoupper(post_string('jobref')),
    'firstname'   => post_string('firstname'),
    'lastname'    => post_string('lastname'),
    'dob'         => post_string('dob'),
    'gender'      => post_string('gender'),
    'street'      => post_string('street'),
    'suburb'      => post_string('suburb'),
    'state'       => post_string('state'),
    'postcode'    => post_string('postcode'),
    'email'       => post_string('email'),
    'phone'       => post_string('phone'),
    'other_skills'=> post_string('other_skills')
];

$errors        = [];
$eoiNumber     = null;
$databaseError = false;
$conn          = null;
$validJobs     = [];

// --------- Load valid job references from jobs table -----------
require_once __DIR__ . '/settings.php';

try {
    mysqli_report(MYSQLI_REPORT_ERROR);
    $conn = new mysqli($host, $user, $pwd, $sql_db);
    $conn->set_charset('utf8mb4');

    $result = $conn->query("SELECT jobref FROM jobs");
    while ($row = $result->fetch_assoc()) {
        $validJobs[] = $row['jobref'];
    }
    $result->free();
    } catch (mysqli_sql_exception $exception) {
    error_log('FABWEB database error: ' . $exception->getMessage());
    $databaseError = true;
}

// ----------------------- Validation -----------------------------
if (!is_valid_job_ref($raw['jobref'])) {
    $errors[] = 'Job reference must have 6 letters or numbers.';
} elseif (!is_in_list($raw['jobref'], $validJobs)) {
    $errors[] = 'Please enter a valid FABWEB job reference.';
}

if (!is_valid_name($raw['firstname'], 20)) {
    $errors[] = 'First name must contain 1-20 letters only.';
}
if (!is_valid_name($raw['lastname'], 20)) {
    $errors[] = 'Last name must contain 1-20 letters only.';
}

$dobForDatabase = null;
if (!is_valid_date($raw['dob'])) {
    $errors[] = 'Date of birth must be a real date in dd/mm/yyyy format.';
} else {
    $dob   = DateTimeImmutable::createFromFormat('!d/m/Y', $raw['dob']);
    $today = new DateTimeImmutable('today');
    $age   = $dob->diff($today)->y;

    if ($dob > $today) {
        $errors[] = 'Date of birth cannot be in the future.';
    } elseif ($age < 18 || $age > 100) {
        $errors[] = 'Applicants must be between 18 and 100 years old.';
    } else {
        $dobForDatabase = $dob->format('Y-m-d');
    }
}

if (!is_in_list($raw['gender'], ['female', 'male', 'other'])) {
    $errors[] = 'Please select a valid gender option.';
}

foreach (['street' => 'Street address', 'suburb' => 'Suburb/town'] as $field => $label) {
    $length = mb_strlen($raw[$field], 'UTF-8');
    if ($length < 1 || $length > 40) {
        $errors[] = "$label must contain 1-40 characters.";
    }
}


$validStates = ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'];
$stateOk = is_in_list($raw['state'], $validStates);
if (!$stateOk) {
    $errors[] = 'Please select a valid state or territory.';
}

$postcodeOk = is_valid_postcode($raw['postcode']);
if (!$postcodeOk) {
    $errors[] = 'Postcode must contain exactly 4 digits.';
}

if ($stateOk && $postcodeOk && !postcode_matches_state($raw['postcode'], $raw['state'])) {
    $errors[] = 'Postcode does not match the selected state.';
}

if (!is_valid_email($raw['email'])) {
    $errors[] = 'Please enter a valid email address.';
}

if (!is_valid_phone($raw['phone'])) {
    $errors[] = 'Phone number must contain 8-12 digits or spaces.';
}

$allowedSkills = [
    'cybersecurity-fundamentals', 'network-security', 'incident-response',
    'siem', 'web-security', 'penetration-testing', 'cloud-security',
    'linux', 'scripting', 'risk-management', 'security-awareness',
    'technical-documentation'
];

$submittedSkills = $_POST['skills'] ?? [];
if (!is_array($submittedSkills)) {
    $errors[] = 'Invalid skills format.';
    $submittedSkills = [];
}

$skillsAllValid = true;
foreach ($submittedSkills as $skill) {
    if (!is_in_list($skill, $allowedSkills)) {
        $skillsAllValid = false;
        break;
    }
}

if (empty($submittedSkills)) {
    $errors[] = 'Please select at least one skill.';
} elseif (!$skillsAllValid) {
    $errors[] = 'An invalid skill was selected.';
} elseif (!in_array('cybersecurity-fundamentals', $submittedSkills, true)) {
    // Required checkbox carried over from Part 1.
    $errors[] = 'Cybersecurity Fundamentals is required.';
}

if (mb_strlen($raw['other_skills'], 'UTF-8') > 5000) {
    $errors[] = 'Other skills must be under 5000 characters.';
}

// --------- Sanitise ------------
$safe = sanitise($raw);
$skillsJson = json_encode(array_values(array_unique($submittedSkills)));

// ----------- Store -------------
if (empty($errors) && !$databaseError) {
    try {
        // Column sizes larger than validation limits because of HTML-escape 
        $createTable = "
            CREATE TABLE IF NOT EXISTS eoi (
                EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                jobref CHAR(6) NOT NULL,
                firstname VARCHAR(120) NOT NULL,
                lastname VARCHAR(120) NOT NULL,
                dob DATE NOT NULL,
                gender ENUM('female','male','other') NOT NULL,
                street VARCHAR(240) NOT NULL,
                suburb VARCHAR(240) NOT NULL,
                state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
                postcode CHAR(4) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(12) NOT NULL,
                skills TEXT NOT NULL,
                other_skills TEXT,
                status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_jobref (jobref),
                INDEX idx_applicant (lastname, firstname)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        $conn->query($createTable);

        $sql = "
            INSERT INTO eoi (
                jobref, firstname, lastname, dob, gender, street, suburb,
                state, postcode, email, phone, skills, other_skills
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssssssssss',
            $safe['jobref'],
            $safe['firstname'],
            $safe['lastname'],
            $dobForDatabase,
            $safe['gender'],
            $safe['street'],
            $safe['suburb'],
            $safe['state'],
            $safe['postcode'],
            $safe['email'],
            $safe['phone'],
            $skillsJson,
            $safe['other_skills']
        );
        $stmt->execute();

        $eoiNumber = $conn->insert_id;
        $stmt->close();
    } catch (mysqli_sql_exception $exception) {
        error_log('FABWEB database error: ' . $exception->getMessage());
        $databaseError = true;
    }
}


