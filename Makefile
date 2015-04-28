all: cs dunit dunit-nightly unit
travis: cs unit-travis
contrib: cs dunit unit

init:
	if [ ! -d vendor ]; then composer install; fi;

cs: init
	./bin/phpcs --standard=PSR2 src/

unit: init
	./bin/phpunit --coverage-text --coverage-html covHtml

unit-travis: init
	./bin/phpunit --coverage-text --coverage-clover ./build/logs/clover.xml

dunit: init
	./bin/dunit

dunit-nightly: init
	./bin/dunit -c .dunitconfig-nightly
