<?php
$pageTitle = 'About Our Team | FABWEB';
require __DIR__ . '/includes/header.inc';
?>

<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">COS10026 project team</p>
            <h1>About our team</h1>
            <p>This page documents the student team responsible for designing and developing the FABWEB recruitment website.</p>
        </div>
    </section>

    <!-- Group details section -->
    <section class="section">
        <div class="container about-grid">
            <section class="card team-summary" aria-labelledby="group-details-title">
                <h2 id="group-details-title">Group details</h2>
                <ul>
                    <li>Group name: FABWEB Project Team
                        <ul>
                            <li>Tutorial day: <strong>Thursday</strong></li>
                            <li>Tutorial time: <strong>04:30 PM</strong></li>
                            <li>Allocated industry: G01 – Cybersecurity Services Company</li>
                        </ul>
                    </li>
                </ul>
            </section>

            <figure class="team-figure">
                <img src="assets/teamphoto.jpg" alt="Image of the FABWEB team members">
                <figcaption>Team FABWEB</figcaption>
            </figure>
        </div>
    </section>

    <!-- Contributions section -->
    <section class="section alt" aria-labelledby="contributions-title">
        <div class="container">
            <h2 id="contributions-title">Member contributions</h2>
            <dl class="definition-list">

                <dt>Ravindu Shasimal Karunasinghe</dt>
                <dd>Student ID <span class="student-id">1005541025</span> — <strong>Created GitHub repository, home page and apply page.</strong></dd>

                <dt>Heshan Gimhana Naotunna Palliya Guruge</dt>
                <dd>Student ID <span class="student-id">106378923</span> — <strong>Created WhatsApp group and the jobs page.</strong></dd>

                <dt>Ethan Purvis</dt>
                <dd>Student ID <span class="student-id">106602101</span> — <strong>Proof-read repository and created the about page.</strong></dd>

            </dl>
        </div>
    </section>

    <!-- Quotes section -->
    <section class="section" aria-labelledby="quotes-title">
        <div class="container">
            <h2 id="quotes-title">Team quotes</h2>

            <blockquote>
                <p>“සිහින දකින්න, ඒවා සැබෑ කරගන්න.”</p>
                <p><strong>English:</strong>“Dream, and make those dreams come true.”</p>
                <footer>— Rav</footer>
            </blockquote>
            <blockquote>
                <p>“උත්සාහය අත් නොහරින්න.”</p>
                <p><strong>English:</strong> “Never give up.”</p>
                <footer>— Heshan</footer>
            </blockquote>
            <blockquote>
                <p>"In vino veritas"</p>
                <p><strong>English:</strong> "In wine, there is truth."</p>
                <footer>— Ethan</footer>
            </blockquote>
        </div>
    </section>

    <!-- Facts section -->
    <section class="section alt" aria-labelledby="facts-title">
        <div class="container">
            <h2 id="facts-title">Student facts</h2>
            <div class="table-wrap">
                <table>
                    <caption>FABWEB project team facts</caption>
                    <thead>
                        <tr>
                            <th scope="col">Member</th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Study interest</th>
                            <th scope="col">Project strength</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <th scope="row">Ravindu Shasimal Karunasinghe</th>
                            <td><span class="student-id">1005541025</span></td>
                            <td>Data Science</td>
                            <td>Teamwork and communication</td>
                        </tr>

                        <tr>
                            <th scope="row">Heshan Gimhana Naotunna Palliya Guruge</th>
                            <td><span class="student-id">1005541025</span></td>
                            <td>Data Science</td>
                            <td>Willingness to learn new technolgies</td>
                        </tr>

                        <tr>
                            <th scope="row">Ethan Purvis</th>
                            <td><span class="student-id">106602101</span></td>
                            <td>Cyber Security</td>
                            <td>Ability to keep code tidy and readable.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>


<?php require __DIR__ . '/includes/footer.inc'; ?>