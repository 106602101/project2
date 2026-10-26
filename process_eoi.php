<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';

// A direct browser visit (GET) cannot create or view an application.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('apply.php');
}
if (!verify_csrf()) {
    http_response_code(403);
    exit('Invalid form token. Return to the application page and try again.');
}

$fields = ['job_ref', 'first_name', 'last_name', 'date_of_birth', 'gender', 'street_address', 'suburb_town', 'state', 'postcode', 'email', 'phone', 'other_skills'];
$data = [];
foreach ($fields as $field) {
    $data[$field] = clean($_POST[$field] ?? '', 4000);
}
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
$submittedSkills = is_array($_POST['skills'] ?? null) ? $_POST['skills'] : [];
$selectedSkills = [];
foreach ($submittedSkills as $skill) {
    if (is_string($skill) && in_array($skill, $allowedSkills, true) && !in_array($skill, $selectedSkills, true)) {
        $selectedSkills[] = $skill;
    }
}
$errors = [];
if (!valid_job_ref($data['job_ref'])) {
    $errors[] = 'Choose a valid 5–6 character job reference.';
} else {
    $check = db()->prepare('SELECT job_ref FROM jobs WHERE job_ref = ? AND is_open = 1');
    $check->bind_param('s', $data['job_ref']);
    $check->execute();
    if (!$check->get_result()->fetch_assoc()) {
        $errors[] = 'The selected job reference is not open.';
    }
    $check->close();
}
foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $key => $label) {
    if (!preg_match("/^[\\p{L}][\\p{L} '\x{2019}-]{0,19}$/uD", $data[$key]) || text_length($data[$key]) > 20) {
        $errors[] = "$label must contain 1–20 letters and valid name punctuation.";
    }
}
$dob = $data['date_of_birth'];
$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dob);
if (!$date || $date->format('Y-m-d') !== $dob) {
    $errors[] = 'Enter a valid date of birth.';
} else {
    $today = new DateTimeImmutable('today');
    $age = $date->diff($today)->y;
    if ($date > $today || $age < 16 || $age > 100) {
        $errors[] = 'Applicant age must be between 16 and 100 years.';
    }
}
if (!in_array($data['gender'], ['Male', 'Female', 'Other', 'Prefer not to say'], true)) {
    $errors[] = 'Select a valid gender option.';
}
foreach (['street_address' => 40, 'suburb_town' => 40] as $key => $limit) {
    if ($data[$key] === '' || text_length($data[$key]) > $limit) {
        $errors[] = str_replace('_', ' ', ucfirst($key)) . " is required and must be at most $limit characters.";
    }
}
if (!in_array($data['state'], ['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'], true)) {
    $errors[] = 'Select a valid Australian state or territory.';
}
if (!preg_match('/^[0-9]{4}$/D', $data['postcode'])) {
    $errors[] = 'Postcode must contain exactly four digits.';
}
if (strlen($data['email']) > 254 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
}
if (!preg_match('/^[0-9 ]+$/D', $data['phone']) || !preg_match('/^[0-9]{8,12}$/D', str_replace(' ', '', $data['phone']))) {
    $errors[] = 'Phone number must contain 8–12 digits (spaces allowed).';
}
if (!$selectedSkills) {
    $errors[] = 'Choose at least one skill.';
}
if (!in_array('cybersecurity-fundamentals', $selectedSkills, true)) {
    $errors[] = 'Cybersecurity Fundamentals is required.';
}
if (text_length($data['other_skills']) > 1500) {
    $errors[] = 'Other skills must be 1500 characters or fewer.';
}
if ($errors) {
    $_SESSION['eoi_errors'] = $errors;
    $_SESSION['eoi_old'] = $data + ['skills' => $selectedSkills];
    redirect('apply.php');
}

// The assessment explicitly requires creation in PHP when eoi is absent.
// jobs must already exist (import fabweb_db.sql once to install the site).
db()->query("CREATE TABLE IF NOT EXISTS eoi (
 EOInumber INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 job_ref VARCHAR(6) NOT NULL,
 first_name VARCHAR(20) NOT NULL,
 last_name VARCHAR(20) NOT NULL,
 date_of_birth DATE NOT NULL,
 gender ENUM('Male','Female','Other','Prefer not to say') NOT NULL,
 street_address VARCHAR(40) NOT NULL,
 suburb_town VARCHAR(40) NOT NULL,
 state ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
 postcode CHAR(4) NOT NULL,
 email VARCHAR(254) NOT NULL,
 phone VARCHAR(20) NOT NULL,
 skills VARCHAR(500) NOT NULL,
 other_skills TEXT NULL,
 status ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_eoi_job (job_ref), INDEX idx_eoi_name (first_name,last_name),
 CONSTRAINT fk_eoi_job FOREIGN KEY (job_ref) REFERENCES jobs(job_ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$joinedSkills = implode(', ', $selectedSkills);
$stmt = db()->prepare('INSERT INTO eoi (job_ref,first_name,last_name,date_of_birth,gender,street_address,suburb_town,state,postcode,email,phone,skills,other_skills) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
$stmt->bind_param('sssssssssssss', $data['job_ref'], $data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'], $data['street_address'], $data['suburb_town'], $data['state'], $data['postcode'], $data['email'], $data['phone'], $joinedSkills, $data['other_skills']);
$stmt->execute();
$eoiNumber = db()->insert_id;
$stmt->close();
// Prevent an old tab from reusing the just-submitted CSRF token.
unset($_SESSION['csrf_token']);
$pageTitle = 'Application Submitted | FABWEB';
$activePage = 'apply.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
    <section class="section">
        <div class="container narrow">
            <div class="card success-card">
                <p class="eyebrow dark">Application received</p>
                <h1>Thank you, <?= h($data['first_name']) ?>.</h1>
                <p>Your expression of interest for <strong><?= h($data['job_ref']) ?></strong> has been saved successfully.</p>
                <p class="confirmation-number">EOI number: <strong><?= (int) $eoiNumber ?></strong></p>
                <p>Your initial application status is <strong>New</strong>. Keep this reference number for your records.</p>
                <a class="button" href="jobs.php">Back to open jobs</a>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>