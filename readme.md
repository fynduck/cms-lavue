[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

# About CMS Lavue

CMS-LAVUE is cms for eCommerce, Blog, Landing Page, etc.

CMS use modular system, which gives you the ability to disable or enable new modules

CMS can use as REST API

## CMS-LAVUE use
- Laravel
- VueJs
- Vuex
- NuxtJs
- Stylus

## Uses by step

require of server

- ```php >= 7.3```
- ```composer```
- ```nodeJs >= 12```

#### clone repository in project directory
SSH ```git@github.com:fynduck/cms-lavue.git .```

#### Up project

 - ```cp .env.example .env``` and set correct config data in .env file
 
 - ```composer install```
 
 - ```php artisan key:generate```
 
 - ```php artisan project:modules```
 
 - ```php artisan project:migrations```
 
 - ```php artisan project:seeds```
 
 #### Install npm and run
 
 - ```npm install```
 
 - ```npm run prod```

## License
The MIT License (MIT). Please see [License File](/LICENSE) for more information.
