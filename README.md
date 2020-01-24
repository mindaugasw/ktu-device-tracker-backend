
# Device tracker

## System description
**System to track company-owned mobile devices** and provide info about them: who used them and where they are now. System was developed for **Semester Project subject in KTU**, in cooperation with IT company in Kaunas. **I was responsible for creating backend for this system**.

**Problem:** company has many mobile (Android/iOS) devices that employees can use whenever they need to test new products. However, it is difficult to find specific device in the office and chat is constantly flooded with questions like "who has galaxy s9???"

**Solution:** system to track  all those devices. System consists of web application and mobile apps that should be installed on each company device. Then, whenever someone takes a device, they should scan a QR code with it, which will mark that device as in use. When finished, employee puts device back in place and scans another QR code to mark it as not in use.  
With this system any employee can see live location for each device and if it's currently in use. This system makes it much easier and faster finding specific devices in the office.

This system makes it much easier for QA team to do its job when testing products on a wide range of devices or investigating bug on a specific device.

## Links
~[**Backend is live here**](http://devicetracker-env.sgxpxxsehq.eu-central-1.elasticbeanstalk.com/)~  
~[API documentation](http://devicetracker-env.sgxpxxsehq.eu-central-1.elasticbeanstalk.com/api/docs)~  
[Postman collection with request templates](https://www.getpostman.com/collections/ccf99da1ba8281ccd4b7)  
~[**Web application live here**]()~  

## Backend installation
Built with PHP + Symfony. Requires LAMP stack. 
```
git clone git@github.com:mindaugasw/ktu-device-tracker-backend.git
cd ./ktu-device-tracker-backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console app:generate-JWT-secret
php bin/console app:create-account
php bin/console server:start
```
