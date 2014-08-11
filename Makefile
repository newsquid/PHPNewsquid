.PHONY: test test_unit test_integration test_all start_nsqor stop_nsqor

PHPUNIT=docker run -v `pwd`:/var/www agallou/phpunit
PHPUNIT_NSQOR=docker run -v `pwd`:/var/www --link nsqor_test:nsqor agallou/phpunit

test: test_unit

test_unit:
	$(PHPUNIT) --bootstrap test/_autoload.php test/unit

test_integration: start_nsqor
	-$(PHPUNIT_NSQOR) --bootstrap test/_autoload.php test/integration
	-@$(MAKE) stop_nsqor

start_nsqor:
	docker run -d --name nsqor_test_db -e POSTGRES_DB=nsq_test \
		-e POSTGRESQL_USER=nsq -e POSTGRESQL_PASS=nsq orchardup/postgresql
	docker run -d --name nsqor_test --link nsqor_test_db:db \
		-e DB_NAME=nsq_test -e DB_HOST=db \
		-e DB_USER=nsq -e DB_PASS=nsq -p 1337 \
		index.ouchmg.com/nsqor make tt_test
	./wait_for_nsqor.sh


stop_nsqor:
	-@docker stop -t 2 nsqor_test_db &> /dev/null
	-@docker stop -t 2 nsqor_test &> /dev/null
	-@docker rm -f nsqor_test_db &> /dev/null
	-@docker rm -f nsqor_test &> /dev/null

test_all: test_unit test_integration
