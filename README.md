## Debug
- Install the extension named `xdebug` for php in visual studio code.
- Download and install xampp or configure docker with PHP 8.1 or later
- Follow the instructions in the xdebug config website `https://xdebug.org/wizard` OR
- Run the following command to ensure xdebug is correctly installed `php -v`. If you see the xdebug name there then you are good to go.
- Run `php -i` in CMD then paste the results on the `https://xdebug.org/wizard` website
- Download the recommended DLL library
- Move the downloaded file to C:\xampp\php\ext, or where your xampp is installed and rename it to php_xdebug.dll
- Update `php.ini` and add the line: `zend_extension = xdebug`
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