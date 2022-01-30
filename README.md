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

8. Pdf document list
9. Navigate pdf by page number
10. Unit/Feature/Integration test
- feature video https://www.loom.com/share/e1d5eda734be43c984e32604c43d9818

#Environment
- Tech: php v8.1(Laravel v8), Mysql(v8.0), Jquery(v3.6), Tailwind(css), Pdf viewer JS pkg - Mozila pdf(https://mozilla.github.io/pdf.js/)
- Environment: OS - Debian 11, Firefox ESR 91.4.1
- IDE: Phpstorm

#Running app
1. Clone this repo
2. install php dependencies command `composer install`
3. install js dependencies command `npm install`
4. copy environment file command `cp .env.example .env`
5. edit APP_KEY in .env file command `php artisan key:generate`
6. create link from /storage folder to /storage/app/public command `php artisan storage:link`
7. edit .env file's database section accordingly
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=laravel
- DB_USERNAME=root
- DB_PASSWORD=
8. create tables commend `php artisan migrate`
9. server up commend `php artisan serve`
