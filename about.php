<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
$sections = db()->query('SELECT heading, body FROM about_sections ORDER BY display_order, id')->fetch_all(MYSQLI_ASSOC);
$members = db()->query('SELECT member_name, student_id, part1_contribution, part2_contribution FROM team_members ORDER BY display_order, id')->fetch_all(MYSQLI_ASSOC);
$pageTitle = 'About FABWEB and Our Team';
$activePage = 'about.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
    <section class="page-banner">
        <div class="container">
            <p class="eyebrow">Our story</p>
            <h1>About FABWEB and our team</h1>
            <p>Company details and team contributions are retrieved from our database.</p>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="cards">
                <?php foreach ($sections as $section): ?><article class="card">
                        <h2><?= h($section['heading']) ?></h2>
                        <p><?= nl2br(h($section['body'])) ?></p>
                    </article><?php endforeach; ?>
            </div>
        </div>
    </section>
    <section class="section alt">
        <div class="container">
            <p class="eyebrow dark">Project contributions</p>
            <h2>Our project team</h2>
            <p class="section-intro">Contributions cover both Applied Web Project Part 1 and Part 2.</p>
            <div class="cards"><?php foreach ($members as $member): ?><article class="card member">
                        <h3><?= h($member['member_name']) ?></h3>
                        <p class="muted">Student ID: <?= h($member['student_id']) ?></p>
                        <h4>Project 1 contribution</h4>
                        <p><?= nl2br(h($member['part1_contribution'])) ?></p>
                        <h4>Project 2 contribution</h4>
                        <p><?= nl2br(h($member['part2_contribution'])) ?></p>
                    </article><?php endforeach; ?></div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>