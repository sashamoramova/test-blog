{if $totalPages > 1}
<nav class="pagination" aria-label="Постраничная навигация">
    {if $page > 1}
        <a class="pagination__link" href="{$baseUrl}?sort={$sort}&amp;page={$page-1}">‹ Назад</a>
    {else}
        <span class="pagination__link pagination__link--disabled">‹ Назад</span>
    {/if}

    {for $p=1 to $totalPages}
        {if $p === $page}
            <span class="pagination__current" aria-current="page">{$p}</span>
        {else}
            <a class="pagination__link" href="{$baseUrl}?sort={$sort}&amp;page={$p}">{$p}</a>
        {/if}
    {/for}

    {if $page < $totalPages}
        <a class="pagination__link" href="{$baseUrl}?sort={$sort}&amp;page={$page+1}">Вперёд ›</a>
    {else}
        <span class="pagination__link pagination__link--disabled">Вперёд ›</span>
    {/if}
</nav>
{/if}
