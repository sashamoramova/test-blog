<?php

declare(strict_types=1);

use App\Core\Database;

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line.\n");
    exit(1);
}

require dirname(__DIR__) . '/vendor/autoload.php';
$config = require dirname(__DIR__) . '/config/config.php';

$pdo = Database::connection($config['db']);

// Truncate in FK-safe order.
$pdo->query('SET FOREIGN_KEY_CHECKS = 0');
$pdo->query('TRUNCATE article_categories');
$pdo->query('TRUNCATE articles');
$pdo->query('TRUNCATE categories');
$pdo->query('SET FOREIGN_KEY_CHECKS = 1');
echo "Tables truncated.\n";

// Insert categories.
$categoriesData = require __DIR__ . '/data/categories.php';
$catStmt = $pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');
$catIdByKey = [];
foreach ($categoriesData as $key => $row) {
    $catStmt->execute(['name' => $row['name'], 'description' => $row['description']]);
    $catIdByKey[$key] = (int) $pdo->lastInsertId();
}
echo count($catIdByKey) . " categories inserted.\n";

// Copy placeholder images into public/uploads so they are reachable from the browser.
$srcImagesDir = __DIR__ . '/images';
$dstImagesDir = dirname(__DIR__) . '/public/uploads';
if (!is_dir($dstImagesDir)) {
    mkdir($dstImagesDir, 0775, true);
}
$copied = 0;
foreach (glob($srcImagesDir . '/*') as $file) {
    if (is_file($file)) {
        copy($file, $dstImagesDir . '/' . basename($file));
        $copied++;
    }
}
echo "$copied placeholder images copied to public/uploads.\n";

// Insert articles and link them to categories.
$articlesData = require __DIR__ . '/data/articles.php';
$articleStmt = $pdo->prepare(
    'INSERT INTO articles (title, description, body, image, views, published_at)
     VALUES (:title, :description, :body, :image, :views, :published_at)'
);
$linkStmt = $pdo->prepare(
    'INSERT INTO article_categories (article_id, category_id) VALUES (:aid, :cid)'
);

$inserted = 0;
foreach ($articlesData as $row) {
    $articleStmt->execute([
        'title'        => $row['title'],
        'description'  => $row['description'],
        'body'         => $row['body'],
        'image'        => $row['image'] ?? null,
        'views'        => $row['views'] ?? random_int(0, 500),
        'published_at' => $row['published_at'],
    ]);
    $aid = (int) $pdo->lastInsertId();
    foreach ($row['categories'] as $catKey) {
        if (!isset($catIdByKey[$catKey])) {
            continue;
        }
        $linkStmt->execute(['aid' => $aid, 'cid' => $catIdByKey[$catKey]]);
    }
    $inserted++;
}
echo "$inserted articles inserted.\n";

echo "Done.\n";
