<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class CategoryRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function all(): array
    {
        return $this->pdo
            ->query('SELECT * FROM categories ORDER BY name ASC')
            ->fetchAll();
    }

    public function withArticles(): array
    {
        $sql = 'SELECT c.*
                FROM categories c
                INNER JOIN article_categories ac ON ac.category_id = c.id
                GROUP BY c.id
                ORDER BY c.name ASC';

        return $this->pdo->query($sql)->fetchAll();
    }

    public function forArticle(int $articleId): array
    {
        $sql = 'SELECT c.*
                FROM categories c
                INNER JOIN article_categories ac ON ac.category_id = c.id
                WHERE ac.article_id = :id
                ORDER BY c.name ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $articleId]);

        return $stmt->fetchAll();
    }
}
