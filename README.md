
# Device tracker

## System description
**System to track company-owned mobile devices** and provide info about them: who used them and where they are now. System was developed for **Semester Project subject in KTU**, in cooperation with IT company in Kaunas. **I was responsible for creating backend for this system**.

**Problem:** company has many mobile (Android/iOS) devices that employees can use whenever they need (they are used mostly by QA team while testing new products on various devices). However, it is difficult to find specific device in the office and chat is constantly flooded with questions like "who has galaxy s9???"

**Solution:** system to track  all those devices. System consists of web application and mobile apps that should be installed on each company device. Then, whenever someone takes a device, they open that app on the device and use it to scan their personal QR code. The system then registers device as "in use" and assigns it to that employee. After using device that employee scans another QR code to mark device as "not in use".  
With this system, whenever someone needs some specific device, they can open web application, search for that device, and see if it is currently in use and by whom (or when it was last used). If device is marked as not in use but you still can't find it in storage place, you now know who to blame about it :)

This system makes it much easier for QA team to do its job when testing products on a wide range of devices or investigating bug on a specific device.

## Links
[**Backend is live here**](http://devicetracker-env.sgxpxxsehq.eu-central-1.elasticbeanstalk.com/)  
[API documentation](http://devicetracker-env.sgxpxxsehq.eu-central-1.elasticbeanstalk.com/api/docs)  
[Postman collection with request templates](https://www.getpostman.com/collections/ccf99da1ba8281ccd4b7)  
[**Web application (frontend) live here**](http://tommoc1.stud.if.ktu.lt/) (developed by a teammate)  

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
