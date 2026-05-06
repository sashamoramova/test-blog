<article class="article-card">
    <a href="/article/{$article.id}" class="article-card__media">
        {if $article.image}
            <img src="{$article.image|escape}" alt="{$article.title|escape}" loading="lazy">
        {else}
            <span class="article-card__placeholder">Без обложки</span>
        {/if}
    </a>
    <div class="article-card__body">
        <h3 class="article-card__title">
            <a href="/article/{$article.id}">{$article.title|escape}</a>
        </h3>
        {if $article.description}
            <p class="article-card__desc">{$article.description|escape|truncate:140}</p>
        {/if}
        <p class="article-card__meta">
            <time datetime="{$article.published_at}">{$article.published_at|date_format:'%d.%m.%Y'}</time>
            <span class="dot">·</span>
            <span>{$article.views} просмотр.</span>
        </p>
    </div>
</article>
