CURRENT_DIR = $(shell pwd)

default: compile

compile: _compile-ccs _minify-css _compile-i18n

_compile-ccs:
	docker run -i -v $(CURRENT_DIR)/scss:/src -v $(CURRENT_DIR)/css:/target --rm lapsatech/sass

_minify-css: _compile-ccs
	docker run -i -v $(CURRENT_DIR):/src --rm lapsatech/yuicompressor

_compile-i18n:
	docker run -i -v $(CURRENT_DIR):/src --rm lapsatech/msgfmt
