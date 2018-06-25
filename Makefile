update:
	docker-compose exec fpm rm -rf var/cache/* var/logs/* web/bundles/* web/js/* web/css/*
	docker-compose exec fpm bin/console p:i:a --symlink
	docker-compose exec fpm bin/console a:i --symlink
	docker-compose run --rm node yarn run webpack

install:
	docker-compose exec fpm rm -rf var/cache/* var/logs/* web/bundles/* web/js/*
	docker-compose exec fpm bin/console pim:install --force -e=prod
	docker-compose run -uroot --rm node yarn install
	docker-compose run --rm node yarn run webpack
	docker-compose exec fpm sudo chmod -R 777 .

launch-job:
	docker-compose exec fpm bin/console akeneo:batch:job-queue-consumer-daemon