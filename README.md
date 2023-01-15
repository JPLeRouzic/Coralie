# Coralie
Coralie is flat-file blog derived from HTMLy
https://github.com/danpros/htmly

After 3 years Coralie is now very different from HTMLy, but it still tries to be compatible with content developped on HTMLy.
It is used in Padirac Innovation website: https://padiracinnovation.org/News/ which serves 300-600 users every day and has around 1K posts.

What attracted me to HTMLy initialy was the flat-file usage as I strongly dislike SQL and insecurities created by having another server on my VPS.
HTMLy looks much simpler than Wordpress, so it may appeal because at first glance one single person should be able to master the source code.
Yet 3 years of using HTMLy has shown that debugging or extending HTMLy is no fun activity. 

HTMLy didn't changed so much since it was published in 2013, it's a great example of spaghetti code and bugs had even been introduced in it in recent years.
What's lacking most in HTMLy is a test and regression suite, Coralie is not better in this area.

This motivated me to develop Coralie. Coralie has a flat file database. It is developed to run on a VPS and Apache/PHP as host.
I don't want to be involved in problems like configurations on host. Coralie also does not have fancy install functions: You upload it to your Apache web folder and that's it.
Ideally most changes introduced with Coralie will be incorporated back in HTMLy.
I am also sorry to add more noise to the crowd of flat-file blogs/CMS.
You should also be aware that there are much better flat-file CMS out there.

Coralie uses small files instead of the horribly large htmly.php and functions.php.
The login functions are properly written.

Coralie does not use external tools for comments, it presently uses a modified forum frim Mitchel Urgero (https://urgero.org).

Coralie separates the infrastructure (the system/includes folder which comes for the dispatch folder) from the functionalities from the views:

![image](https://user-images.githubusercontent.com/18621529/209707133-d3659acf-0595-4fc2-9dd8-58d9767264a5.png)

In future there will be no HTML/JS code inbedded in PHP code of functionalities or infrastructure (like in today's code).

It's code is PHP8.1 compliant

The /model/ folder contains one sub-folder by functionality and attempts are made to keep files small.
I am however still unsatisfied (there are a lot of snippets that are redundant)

The /views/ folder contains the UI for administration and enables the usage of many themes that can be changed on the fly. 
I am still not satisfied as as told above there are still lots of UI codes which are located in functionalities' folder.

The user can listen to posts.

You will not find internationalized menus as I don't feel the need, yet it should be very simple to introduce this function in Coralie.

Coralie still has at least a dozen of bugs.

You can have a look at it here:
https://csrf.4lima.de/News

# Roadmap
Coralie, contrary to HTMLy, will have plugins.
Coralie will be able to use at least 1/3 of WordPress plugins (they are over 70K, so Coralie will have only access to ~25,000 plugins :-) ).
For WordPress plugins to be usable with Coralie, they must behave as WP asks: They should interact with the database only through WP_Query().

# Installation
* Upload this code to your Web folder
* ![image](https://user-images.githubusercontent.com/18621529/210220361-107500b6-640d-406f-a8ab-4b435be1c1ca.png)
* Change the root folder in config.ini (for example in my case site.url = "/News/")
* Upload your content (compatible with HTMLy) in /content/users/"your login"/
* ![image](https://user-images.githubusercontent.com/18621529/210220057-a3854131-12c6-4eba-8a6b-ba9428e56b11.png)
* Remove /content/widget/recent.cache
* ![image](https://user-images.githubusercontent.com/18621529/210220168-df3315db-c143-4cf4-ba4f-7e5f2e7e8397.png)
