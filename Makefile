.PHONY: test test_unit test_integration test_all

test: test_unit

test_unit:
	phpunit --bootstrap test/_autoload.php test/unit

test_integration:
	phpunit --bootstrap test/_autoload.php test/integration

test_all: test_unit test_integration
