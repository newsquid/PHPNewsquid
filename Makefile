.PHONY: test

test:
	phpunit --bootstrap test/_autoload.php test
