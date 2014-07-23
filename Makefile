.PHONY: test test_unit test_integration test_all

PHPUNIT=docker run -v `pwd`:/var/www agallou/phpunit

test: test_unit

test_unit:
	$(PHPUNIT) --bootstrap test/_autoload.php test/unit

test_integration:
	$(PHPUNIT) --bootstrap test/_autoload.php test/integration

test_all: test_unit test_integration
