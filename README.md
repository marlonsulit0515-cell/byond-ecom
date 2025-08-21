<p align="center"><a href="public\img\logo\ByondLogoFinal-02.jpg" target="_blank"><img src="public\img\logo\ByondLogoFinal-02.jpg" width="400" alt="Byond Co. Logo"></a></p>

## About Byond Clothing

A Clothing Ecommerce


Website that is responsive for Desktop and mobile

To Setup and run this project in you Device please follow these steps:
1. Download the repo clone
2. open the file in vscode
3. run composer install in the vscode terminal
4. run npm install
5. run cp .env.example .env
6. open .env and set this setting for the local database:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=byond-ecom
    DB_USERNAME=root
    DB_PASSWORD=
7. run php artisan key:generate
8. run php artisan migrate
9. run php artisan serve
