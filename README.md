# Dietary App Test Task Backend

To login the user
UserName : admin
Password : admin123

## Install

clone this repository using ### `git clone https://github.com/satishtcw/dietary.git`

in the root of repository you will get ### `dietary_db.sql` create a Database and import this file.

## Config

in ### `.env` file add database config

DB_HOST=localhost \
DB_NAME=databaseName \
DB_USER=UserName \
DB_PASS=Password 

In `src/CORS.php` add front-end url to overcome CORS error

header("Access-Control-Allow-Origin: http://localhost:3000"); your frontend url here

Run Composer command ### `composer install`

## go to public directory and run `php -S localhost:8080`

I think using this code you can understand my coding skill.