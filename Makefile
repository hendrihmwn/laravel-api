prepare:
	composer update
	php artisan migrate

run-app:
	php artisan serve

run-scheduler:
	php artisan schedule:work

run-test:
	php artisan test