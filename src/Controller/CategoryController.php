<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class CategoryController
{
    public function __construct(
        private View $view,
        private CategoryRepository $categories,
        private ArticleRepository $articles,
        private array $appConfig,
    ) {
    }

    public function show(int $id): void
    {
        $category = $this->categories->find($id);
        if ($category === null) {
            http_response_code(404);
            $this->view->assign('title', '404 — категория не найдена')->display('404.tpl');
            return;
        }

        $allowedSorts = [ArticleRepository::SORT_DATE, ArticleRepository::SORT_VIEWS];
        $sort = (string) ($_GET['sort'] ?? ArticleRepository::SORT_DATE);
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = ArticleRepository::SORT_DATE;
        }

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = (int) $this->appConfig['articles_per_page'];

        $total      = $this->articles->countByCategory($id);
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $articles = $this->articles->paginatedByCategory($id, $sort, $page, $perPage);

        $this->view
            ->assign('title', $category['name'] . ' — Smarty Blog')
            ->assign('category', $category)
            ->assign('articles', $articles)
            ->assign('sort', $sort)
            ->assign('page', $page)
            ->assign('totalPages', $totalPages)
            ->assign('total', $total)
            ->display('category.tpl');
    }
}
