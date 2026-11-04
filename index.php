<?php

declare(strict_types=1);

require __DIR__ . '/db.php';

$matchdays = nyx_fetch_matchdays(nyx_get_pdo());
$teamValues = [
    ['title' => 'Spirito di squadra', 'description' => 'Ogni azione nasce dalla fiducia tra compagni, dentro e fuori dal campo.'],
    ['title' => 'Intensità', 'description' => 'Pressing alto, ritmo continuo e voglia di conquistare ogni pallone.'],
    ['title' => 'Crescita', 'description' => 'Allenamento, disciplina e miglioramento costante partita dopo partita.'],
];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nyx FC | Calcio a 7</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="hero" id="top">
        <nav class="nav">
            <a class="logo" href="#top">NYX FC</a>
            <div class="nav-links">
                <a href="#squadra">Squadra</a>
                <a href="#partite">Partite</a>
                <a href="#contatti">Contatti</a>
                <a href="/admin.php">Admin</a>
            </div>
        </nav>
        <div class="hero-content">
            <p class="tag">Squadra di calcio a 7</p>
            <h1>Benvenuti nel mondo Nyx FC</h1>
            <p class="lead">Passione, velocità e gioco di squadra: scopri il nostro progetto e seguici nelle prossime sfide.</p>
            <a class="cta" href="#partite">Prossime partite</a>
        </div>
    </header>

    <main>
        <section class="section" id="squadra">
            <h2>Chi siamo</h2>
            <p>Nyx FC è una squadra ambiziosa di calcio a 7, costruita su impegno, divertimento e voglia di crescere insieme.</p>
            <div class="cards">
                <?php foreach ($teamValues as $value): ?>
                    <article class="card reveal">
                        <h3><?= htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?= htmlspecialchars($value['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section section-dark" id="partite">
            <h2>Calendario</h2>
            <div class="matches">
                <?php foreach ($matchdays as $match): ?>
                    <article class="match reveal">
                        <p class="match-day"><?= htmlspecialchars(nyx_italian_day($match['match_date']), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><?= htmlspecialchars($match['match_date'], ENT_QUOTES, 'UTF-8'); ?> · Ore <?= htmlspecialchars($match['match_time'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <h3>Nyx FC vs <?= htmlspecialchars($match['opponent'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <?php if (!empty($match['location'])): ?>
                            <p>Campo: <?= htmlspecialchars((string) $match['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($match['notes'])): ?>
                            <p><?= htmlspecialchars((string) $match['notes'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="contatti">
            <h2>Contatti</h2>
            <p>Vuoi giocare con noi o organizzare un'amichevole? Scrivici e ti risponderemo al più presto.</p>
            <a class="mail" href="mailto:info@nyxfc.it">info@nyxfc.it</a>
        </section>
    </main>

    <footer class="footer">
        <p>© <?= date('Y'); ?> Nyx FC · Tutti i diritti riservati.</p>
    </footer>

    <script src="/assets/js/main.js" defer></script>
</body>
</html>
