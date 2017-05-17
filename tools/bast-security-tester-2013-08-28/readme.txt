HOW TO USE:
-----------------------
1) launch the index.php file (and see it's contents for details)

or

2) include the SecurityTest.php in your application and create the MainTest like so:

new MainTest();


DISCLAIMER:
-----------------------
This is just a set of simple security tests created in PHP and targeted mainly for PHP and Apache software. If you don't see any vulnerabilities listed in report, then do not think your system is secure (it's just that this class failed to find any weakneses).

KEEPING UP TO DATE:
---------------------
This class and its database will be updated every time the new version of tested software (PHP/Apache) will be released to public.

FEEDBACK:
----------------------
If you found any bug or inconcistency in my class, please do not hesitate and contact by sending email to aargoth@boo.pl. Your feedback will be greatly appreciated and used to improve this class.

TODO:
-----------------------
Better OS detection, more detailed reports, tests for Lighthttpd, MySQL, PostgreSQL, FTP, SSH. Improved tests for open_basedir path traversal, local port scans (to detect other software installed on server but not connected with PHP).

CHANGELOG:
-----------------------
1.12.09.28:
- initial version

1.12.10.08:
- DB update: Added 3 security vulnerabilities for Apache 2.4.x (1 important, 2 low)


1.13.03.28:
- DB update: Added security vulnerabilities for Apache and PHP

1.13.09.28:
- DB update: Added security vulnerabilitues for Apache (6 moderate-to-low) and PHP (more than 8).
- Reporting End-of-life warning for old software.


Cheers,
- Artur Graniszewski

