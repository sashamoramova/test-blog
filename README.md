## Стек

- **Backend**: PHP 8.1 (FPM), PDO
- **Templates**: Smarty 4
- **БД**: MySQL 8
- **Web**: Nginx
- **Стили**: SCSS (Dart Sass через node-контейнер)
- **Окружение**: Docker Compose

## Запуск

### 1) Клонирование, переход в проект, создание .env

```bash
HTTPS:
git clone https://github.com/sashamoramova/test-blog.git
или SSH:
git clone git@github.com:sashamoramova/test-blog.git

cd test-blog
cp .env.example .env
```

### 2) Поднять контейнеры

```bash
docker compose up -d --build
```

### 3) Установить PHP-зависимости (Smarty)

```bash
docker compose exec php composer install
```

### 4) Применить миграцию БД

```bash
docker compose exec -T mysql mysql -h127.0.0.1 -uroot -proot blog < migrations/001_init.sql
```

### 5) Засеять тестовые данные

```bash
docker compose exec php php seeds/seed.php
```

## Либо запуск одной командой

Если в проекте есть скрипты:

- Linux/macOS:
  ```bash
  ./setup.sh
  ```
- Windows PowerShell:
  ```powershell
  .\setup.ps1
  ```


## Адреса 

- Главная:       http://localhost:3000/
- Категория:     http://localhost:3000/category/1
- Статья:        http://localhost:3000/article/1
- 404:           http://localhost:3000/unknown


## Структура проекта

```text
test-blog/
├── config/
│   └── config.php
├── docker/
│   ├── nginx/default.conf
│   ├── php/Dockerfile
│   └── node/Dockerfile
├── migrations/
│   └── 001_init.sql
├── public/
│   ├── index.php
│   ├── css/                 # compiled css
│   └── uploads/             # картинки, заполняются сидером
├── scss/
│   ├── main.scss
│   ├── _variables.scss
│   ├── _layout.scss
│   ├── _components.scss
│   ├── _article.scss
│   └── _responsive.scss
├── seeds/
│   ├── seed.php
│   ├── data/
│   │   ├── categories.php
│   │   └── articles.php
│   └── images/
├── src/
│   ├── bootstrap.php
│   ├── Core/
│   │   ├── Database.php
│   │   ├── Router.php
│   │   └── View.php
│   ├── Repository/
│   │   ├── CategoryRepository.php
│   │   └── ArticleRepository.php
│   └── Controller/
│       ├── HomeController.php
│       ├── CategoryController.php
│       └── ArticleController.php
├── templates/
│   ├── layout.tpl
│   ├── home.tpl
│   ├── category.tpl
│   ├── article.tpl
│   ├── 404.tpl
│   └── partials/
│       ├── header.tpl
│       ├── footer.tpl
│       ├── article-card.tpl
│       └── pagination.tpl
├── docker-compose.yml
├── .env.example
├── composer.json
└── README.md
```

