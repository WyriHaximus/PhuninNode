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

travis-coverage: init
	if [ -f ./build/logs/clover.xml ]; then wget https://scrutinizer-ci.com/ocular.phar && php ocular.phar code-coverage:upload --format=php-clover ./build/logs/clover.xml; fi
