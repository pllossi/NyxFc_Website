<?php

declare(strict_types=1);

function nyx_get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0775, true);
    }

    $pdo = new PDO('sqlite:' . $dataDir . '/nyxfc.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    nyx_initialize_db($pdo);

    return $pdo;
}

function nyx_initialize_db(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS matchdays (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            match_date TEXT NOT NULL,
            match_time TEXT NOT NULL,
            opponent TEXT NOT NULL,
            location TEXT,
            notes TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM matchdays')->fetchColumn();
    if ($count === 0) {
        $seed = $pdo->prepare(
            'INSERT INTO matchdays (match_date, match_time, opponent, location, notes)
             VALUES (:match_date, :match_time, :opponent, :location, :notes)'
        );

        $defaults = [
            ['match_date' => '2026-11-10', 'match_time' => '18:30', 'opponent' => 'Thunder 7', 'location' => 'Centro Sportivo Aurora', 'notes' => 'Campionato - Giornata 4'],
            ['match_date' => '2026-11-13', 'match_time' => '21:00', 'opponent' => 'Atletico Nord', 'location' => 'Arena Nord', 'notes' => 'Recupero serale'],
            ['match_date' => '2026-11-16', 'match_time' => '16:00', 'opponent' => 'Real City', 'location' => 'Campo Comunale Ovest', 'notes' => 'Campionato - Giornata 5'],
        ];

        foreach ($defaults as $row) {
            $seed->execute($row);
        }
    }
}

function nyx_fetch_matchdays(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, match_date, match_time, opponent, location, notes FROM matchdays ORDER BY match_date ASC, match_time ASC, id ASC');

    return $stmt->fetchAll();
}

function nyx_italian_day(string $date): string
{
    $days = [
        'Monday' => 'Lunedì',
        'Tuesday' => 'Martedì',
        'Wednesday' => 'Mercoledì',
        'Thursday' => 'Giovedì',
        'Friday' => 'Venerdì',
        'Saturday' => 'Sabato',
        'Sunday' => 'Domenica',
    ];

    $dayKey = (new DateTimeImmutable($date))->format('l');

    return $days[$dayKey] ?? $dayKey;
}
