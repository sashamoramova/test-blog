{extends file="layout.tpl"}

{block name="content"}
<h1 class="page-title">Свежие статьи</h1>

{if !$categories}
    <p class="empty-state">Пока нет ни одной статьи. Запустите сидинг: <code>php seeds/seed.php</code>.</p>
{/if}

{foreach $categories as $category}
<section class="category-block">
    <header class="category-block__header">
        <h2 class="category-block__title">
            <a href="/category/{$category.id}">{$category.name|escape}</a>
        </h2>
        {if $category.description}
            <p class="category-block__desc">{$category.description|escape}</p>
        {/if}
    </header>

    <div class="article-grid">
        {foreach $category.articles as $article}
            {include file="partials/article-card.tpl" article=$article}
        {/foreach}
    </div>

    <a class="button button--secondary" href="/category/{$category.id}">Все статьи</a>
</section>
{/foreach}
{/block}
