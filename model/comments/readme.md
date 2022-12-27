# BootForums
Bootstrap themed, simple, easy no-click install, flat file, JSON based Forum for PHP!

## Notes:
- Still beta, might be bugs / exploits.

## Current Features:
- Markdown formatting for posts
- Easy no Database install(Uses JSON encoded flat files, lots of them, in configured folders. Easy to maintain, and still pretty quick even compared to MySQL)
- Lock threads to block new posts
- Lock New thread creation (On by default)
- Ability to brand the entire site via config.php so there is less of a need to manually edit files.
- Registration with captcha
- Login with captcha (Can be disabled)
- Pagination works well, and by default the index shows 20 threads per page, and each thread will display 10 posts per page.
- Almost EVERYTHING can be configured in config.php, just look for the option and edit.
- No admin panel (Better security)
- Admin users (Users that can lock/delete threads owned by anyone)
- Ability to force SSL
- An announcement can be put on the index page

## Potential issues:
- Still experimenting, but right now all security measures are calulated on the fly (On each page load) which is difficult to explain, but:

Each thread has an owner (index 0 in dat file). Thread owners are determined by the first user to post in the thread. This is called index 0. To save space, and processing time, when a thread is loaded in view mode
the first post (post #1) is what is used to determine the owner of the thread. I am not sure what kind of security implications this may or may not have, but it seems to work so far. This allows a smaller set of thread
databases.

Speed may or may not be an issue depending on the server this is ran on. On my server (2048GB RAM, 100GB HDD, 1Gb/s link) I was able to view 1,000,000 post in about a second. Mind you this is just with one user, but 
it goes to show the kind speed even flat files can produce.

Each user and each thread has it's own set of files. Each user is in the user folder you configure when setting up the site. But When a new thread is created, the initial creation makes 2 files threadname.dat and threadname.name
the .dat file stores the posts inside the thread, while the .name file just holds a human readable name that is displayed on the page. If a thread is locked, it will create a threadname.lock file. Very simple, but unsure 
of any security/speed issues this may cause in the future, or on a large site.

## Installation:
Just download (Clone or download zip) and put in a directory for apache or nginx. Then open config.php and configure for your needs.

## Installation notes:
- Be sure that when configuring the site for the first time that the thread_data and user_data variables in config.php are OUTSIDE the webroot (which on ubuntu is /var/www/html) this will save you a lot of hassle down the road with security. 
- When you create the thread_data and user_data folders, be sure to give www-data permissions to access them. (And with 744 permissions on those folders)


The following commands will work in most cases for creating the database folders for the forum:

> sudo mkdir /var/forum_data/threads

> sudo mkdir /var/forum_data/users

> sudo chown -R www-data:www-data /var/forum_data/threads

> sudo chown -R www-data:www-data /var/forum_data/users

> sudo chmod 744 /var/forum_data/threads

> sudo chmod 744 /var/forum_data/users


