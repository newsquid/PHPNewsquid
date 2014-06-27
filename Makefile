.PHONY: test test_unit test_integration test_all

test: test_unit

test_unit:
	phpunit --bootstrap test/_autoload.php test/NewsquidTest.php test/NewsquidUserTest.php test/NewsquidProductTest.php

test_integration:
	phpunit --bootstrap test/_autoload.php test/IntegrationTest.php

test_all: test_unit test_integration
