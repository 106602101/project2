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
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQL_REPORT_STRICT);
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





// Job reference: exactly 6 alphanumeric characters.
if (!preg_match('/^[A-Z0-9]{6}$/D', $data['jobref'])) {
    $errors[] = 'Job reference must have 6 letters or numbers.';
}

// Existing positions in the Part 1 project.
$validJobs = ['FW1001', 'FW1002', 'FW1003', 'FW1004'];

if (preg_match('/^[A-Z0-9]{6}$/D', $data['jobref']) &&
    !in_array($data['jobref'], $validJobs, true)) {
    $errors[] = 'Please enter a valid FABWEB job reference.';
}

// First and last names.
foreach (['firstname' => 'First name',
          'lastname' => 'Last name'] as $field => $label) {
    if (!preg_match('/^[A-Za-z]{1,20}$/D', $data[$field])) {
        $errors[] = "$label must contain 1-20 letters only.";
    }
}

// Date of birth: dd/mm/yyyy.
$dobForDatabase = null;

if (!preg_match(
    '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/D',
    $data['dob'],
    $matches
)) {
    $errors[] = 'Date of birth must be dd/mm/yyyy.';
} else {
    $day = (int)$matches[1];
    $month = (int)$matches[2];
    $year = (int)$matches[3];

    if (!checkdate($month, $day, $year)) {
        $errors[] = 'Please enter a real date of birth.';
    } else {
        $dob = DateTimeImmutable::createFromFormat(
            '!d/m/Y',
            $data['dob']
        );

        if (!$dob || $dob > new DateTimeImmutable('today')) {
            $errors[] = 'Date of birth cannot be in the future.';
        } else {
            $dobForDatabase = $dob->format('Y-m-d');
        }
    }
}

// Validate radio-button value.
if (!in_array(
    $data['gender'],
    ['female', 'male', 'other'],
    true
)) {
    $errors[] = 'Please select a valid gender option.';
}

// Address.
foreach (['street' => 'Street address',
          'suburb' => 'Suburb/town'] as $field => $label) {
    $length = mb_strlen($data[$field], 'UTF-8');

    if ($length < 1 || $length > 40) {
        $errors[] = "$label must contain 1-40 characters.";
    }
}

// State or territory.
$validStates = [
    'VIC', 'NSW', 'QLD', 'NT',
    'WA', 'SA', 'TAS', 'ACT'
];

if (!in_array($data['state'], $validStates, true)) {
    $errors[] = 'Please select a valid state or territory.';
}

// Postcode: exactly 4 digits.
if (!preg_match('/^[0-9]{4}$/D', $data['postcode'])) {
    $errors[] = 'Postcode must contain exactly 4 digits.';
}

// Email address.
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) ||
    strlen($data['email']) > 254) {
    $errors[] = 'Please enter a valid email address.';
}

// Phone number: 8 to 12 digits.
if (!preg_match('/^[0-9]{8,12}$/D', $data['phone'])) {
    $errors[] = 'Phone number must contain 8-12 digits.';
}

// Validate submitted skills.
$allowedSkills = [
    'cybersecurity-fundamentals',
    'network-security',
    'incident-response',
    'siem',
    'web-security',
    'penetration-testing',
    'cloud-security',
    'linux',
    'scripting',
    'risk-management',
    'security-awareness',
    'technical-documentation'
];

$submittedSkills = $_POST['skills'] ?? [];

if (!is_array($submittedSkills)) {
    $errors[] = 'Invalid skills format.';
    $submittedSkills = [];
}

$submittedSkills = array_values(array_unique(
    array_filter(
        $submittedSkills,
        fn($skill) => is_string($skill)
    )
));

if (empty($submittedSkills)) {
    $errors[] = 'Please select at least one skill.';
}

foreach ($submittedSkills as $skill) {
    if (!in_array($skill, $allowedSkills, true)) {
        $errors[] = 'An invalid skill was selected.';
        break;
    }
}

// Preserve the required checkbox from Part 1.
if (!in_array(
    'cybersecurity-fundamentals',
    $submittedSkills,
    true
)) {
    $errors[] = 'Cybersecurity Fundamentals is required.';
}

