CURRENT_DIR = $(shell pwd)

BASENAME = $(shell basename $(CURRENT_DIR))
IMAGE_NAME = $(BASENAME):latest
CONTAINER_NAME = $(BASENAME)_1

SOURCE_DIR  = $(CURRENT_DIR)/src
TARGET_DIR  = $(CURRENT_DIR)/target

default: build

# CLEAN

clean:
	rm -rf $(TARGET_DIR)

## INIT

init:
	mkdir -p $(TARGET_DIR)
	cp -Rfp $(SOURCE_DIR)/* $(TARGET_DIR)/

## COMPILE

compile: init
	find $(TARGET_DIR) -type f -name Makefile -print0 | xargs -0 -n1 dirname | xargs -n1 make -C

## BUILD

build: clean compile

## RUN, START, STOP

_docker-build: compile
	docker build -t $(IMAGE_NAME) -f $(CURRENT_DIR)/Dockerfile $(TARGET_DIR)

run: _docker-build
	mkdir -p $(TARGET_DIR)/sites/html
	docker run -it -p 80:80 -p 443:443 -v $(TARGET_DIR)/sites:/var/www $(IMAGE_NAME)

start: _docker-build
	mkdir -p $(TARGET_DIR)/sites/html
	docker run -d  -p 80:80 -p 443:443 -v $(TARGET_DIR)/sites:/var/www --name $(CONTAINER_NAME) $(IMAGE_NAME)

stop:
	docker container stop $(CONTAINER_NAME)
	docker container rm $(CONTAINER_NAME)

restart:
	docker container restart $(CONTAINER_NAME)
