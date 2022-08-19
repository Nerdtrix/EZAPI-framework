## Debug
- Install the extension named 'xdebug' for php
- Download and install xampp or configure docker with PHP 8.1 or later
- follow the instructions in the xdebug config regarding https://xdebug.org/wizard
- run the following command to ensure xdebug is correctly installed `php -v`. If you see the xdebug name there then you are good to go.
- Once the xdebug has been correctly configure you can press F5 or go to run/start debugging to start debugging. 

## PHPUnit debug
- Press Ctrl + Shift + D
- Select PHPUnit Debug
- Press the Start button next to run or Press F5

## Start without debugging
To to Terminal/Run Task/EZAPI server


## First time only
- composer install
- composer dump-autoload
- Duplicate .env.example and rename them with .env and copy the content of example to them.
- If Composer is preferred follow the step above.