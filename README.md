## KTU-iTo Device tracker

Requires LAMP stack. *Should* work with PHP 5.0.12 and higher.

**Installation:**
```
git clone git@gitlab.ito.lt:ktu/ktu-backend.git
cd ./ktu-backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console app:generate-JWT-secret
php bin/console app:create-account
```