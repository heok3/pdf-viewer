## Pdf viewer

Pdf viewer provides user to open/upload multiple pdf files, and take a note

#Feature
1. View a pdf file
2. Open multiple pdf files
3. Zoom in/out
4. No print
5. Take a note for opened files
6. Save/upload a note
7. Timer

#Bonus
1. Pdf document list
2. Navigate pdf by page number
3. Unit/Feature/Integration test

#Running app
1. Tech: php 8.1/Mysql/Jquery
2. Clone this repo
3. install php dependencies command `composer install`
4. install js dependencies command `npm install`
5. copy environment file command `cp .env.example .env`
6. edit APP_KEY in .env file command `php artisan key:generate`
7. create link from /storage folder to /storage/public command `php artisan storage:link`
8. edit .env file's database section accordingly
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=laravel
- DB_USERNAME=root
- DB_PASSWORD=
9. create tables commend `php artisan migrate`
10. server up commend `php artisan serve`
