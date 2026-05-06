<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class ArticleRepository
{
    public const SORT_DATE  = 'date';
    public const SORT_VIEWS = 'views';

    public function __construct(private PDO $pdo)
    {
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function latestByCategory(int $categoryId, int $limit = 3): array
    {
        $sql = 'SELECT a.*
                FROM articles a
                INNER JOIN article_categories ac ON ac.article_id = a.id
                WHERE ac.category_id = :cid
                ORDER BY a.published_at DESC
                LIMIT ' . max(1, $limit);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $categoryId]);

        return $stmt->fetchAll();
    }

    public function paginatedByCategory(int $categoryId, string $sort, int $page, int $perPage): array
    {
        $orderBy = $sort === self::SORT_VIEWS
            ? 'a.views DESC, a.published_at DESC'
            : 'a.published_at DESC';

        $perPage = max(1, $perPage);
        $offset  = max(0, ($page - 1) * $perPage);

        $sql = "SELECT a.*
                FROM articles a
                INNER JOIN article_categories ac ON ac.article_id = a.id
                WHERE ac.category_id = :cid
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $categoryId]);

        return $stmt->fetchAll();
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM article_categories WHERE category_id = :cid'
        );
        $stmt->execute(['cid' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public function incrementViews(int $articleId): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $articleId]);
    }

    public function similar(int $articleId, int $limit = 3): array
    {
        // Native prepares require each placeholder to have a unique name even
        // when bound to the same value, hence :aid_filter and :aid_exclude.
        $sql = 'SELECT a.*, COUNT(ac.category_id) AS shared_categories
                FROM articles a
                INNER JOIN article_categories ac ON ac.article_id = a.id
                WHERE ac.category_id IN (
                    SELECT category_id FROM article_categories WHERE article_id = :aid_filter
                )
                AND a.id <> :aid_exclude
                GROUP BY a.id
                ORDER BY shared_categories DESC, a.published_at DESC
                LIMIT ' . max(1, $limit);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'aid_filter'  => $articleId,
            'aid_exclude' => $articleId,
        ]);

        return $stmt->fetchAll();
    }
}
