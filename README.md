# Processor
A handy class for managing background processes in PHP

Requirements:
-------------
It is based on the awesome **Background Process** script created by "cocur": https://github.com/cocur/background-process

Ho to use it:
-------------

1. Run `composer install` to install the required dependency

2. You can simply include the Processor.php file in your scripts. Please check the Test.php file on ways to use it.
    
3. If you want to run the supplied Test.php file just run it in your browser: 
    
    https://mysite.com/Processor/Test.php
    
4. Play around with the options. In the package there is also an example of a long running script: LongRunningPHPScript.php that you can use it for testing. It will simply run for 1 minute and then finish.

5. Enjoy.

### Disclaimer:

Since this is a very powerful script it is important to be 100% sure where are you putting it and which kind of processes you would run. I shall not be held liable for any misuse or damages it may do to your data or your server.