<?php

declare(strict_types=1);
require_once __DIR__ . '/includes/db.inc';
$openCount = (int) db()->query('SELECT COUNT(*) AS total FROM jobs WHERE is_open = 1')->fetch_assoc()['total'];
$pageTitle = 'FABWEB Cybersecurity Services | Home';
$activePage = 'index.php';
require __DIR__ . '/includes/header.inc';
?>
<main id="main-content">
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <p class="eyebrow">Melbourne cybersecurity careers</p>
        <h1>Secure the Web.<br><span>Protect What Matters.</span></h1>
        <p class="lead">Practical cybersecurity services that protect websites, networks, cloud platforms and digital operations.</p>
        <div class="actions"><a class="button" href="jobs.php">Explore open roles</a><a class="button button-outline" href="apply.php">Apply now</a></div>
        <div class="stats">
          <div><strong><?= $openCount ?></strong><span>Open positions</span></div>
          <div><strong>7</strong><span>Security services</span></div>
          <div><strong>Melbourne</strong><span>Hybrid opportunities</span></div>
        </div>
      </div>
      <img class="hero-image" src="images/hero-cybersecurity.svg" alt="Illustration of a secured digital network and laptop">
    </div>
  </section>
  <section class="section">
    <div class="container">
      <p class="eyebrow dark">About FABWEB</p>
      <h2>Cybersecurity built around people and trust</h2>
      <p class="section-intro">FABWEB Cybersecurity Services helps organisations build secure and resilient digital systems, with a focus on practical risk reduction.</p>
      <div class="cards">
        <article class="card">
          <h3>Mission</h3>
          <p>Make digital systems safer and easier to trust using responsible and accessible cybersecurity services.</p>
        </article>
        <article class="card">
          <h3>Vision</h3>
          <p>Organisations and communities confidently using technology without compromising privacy or security.</p>
        </article>
        <article class="card">
          <h3>Values</h3>
          <p>Integrity, collaboration, privacy, continuous learning and responsible security testing.</p>
        </article>
      </div>
    </div>
  </section>
  <section class="section alt">
    <div class="container">
      <p class="eyebrow dark">What we do</p>
      <h2>Core cybersecurity services</h2>
      <div class="cards service-grid">
        <?php foreach ([['Security operations', 'Monitoring and incident response'], ['Penetration testing', 'Authorised testing of networks and applications'], ['Cloud security', 'Secure cloud identities and configurations'], ['Risk and compliance', 'Security governance and practical risk reviews'], ['Web security', 'Security assessments for online services'], ['Awareness training', 'Help staff identify social engineering'], ['Incident response', 'Contain, investigate and recover from attacks']] as $service): ?>
          <article class="card">
            <h3><?= h($service[0]) ?></h3>
            <p><?= h($service[1]) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <section class="section">
    <div class="container careers-cta">
      <div>
        <p class="eyebrow dark">Careers</p>
        <h2>Build a safer digital future</h2>
        <p>Explore roles in security operations, penetration testing, cloud security and cybersecurity risk.</p>
      </div><a class="button" href="jobs.php">View careers</a>
    </div>
  </section>
  <section class="section alt">
    <div class="container">
      <h2>Acknowledgement of Country</h2>
      <p>FABWEB acknowledges the Wurundjeri Woi-wurrung people of the Kulin Nation as Traditional Owners of the land on which our Melbourne office is located and pays respect to Elders past and present.</p>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.inc'; ?>