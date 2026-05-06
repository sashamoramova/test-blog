{extends file="layout.tpl"}

{block name="content"}
<article class="article">
    {if $categories}
        <div class="article__categories">
            {foreach $categories as $cat}
                <a class="badge" href="/category/{$cat.id}">{$cat.name|escape}</a>
            {/foreach}
        </div>
    {/if}

    <h1 class="article__title">{$article.title|escape}</h1>

    <p class="article__meta">
        <time datetime="{$article.published_at}">{$article.published_at|date_format:'%d.%m.%Y'}</time>
        <span class="dot">·</span>
        <span>{$article.views} просмотр.</span>
    </p>

    {if $article.image}
        <figure class="article__figure">
            <img src="{$article.image|escape}" alt="{$article.title|escape}">
        </figure>
    {/if}

    {if $article.description}
        <p class="article__lead">{$article.description|escape}</p>
    {/if}

    <div class="article__body">
        {$article.body|escape|nl2br nofilter}
    </div>
</article>

{if $similar}
<aside class="similar">
    <h2 class="similar__title">Похожие статьи</h2>
    <div class="article-grid">
        {foreach $similar as $article}
            {include file="partials/article-card.tpl" article=$article}
        {/foreach}
    </div>
</aside>
{/if}
{/block}
