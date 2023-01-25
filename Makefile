VERSION=0

CONSOLE_CLI=symfony console

build_project:
docker-compose up --build -d
$(CONSOLE_CLI) doctrine:migrations:migrate
$(CONSOLE_CLI) doctrine:fixtures:load
cp .env.dist .env

cc-rc-exec:
	echo "Cleaning up application cache"
	$(CONSOLE_CLI) doctrine:cache:clear-metadata
	$(CONSOLE_CLI) doctrine:cache:clear-query
	$(CONSOLE_CLI) doctrine:cache:clear-result
	composer run rebuild-bootstrap
	$(CONSOLE_CLI) cache:clear --no-warmup || rm -rf var/cache/*
	echo "Warming up application cache"
	$(CONSOLE_CLI) cache:clear --no-warmup  --no-debug