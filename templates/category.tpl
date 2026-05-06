{extends file="layout.tpl"}

{block name="content"}
<header class="category-header">
    <a class="back-link" href="/">← На главную</a>
    <h1 class="page-title">{$category.name|escape}</h1>
    {if $category.description}
        <p class="category-header__desc">{$category.description|escape}</p>
    {/if}
    <p class="category-header__meta">Всего статей: {$total}</p>
</header>

<form class="sort-bar" method="get" action="/category/{$category.id}">
    <label for="sort">Сортировать:</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
        <option value="date"  {if $sort === 'date'}selected{/if}>по дате публикации</option>
        <option value="views" {if $sort === 'views'}selected{/if}>по количеству просмотров</option>
    </select>
    <noscript><button type="submit">Применить</button></noscript>
</form>

{if $articles}
    <div class="article-grid article-grid--list">
        {foreach $articles as $article}
            {include file="partials/article-card.tpl" article=$article}
        {/foreach}
    </div>

    {include file="partials/pagination.tpl"
        page=$page
        totalPages=$totalPages
        baseUrl="/category/`$category.id`"
        sort=$sort}
{else}
    <p class="empty-state">В этой категории пока нет статей.</p>
{/if}
{/block}
