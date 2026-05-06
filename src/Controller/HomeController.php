<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class HomeController
{
    public function __construct(
        private View $view,
        private CategoryRepository $categories,
        private ArticleRepository $articles,
    ) {
    }

    public function index(): void
    {
        $blocks = [];
        foreach ($this->categories->withArticles() as $category) {
            $articles = $this->articles->latestByCategory((int) $category['id'], 3);
            if ($articles === []) {
                continue;
            }
            $category['articles'] = $articles;
            $blocks[] = $category;
        }

        $this->view
            ->assign('title', 'Smarty Blog — главная')
            ->assign('categories', $blocks)
            ->display('home.tpl');
    }
}
