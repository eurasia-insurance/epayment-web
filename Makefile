CURRENT_DIR = $(shell pwd)

SOURCE_DIR  = $(CURRENT_DIR)/src
TARGET_DIR  = $(CURRENT_DIR)/target

SOURCE_DOCROOT = $(SOURCE_DIR)/docroot
TARGET_DOCROOT = $(TARGET_DIR)/docroot

SITE = eurasia36_kz

SITE_PATH = $(TARGET_DOCROOT)/$(SITE)

# CLEAN

clean:
	rm -rf $(TARGET_DIR)

## INIT

init:
	mkdir -p $(TARGET_DOCROOT)
	cp -Rfp $(SOURCE_DIR)/* $(TARGET_DIR)/

## COMPILE

compile: _compile-ccs _minify-css _compile-i18n

_compile-ccs: init
	docker run -it -v $(SITE_PATH)/scss:/src -v $(SITE_PATH)/css:/target --rm lapsatech/sass

_minify-css: init _compile-ccs
	docker run -it -v $(SITE_PATH)/css:/src --rm lapsatech/yuicompressor ; \

_compile-i18n: init
	docker run -it -v $(SITE_PATH)/i18n:/src --rm lapsatech/msgfmt ; \

## BUILD

build: clean compile

## ALL (alias to build)

all: build

## RUN, START, STOP

_docker-build: compile
	docker build -t eurasia36/eurasia36-kz-httpd:latest $(TARGET_DIR)

run: _docker-build
	docker run -it -p 80:80 -p 443:443 -v $(TARGET_DOCROOT):/var/www/eurasia36_kz --name eurasia36_kz_htpd_1 eurasia36/eurasia36-kz-httpd:latest

start: _docker-build
	docker run -d  -p 80:80 -p 443:443 -v $(TARGET_DOCROOT):/var/www/eurasia36_kz --name eurasia36_kz_htpd_1 eurasia36/eurasia36-kz-httpd:latest

stop:
	docker container stop eurasia36_kz_htpd_1
	docker container rm eurasia36_kz_htpd_1

restart:
	docker container restart eurasia36_kz_htpd_1
