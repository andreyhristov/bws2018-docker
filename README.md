BWS2018 Docker Demo
===================

General project structure
---
This demo has two parts
- _app_, where the application code resides 
- _runtime_, where the Docker related is situated

_App_
---
Under _app_ you will find just two files. One is public/index.php with sample code which uses Monolog for application logging.
The second file is _composer.json_, where the dependency to Monolog is stated.

_Runtime_
---
Under _runtime_ you will find 3 directories
- assets
- base
- local

### runtime/assets
This directory is currently empty. If you need some scripts to be part of the container but they are not related to the application code, then this is the place for them. For example let's say you have a script for running Doctrine migrations or other maintenance works. Typically, when building the production container the assets are copied into it.

### runtime/base
The base directory currently has only one subdirectory - _bws2018-app_ . This is the directory where the definitions base container for the web app are residing. There you will find a Dockerfile, a vhost file definition, and a Makefile for (re)building / pushing the image.
If you make changes to the Dockerfile, you need to build a new image and if the current image version has been already published and used by others you have to bump the version. The version number is in the Makefile : `IMAGE_VERSION=0.1` . Change it to whatever you like.

### runtime/local
Under _local_ you will see a bunch of files and directories. This is where you will be most of the time during development.

To save you writing a lot, some commands to Docker and Compose have already been added to a Makefile. `make up` will start the cluster. `make killall` will stop it and remove the containers (not the images). And all at once : `make killall up`
To see all available commands type `make <tab>`. 
- `make ps` - shows the running containers and some information about them. Here is an example output

```
          Name                         Command               State                        Ports                      
--------------------------------------------------------------------------------------------------------------------
bws2018_app                 docker-php-entrypoint apac ...   Up       0.0.0.0:4080->80/tcp                           
bws2018_composer            /docker-entrypoint.sh composer   Exit 0                                                  
bws2018_elasticsearchlogs   /bin/bash bin/es-docker          Up       0.0.0.0:9200->9200/tcp, 0.0.0.0:9300->9300/tcp 
bws2018_kibana              /bin/sh -c /usr/local/bin/ ...   Up       5601/tcp                                       
bws2018_logstash            /usr/local/bin/docker-entr ...   Up       0.0.0.0:5000->5000/tcp, 5044/tcp, 9600/tcp     
bws2018_mailhog             MailHog                          Up       1025/tcp, 8025/tcp                             
bws2018_mysql_tunnel        /usr/bin/ssh -T -N -o Stri ...   Up       2222/tcp, 0.0.0.0:23306->3306/tcp              
bws2018_mysqldb             docker-entrypoint.sh mysql ...   Up       0.0.0.0:13306->3306/tcp                        
bws2018_pma_local           /run.sh phpmyadmin               Up       80/tcp, 9000/tcp                               
bws2018_proxy               /app/docker-entrypoint.sh  ...   Up       0.0.0.0:443->443/tcp, 0.0.0.0:80->80/tcp       

```

- `make logs-all` - dumps at the screen the stdouts of all started containers
- `make logs-app` - dumps at the screen the stdout of the app container
- `make kill` - stops and removes the containers of the cluster. `make killall` OTOH is a weapon of mass destruction and kills all containers running on the system.
- `make volumes-clean` - because all containers are ephemeral by design, the persistent data should be stored somewhere. Here it is under `runtime/local/volumes`. This command clean up all data to start from scratch. For example it will delete all MySQL datafiles.
- `make fix-rights` - some of the containers don't run under anything but root. This results in files in the volumes with root as owner and not the application user. This command chowns everything under volumes/ to 1000:1000 which is the first (and probably the only one) GUI user on Ubuntu
- `make composer-install` - starts the `composer` container to install all dependencies found in `app/composer.json`. Under normal circumstances the `composer` container is not running and started only for this command
- `make shell` - starts a non-privileged shell in the `app` container. Use for quickly checking things from container's inside, like files or install some software for a quick check, which at container restart will vanish automagically.
- `make hosts-add` - for the project to run some server names need to be registered. In this project this is not done with Traefik but just by putting some entries in `/etc/hosts`. This command add the entries. The command is smart to check for all VIRTUAL_HOST env variables in all docker compose yml definition files and register them.
- `make hosts-clean` - removes the added entries from `/etc/hosts`

## How does this work?

All services are split into own docker-compose.*.yml files. In most of the files there is just one service per file. An exception is however docker-compose.mysql.yml where there is a second service for PHPMyAdmin. Also notable exception is the ELK (ElasticSearch / Logstash / Kibana) stack which resides as a whole in one file : docker-compose.elk.yml.

The different containers are split into different networks. For example, the nginx proxy cannot speak to the MySQL database directly. It can't see the database because the database is on a different network. OTOH, PHPMyAdmin sits on the nginx network, so nginx can act as a reverse proxy but also sits on the MySQL network.

By default Docker Compose looks only for docker-compose.yml (or .yaml). With the help of some commands in a pipe this is scripted inside the Makefile to generate this file out of all docker-compose.*.yml files when starting the container. Generating the file has the pro that all other commands will use that file. D-C can also be started with multiple definition files by using the `-f` parameter. However, in this case the command becomes very very long and hard to inspect. And also in all cases the names have to be passed.

For your information, Docker Compose has the notion of projects. The project is a cluster. By default the name of the project is the name of the current directory (only the last part, not the whole path). The project name is also used for naming the default bridge network of the project. However, there is a problem if a directory structure like the one in this source repo is used. In all cases you will be in `local` and all projects will be named `local`. Thus `-p` option is passed to docker-compose to specify specific name. With some variable magic this is done in one place in the Makefile and then reused in the whole Makefile.

## Why all those separate service files instead of one big docker-compose.yml ?
This split makes it easier to find the specific service but also to reuse it in your next project. Just copy the docker-compose.mysql.yml to use MySQL in your next project. Change few params and you have a new MySQL. In the case of MySQL there is also a separate Makefile.mysql-db, which includes some administrative commands like `make mysql-db-shell` to get into a shell inside the MySQL container.
