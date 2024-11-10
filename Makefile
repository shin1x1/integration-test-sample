.PHONY: install
install: up
	docker compose exec php-fpm composer install
	cd src && (test -f .env || cp -a .env.example .env && docker compose exec php-fpm php artisan key:generate)
	docker compose exec php-fpm php artisan migrate
	docker compose exec php-fpm php artisan db:seed

.PHONY: up
up:
	docker compose up -d

.PHONY: down
down:
	docker compose down -v

.PHONY: test
test:
	docker compose exec php-fpm php artisan test
