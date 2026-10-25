<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
$jobs = db()->query('SELECT job_ref,title FROM jobs WHERE is_open = 1 ORDER BY job_ref')->fetch_all(MYSQLI_ASSOC);
$old = $_SESSION['eoi_old'] ?? [];
$errors = $_SESSION['eoi_errors'] ?? [];
unset($_SESSION['eoi_old'], $_SESSION['eoi_errors']);
$selectedRef = clean($old['job_ref'] ?? ($_GET['job_ref'] ?? ''), 6);
function oldv(array $old, string $name): string
{
    return h((string) ($old[$name] ?? ''));
}
$skills = [
    'cybersecurity-fundamentals' => 'Cybersecurity Fundamentals',
    'network-security' => 'Network Security',
    'incident-response' => 'Incident Response',
    'siem' => 'SIEM / Security Monitoring',
    'web-security' => 'Web Security',
    'penetration-testing' => 'Penetration Testing',
    'cloud-security' => 'Cloud Security',
    'linux' => 'Linux',
    'scripting' => 'Python / Scripting',
    'risk-management' => 'Risk Management',
    'security-awareness' => 'Security Awareness',
    'technical-documentation' => 'Technical Documentation',
];
$chosenSkills = is_array($old['skills'] ?? null) ? $old['skills'] : [];
$pageTitle = 'Apply for a Position | FABWEB';
$activePage = 'apply.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Careers</p>
            <h1>Expression of Interest</h1>
            <p>All fields marked * are required. We verify every field on the server, even when browser validation is turned off.</p>
        </div>
    </section>
    <section class="section">
        <div class="container form-shell">
            <?php if ($errors): ?><div class="notice error" role="alert"><strong>Please correct the following:</strong>
                    <ul><?php foreach ($errors as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
                </div><?php endif; ?>
            <form method="post" action="process_eoi.php" novalidate>
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <fieldset>
                    <legend>Position</legend>
                    <div class="field"><label for="job_ref">Job reference *</label><select id="job_ref" name="job_ref">
                            <option value="">Choose a job</option><?php foreach ($jobs as $job): ?><option value="<?= h($job['job_ref']) ?>" <?= $selectedRef === $job['job_ref'] ? 'selected' : '' ?>><?= h($job['job_ref']) ?> — <?= h($job['title']) ?></option><?php endforeach; ?>
                        </select></div>
                </fieldset>
                <fieldset>
                    <legend>Personal details</legend>
                    <div class="form-grid">
                        <div class="field"><label for="first_name">First name *</label><input id="first_name" name="first_name" type="text" maxlength="20" value="<?= oldv($old, 'first_name') ?>"></div>
                        <div class="field"><label for="last_name">Last name *</label><input id="last_name" name="last_name" type="text" maxlength="20" value="<?= oldv($old, 'last_name') ?>"></div>
                        <div class="field"><label for="date_of_birth">Date of birth *</label><input id="date_of_birth" name="date_of_birth" type="date" value="<?= oldv($old, 'date_of_birth') ?>"></div>
                        <div class="field"><label for="gender">Gender *</label><select id="gender" name="gender">
                                <option value="">Select</option><?php foreach (['Male', 'Female', 'Other', 'Prefer not to say'] as $g): ?><option value="<?= h($g) ?>" <?= ($old['gender'] ?? '') === $g ? 'selected' : '' ?>><?= h($g) ?></option><?php endforeach; ?>
                            </select></div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Contact and address</legend>
                    <div class="form-grid">
                        <div class="field span2"><label for="street_address">Street address *</label><input id="street_address" name="street_address" type="text" maxlength="40" value="<?= oldv($old, 'street_address') ?>"></div>
                        <div class="field"><label for="suburb_town">Suburb / town *</label><input id="suburb_town" name="suburb_town" type="text" maxlength="40" value="<?= oldv($old, 'suburb_town') ?>"></div>
                        <div class="field"><label for="state">State / territory *</label><select id="state" name="state">
                                <option value="">Select</option><?php foreach (['VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT'] as $s): ?><option value="<?= h($s) ?>" <?= ($old['state'] ?? '') === $s ? 'selected' : '' ?>><?= h($s) ?></option><?php endforeach; ?>
                            </select></div>
                        <div class="field"><label for="postcode">Postcode (4 digits) *</label><input id="postcode" name="postcode" type="text" inputmode="numeric" maxlength="4" value="<?= oldv($old, 'postcode') ?>"></div>
                        <div class="field"><label for="email">Email address *</label><input id="email" name="email" type="email" maxlength="254" value="<?= oldv($old, 'email') ?>"></div>
                        <div class="field"><label for="phone">Phone (8–12 digits) *</label><input id="phone" name="phone" type="tel" maxlength="20" value="<?= oldv($old, 'phone') ?>"></div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Skills and experience</legend>
                    <p>Cybersecurity Fundamentals is required. Select any additional skills that apply. *</p>
                    <div class="checkbox-grid"><?php foreach ($skills as $value => $label): ?><label class="check"><input type="checkbox" name="skills[]" value="<?= h($value) ?>" <?= in_array($value, $chosenSkills, true) ? 'checked' : '' ?>><?= h($label) ?></label><?php endforeach; ?></div>
                    <div class="field"><label for="other_skills">Other skills (optional)</label><textarea id="other_skills" name="other_skills" maxlength="1500" rows="5"><?= oldv($old, 'other_skills') ?></textarea></div>
                </fieldset>
                <button class="button" type="submit">Submit expression of interest</button> <a class="text-link" href="jobs.php">Back to jobs</a>
            </form>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>