$skillsJson = json_encode($submittedSkills);

if ($skillsJson === false) {
    $errors[] = 'Unable to process the selected skills.';
}

if (mb_strlen($data['other_skills'], 'UTF-8') > 5000) {
    $errors[] = 'Other skills must be under 5000 characters.';
}

$eoiNumber = null;
$databaseError = false;

// Only connect to the database if validation succeeds.
if (empty($errors)) {
    require_once __DIR__ . '/settings.php';

    try {
        mysqli_report(
            MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
        );

        $conn = new mysqli(
            $host,
            $user,
            $pwd,
            $sql_db
        );

        $conn->set_charset('utf8mb4');

        // Automatically create the EOI table if missing.
        $createTable = "
            CREATE TABLE IF NOT EXISTS eoi (
                EOInumber INT UNSIGNED AUTO_INCREMENT
                    PRIMARY KEY,
                jobref CHAR(6) NOT NULL,
                firstname VARCHAR(20) NOT NULL,
                lastname VARCHAR(20) NOT NULL,
                dob DATE NOT NULL,
                gender ENUM('female','male','other')
                    NOT NULL,
                street VARCHAR(40) NOT NULL,
                suburb VARCHAR(40) NOT NULL,
                state ENUM(
                    'VIC','NSW','QLD','NT',
                    'WA','SA','TAS','ACT'
                ) NOT NULL,
                postcode CHAR(4) NOT NULL,
                email VARCHAR(254) NOT NULL,
                phone VARCHAR(12) NOT NULL,
                skills TEXT NOT NULL,
                other_skills TEXT,
                status ENUM('New','Current','Final')
                    NOT NULL DEFAULT 'New',
                created_at TIMESTAMP
                    DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_jobref (jobref),
                INDEX idx_applicant (lastname,firstname)
            ) ENGINE=InnoDB
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci
        ";

        $conn->query($createTable);

        // Prepared statement protects against SQL injection.
        $sql = "
            INSERT INTO eoi (
                jobref, firstname, lastname, dob,
                gender, street, suburb, state,
                postcode, email, phone, skills,
                other_skills
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            'sssssssssssss',
            $data['jobref'],
            $data['firstname'],
            $data['lastname'],
            $dobForDatabase,
            $data['gender'],
            $data['street'],
            $data['suburb'],
            $data['state'],
            $data['postcode'],
            $data['email'],
            $data['phone'],
            $skillsJson,
            $data['other_skills']
        );

        $stmt->execute();

        // Retrieve automatically generated EOI number.
        $eoiNumber = $conn->insert_id;

        $stmt->close();
        $conn->close();

    } catch (mysqli_sql_exception $exception) {
        error_log(
            'FABWEB database error: ' .
            $exception->getMessage()
        );

        $databaseError = true;
    }
}

// Display response using existing PHP components.
require __DIR__ . '/includes/header.inc';
?>

<main id="main-content">
    <section class="section">
        <div class="container">

            <?php if ($eoiNumber !== null): ?>

                <h1>Application Submitted Successfully</h1>

                <p>Thank you,
                    <strong><?= escapeHtml($data['firstname']) ?></strong>.
                    Your application has been saved.
                </p>

                <h2>Your EOI Number:
                    <?= (int)$eoiNumber ?>
                </h2>

                <p>
                    Job Reference:
                    <?= escapeHtml($data['jobref']) ?>
                </p>

                <p>Status: <strong>New</strong></p>

                <p>
                    Please keep your EOI number
                    for future reference.
                </p>

                <a class="button" href="index.php">
                    Return to Home
                </a>

            <?php elseif ($databaseError): ?>

                <h1>Unable to Submit Application</h1>

                <p>
                    A database error occurred.
                    Please try again later.
                </p>

                <a href="apply.php">Back to Application</a>

            <?php else: ?>

                <h1>Application Validation Failed</h1>

                <p>Please correct the following errors:</p>

                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escapeHtml($error) ?></li>
                    <?php endforeach; ?>
                </ul>

                <a href="apply.php">Back to Application</a>

            <?php endif; ?>

        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.inc'; ?>
