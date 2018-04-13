REPO_URL=                 # leave empty for Docker Hub
REPO_NAME=andreyhristov   # for Docker Hub this is the user name

# We need this so simple `make` does nothing
first:
	@echo "INFO: make + tab will show you all commands"

login:
	docker login $(REPO_URL) -u $(REPO_NAME)

###############################################################################
build-bws2018-app:
	cd bws2018-app && make build REPO_URL=$(REPO_URL) REPO_NAME=$(REPO_NAME)

rebuild-bws2018-app:
	cd bws2018-app && make rebuild REPO_URL=$(REPO_URL) REPO_NAME=$(REPO_NAME)

push-bws2018-app:
	cd bws2018-app && make push REPO_URL=$(REPO_URL) REPO_NAME=$(REPO_NAME)

###############################################################################
