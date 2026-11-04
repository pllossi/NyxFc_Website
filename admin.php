<?php

declare(strict_types=1);

require __DIR__ . '/db.php';

$pdo = nyx_get_pdo();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $matchDate = trim((string) ($_POST['match_date'] ?? ''));
        $matchTime = trim((string) ($_POST['match_time'] ?? ''));
        $opponent = trim((string) ($_POST['opponent'] ?? ''));
        $location = trim((string) ($_POST['location'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));

        if ($matchDate !== '' && $matchTime !== '' && $opponent !== '') {
            $stmt = $pdo->prepare(
                'INSERT INTO matchdays (match_date, match_time, opponent, location, notes)
                 VALUES (:match_date, :match_time, :opponent, :location, :notes)'
            );

            $stmt->execute([
                'match_date' => $matchDate,
                'match_time' => $matchTime,
                'opponent' => $opponent,
                'location' => $location !== '' ? $location : null,
                'notes' => $notes !== '' ? $notes : null,
            ]);

            header('Location: /admin.php?saved=1');
            exit;
        }

        $error = 'Compila almeno data, ora e avversario.';
    }

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM matchdays WHERE id = :id');
            $stmt->execute(['id' => $id]);
        }

        header('Location: /admin.php?deleted=1');
        exit;
    }
}

$matchdays = nyx_fetch_matchdays($pdo);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Nyx FC</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="section admin-wrap">
        <a class="back-link" href="/">← Torna al sito</a>
        <h1>Admin · Gestione giornate</h1>

        <?php if (isset($_GET['saved'])): ?>
            <p class="notice">Giornata salvata.</p>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <p class="notice">Giornata eliminata.</p>
        <?php endif; ?>

        <?php if ($error !== null): ?>
            <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <section class="admin-panel">
            <h2>Aggiungi giornata</h2>
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="add">
                <label>Data
                    <input type="date" name="match_date" required>
                </label>
                <label>Ora
                    <input type="time" name="match_time" required>
                </label>
                <label>Avversario
                    <input type="text" name="opponent" required placeholder="Es. Thunder 7">
                </label>
                <label>Campo
                    <input type="text" name="location" placeholder="Es. Centro Sportivo Aurora">
                </label>
                <label>Note
                    <input type="text" name="notes" placeholder="Es. Campionato - Giornata 6">
                </label>
                <button type="submit" class="btn">Salva</button>
            </form>
        </section>

        <section class="admin-panel">
            <h2>Giornate registrate</h2>
            <?php if ($matchdays === []): ?>
                <p>Nessuna giornata presente.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Ora</th>
                                <th>Avversario</th>
                                <th>Campo</th>
                                <th>Note</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($matchdays as $match): ?>
                            <tr>
                                <td><?= htmlspecialchars($match['match_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($match['match_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($match['opponent'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars((string) $match['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars((string) $match['notes'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $match['id']; ?>">
                                        <button class="btn btn-danger" type="submit">Elimina</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
