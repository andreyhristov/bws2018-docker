PROJECT_NAME=bws2018
DC=docker-compose -p $(PROJECT_NAME)

first:
	@echo "Use make + <tab> to find possible targets"

include Makefile.mysql-db

LOCALHOST_USER=$(shell whoami)
LOCALHOST_IP=$(shell ifconfig | grep '192.168' | cut -d ':' -f 2 | cut -d ' ' -f 1)


VIRTUAL_HOSTS=$(shell grep VIRTUAL_HOST docker-compose.*.yml | tr -d ' ' | cut -d ':' -f 3 | tr -d '"' | tr -d "'" | sort | tr -s '\n' ' ')

up: _generate-docker-compose-yml _up-aux _mysql-check-and-create-databases


_generate-docker-compose-yml:
	@echo "version: '3.2'" > docker-compose.yml
	@echo "services:" >> docker-compose.yml
	find -mindepth 1 -maxdepth 1 -type f -name "docker-compose.*.yml" -print | sort  | xargs cat | grep -v "^version:" | grep -v "^services:" >> docker-compose.yml
	sed -i "s/LOCALHOST_USER/$(LOCALHOST_USER)/g" docker-compose.yml
	sed -i "s/LOCALHOST_IP/$(LOCALHOST_IP)/g" docker-compose.yml

_up-aux:
	$(DC) up -d

ps:
	$(DC) ps

logs-all:
	$(DC) logs

logs-app:
	$(DC) logs -f app

kill:
	$(DC) kill
	$(DC) rm --force

killall:
	docker kill `docker ps -q`
	docker rm `docker ps -aq`

stop:
	$(DC) stop
	$(DC) rm --force

pull:
	$(DC) pull

volumes-clean:
	sudo rm -rf ./volumes/mysql
	sudo rm -rf ./volumes/composer-cache
	rm -rf ./volumes/apache_logs/*
	rm -rf ./volumes/elasticsearch/*
	rm -rf ./volumes/logstash/*
	

fix-rights:
	sudo chown -R 1000.1000 volumes/*

composer-install:
	$(DC) run --rm --user 1000:1000 composer sh -c "composer --working-dir=/app install"

shell:
	$(DC) exec app bash

hosts-clean:
	@sudo sed -i  '/$(PROJECT_NAME)-local-development/d' /etc/hosts

hosts-add: hosts-clean
	@echo "127.0.0.1 $(VIRTUAL_HOSTS) #$(PROJECT_NAME)-local-development" | sudo sudo tee -a /etc/hosts


