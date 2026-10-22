
<?php
$pageTitle = 'Apply | FABWEB Cybersecurity Services';

$pageDescription = 'Apply for a cybersecurity role at FABWEB.';

$pageStyles = '
.form-shell h2 {
    border-left: 5px solid #1677FF;
    padding-left: 0.75rem;
}
';

require __DIR__ . '/includes/header.inc';
?>

<main id="main-content">

    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Career Application</p>
            <h1>Apply to Join FABWEB</h1>
            <p>
                Complete the form using the six-character
                reference from the position description.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container form-shell">

            <div class="form-intro">
                <h2>Application Form</h2>
                <p>
                    <span class="required-note">Required:</span>
                    All fields except Other Skills must be completed.
                </p>
            </div>

            <form class="application-form"
                  method="post"
                  action="process_eoi.php">

                <div class="form-grid">

                    <div class="form-group full">
                        <label for="jobref">
                            Job Reference Number *
                        </label>
                        <input type="text"
                               name="jobref"
                               id="jobref"
                               maxlength="6"
                               pattern="[A-Za-z0-9]{6}"
                               placeholder="e.g. FW1001"
                               required>
                        <p class="help-text">
                            Valid references: FW1001,
                            FW1002, FW1003, FW1004.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="firstname">
                            First Name *
                        </label>
                        <input type="text"
                               name="firstname"
                               id="firstname"
                               maxlength="20"
                               pattern="[A-Za-z]{1,20}"
                               autocomplete="given-name"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="lastname">
                            Last Name *
                        </label>
                        <input type="text"
                               name="lastname"
                               id="lastname"
                               maxlength="20"
                               pattern="[A-Za-z]{1,20}"
                               autocomplete="family-name"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="dob">
                            Date of Birth *
                        </label>
                        <input type="text"
                               name="dob"
                               id="dob"
                               placeholder="dd/mm/yyyy"
                               pattern="[0-9]{2}/[0-9]{2}/[0-9]{4}"
                               required>
                        <p class="help-text">
                            Format: dd/mm/yyyy
                        </p>
                    </div>

                    <fieldset class="form-group">
                        <legend>Gender *</legend>

                        <div class="radio-group">
                            <label class="choice">
                                <input type="radio"
                                       name="gender"
                                       value="female"
                                       required>
                                Female
                            </label>

                            <label class="choice">
                                <input type="radio"
                                       name="gender"
                                       value="male">
                                Male
                            </label>

                            <label class="choice">
                                <input type="radio"
                                       name="gender"
                                       value="other">
                                Another Identity
                            </label>
                        </div>
                    </fieldset>

                    <div class="form-group full">
                        <label for="street">
                            Street Address *
                        </label>
                        <input type="text"
                               name="street"
                               id="street"
                               maxlength="40"
                               autocomplete="street-address"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="suburb">
                            Suburb / Town *
                        </label>
                        <input type="text"
                               name="suburb"
                               id="suburb"
                               maxlength="40"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="state">
                            State / Territory *
                        </label>
                        <select name="state"
                                id="state"
                                required>
                            <option value="">
                                Select State
                            </option>
                            <option value="VIC">VIC</option>
                            <option value="NSW">NSW</option>
                            <option value="QLD">QLD</option>
                            <option value="NT">NT</option>
                            <option value="WA">WA</option>
                            <option value="SA">SA</option>
                            <option value="TAS">TAS</option>
                            <option value="ACT">ACT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="postcode">
                            Postcode *
                        </label>
                        <input type="text"
                               name="postcode"
                               id="postcode"
                               maxlength="4"
                               pattern="[0-9]{4}"
                               inputmode="numeric"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            Email Address *
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               autocomplete="email"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            Phone Number *
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               pattern="[0-9]{8,12}"
                               maxlength="12"
                               inputmode="numeric"
                               required>
                        <p class="help-text">
                            Enter 8-12 digits, without spaces.
                        </p>
                    </div>

                    <fieldset class="form-group full">
                        <legend>Skill List *</legend>

                        <p class="help-text">
                            Select your skills.
                            Cybersecurity Fundamentals is required.
                        </p>

                        <div class="checkbox-grid">

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="cybersecurity-fundamentals"
                                       required>
                                Cybersecurity Fundamentals
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="network-security">
                                Network Security
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="incident-response">
                                Incident Response
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="siem">
                                SIEM / Security Monitoring
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="web-security">
                                Web Security
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="penetration-testing">
                                Penetration Testing
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="cloud-security">
                                Cloud Security
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="linux">
                                Linux
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="scripting">
                                Python / Scripting
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="risk-management">
                                Risk Management
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="security-awareness">
                                Security Awareness
                            </label>

                            <label class="choice">
                                <input type="checkbox"
                                       name="skills[]"
                                       value="technical-documentation">
                                Technical Documentation
                            </label>

                        </div>
                    </fieldset>

                    <div class="form-group full">
                        <label for="other-skills">
                            Other Skills (Optional)
                        </label>

                        <textarea
                            name="other_skills"
                            id="other-skills"
                            maxlength="5000"
                            placeholder="Other relevant skills, certifications or experience."></textarea>
                    </div>

                </div>

                <div class="submit-row">
                    <input type="submit"
                           value="Submit Application">

                    <input type="reset"
                           value="Clear Form">
                </div>

            </form>
        </div>
    </section>

</main>

<?php require __DIR__ . '/includes/footer.inc'; ?>
