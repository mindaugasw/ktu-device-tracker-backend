## KTU-iTo Device tracker

API documentation: /api/docs  
Postman collection with request templates: [link](https://www.getpostman.com/collections/ccf99da1ba8281ccd4b7)

**Installation:**
```
git clone git@gitlab.ito.lt:ktu/ktu-backend.git
cd ./ktu-backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console app:generate-JWT-secret
php bin/console app:create-account
```

Requires LAMP stack. *Should* work with PHP 5.0.12 and higher.