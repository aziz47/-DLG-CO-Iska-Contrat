up:
	clear
	make stop
	docker-compose -f docker-compose.yml -f docker-compose.$(arg).yml up -d --build
stop:
	docker-compose stop
	docker system prune -f
full-restart:
	clear
	make stop
	docker system prune -f
	docker-compose up -d --build
shell:
	clear
	docker-compose exec $(arg) bash
build:
	docker-compose build
node-watch:
	clear
	docker-compose exec node yarn watch
node-build:
	clear
	docker-compose exec node yarn build
messenger-mail:
	clear
	docker-compose exec www php bin/console messenger:consume async -vv
fixtures:
	docker-compose exec -u 1000 www php bin/console d:f:l --no-interaction
migration:
	clear
	docker-compose exec -u 1000 www php bin/console m:migration
	docker-compose exec -u 1000 www php bin/console d:m:m --no-interaction
	make fixtures
cache-clear:
	clear
	docker-compose exec -u 1000 www php bin/console c:c

logs:
	docker-compose exec www symfony serve:log