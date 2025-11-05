test-512:
	vendor/bin/pest --type-coverage --min=100 -memory-limit=512M
	vendor/bin/pest
	vendor/bin/pint
	vendor/bin/phpstan --memory-limit=512M
