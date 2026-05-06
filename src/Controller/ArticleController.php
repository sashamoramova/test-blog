<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

class ArticleController
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
        $article = $this->articles->find($id);
        if ($article === null) {
            http_response_code(404);
            $this->view->assign('title', '404 — статья не найдена')->display('404.tpl');
            return;
        }

        $this->articles->incrementViews($id);
        $article['views'] = (int) $article['views'] + 1;

        $this->view
            ->assign('title', $article['title'] . ' — Smarty Blog')
            ->assign('article', $article)
            ->assign('categories', $this->categories->forArticle($id))
            ->assign('similar', $this->articles->similar($id, (int) $this->appConfig['similar_articles']))
            ->display('article.tpl');
    }
}
