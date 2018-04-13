################################################################################
# From outside this makefile expects the folling params:
# @param REPO_URL           Leave empty for Docker Hub. If not Docker hub, put trailing slash
# @param REPO_NAME          Like andreyhristov (in case of Docker Hub that's the user name)
################################################################################
# You probably want to change the following variable :
# - IMAGE_VERSION
################################################################################

IMAGE_VERSION=0.1
IMAGE_NAME=bws2018-app

IMAGE_URI=$(REPO_URL)$(REPO_NAME)/$(IMAGE_NAME):$(IMAGE_VERSION)

build:
	docker build -t $(IMAGE_URI) .

rebuild:
	docker build --no-cache -t $(IMAGE_URI) .

push:
	docker push $(IMAGE_URI)
