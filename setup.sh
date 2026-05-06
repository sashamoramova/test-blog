# для запуска на macOS / Linux

set -euo pipefail

cd "$(dirname "$0")"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

step()  { printf "\n${GREEN}==>${NC} %s\n" "$*"; }
warn()  { printf "${YELLOW}!! %s${NC}\n" "$*"; }
fail()  { printf "${RED}xx %s${NC}\n" "$*"; exit 1; }

# 0. Prereq checks
step "Проверяю Docker..."
command -v docker >/dev/null  || fail "Docker не установлен. Поставьте Docker Desktop или docker-ce."
docker compose version >/dev/null 2>&1 || fail "Docker Compose v2 не найден."

# 1. .env
if [ ! -f .env ]; then
    step "Создаю .env из .env.example"
    cp .env.example .env
else
    warn ".env уже существует, оставляю как есть."
fi

# 2. Up
step "Запускаю контейнеры (docker compose up -d --build)..."
docker compose up -d --build

# 3. Wait MySQL
step "Жду готовности MySQL..."
ready=0
for i in $(seq 1 60); do
    if docker compose exec -T mysql mysql -h127.0.0.1 -uroot -proot -e "SELECT 1" >/dev/null 2>&1; then
        ready=1
        break
    fi
    printf "."
    sleep 2
done
echo
[ "$ready" = "1" ] || fail "MySQL не успел подняться за 120 секунд. Проверьте: docker compose logs mysql"

# 4. Migration
step "Применяю миграцию БД..."
docker compose exec -T mysql mysql -h127.0.0.1 -uroot -proot blog < migrations/001_init.sql

# 5. Composer install
step "Устанавливаю PHP-зависимости (composer install)..."
docker compose exec -T php composer install --no-interaction --quiet

# 6. Seed
step "Заполняю тестовыми данными (5 категорий, 30 статей, 30 обложек)..."
docker compose exec -T php php seeds/seed.php

# 7. Smoke check
step "Проверяю, что сайт отвечает..."
sleep 2
if curl -sf -o /dev/null http://localhost:3000/; then
    printf "\n${GREEN}=========================================${NC}\n"
    printf "${GREEN} Готово! Откройте http://localhost:3000${NC}\n"
    printf "${GREEN}=========================================${NC}\n"
else
    warn "HTTP-проверка не прошла. Откройте http://localhost:3000 в браузере вручную и см. docker compose logs."
fi
