# vim: ts=4:sw=4:noexpandtab!:

BASEDIR  := $(shell pwd)
COMPOSER := $(shell which composer)

help:
	@echo "---------------------------------------------"
	@echo "List of available targets:"
	@echo "  composer-install         - Installs composer dependencies."
	@echo "  proto-generate           - Generate PHP classes from proto files."
	@echo "  phpcs                    - Runs PHP Code Sniffer."
	@echo "  phpunit                  - Runs tests."
	@echo "  phpunit-coverage-clover  - Runs tests to genereate coverage clover."
	@echo "  phpunit-coverage-html    - Runs tests to genereate coverage html."
	@echo "  help                     - Shows this dialog."
	@exit 0

all: install phpunit

install: composer-install proto-generate

test: phpcs phpunit

composer-install:
ifdef COMPOSER
	php $(COMPOSER) install --prefer-source --no-interaction;
else
	@echo "Composer not found !!"
	@echo
	@echo "curl -sS https://getcomposer.org/installer | php"
	@echo "mv composer.phar /usr/local/bin/composer"
endif

proto-clean:
	rm -rf $(BASEDIR)/tests/Protos/*;

proto-generate: proto-clean
	php $(BASEDIR)/vendor/bin/protobuf --include-descriptors \
		--psr4 ProtobufTest\\Protos \
		-o $(BASEDIR)/tests/Protos \
		-i $(BASEDIR)/tests/Resources \
		$(BASEDIR)/tests/Resources/*.proto

phpunit: proto-generate
	php $(BASEDIR)/vendor/bin/phpunit -v;

phpunit-coverage-clover:
	php $(BASEDIR)/vendor/bin/phpunit -v --coverage-clover ./build/logs/clover.xml;

phpunit-coverage-html:
	php $(BASEDIR)/vendor/bin/phpunit -v --coverage-html ./build/coverage;

phpcs:
	php $(BASEDIR)/vendor/bin/phpcs -p --extensions=php --standard=ruleset.xml src;

.PHONY: composer-install phpunit phpcs help