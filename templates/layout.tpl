<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{$description|default:'Простой блог на чистом PHP, MySQL и Smarty.'|escape}">
    <meta name="theme-color" content="#2563eb">
    <title>{$title|default:'Smarty Blog'|escape}</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
{include file="partials/header.tpl"}

<main class="container main">
    {block name="content"}{/block}
</main>

{include file="partials/footer.tpl"}
</body>
</html>
