# CMS Lavue

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

## Uses by step

require of server

- ```php (v>=7.2.5)```
- ```composer (latest)```
- ```nodeJs (v>=12)```

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
