Prerequisites
-------------

### 1) You have git installed

On a Mac this can be achieved by installing [homebrew](http://mxcl.github.com/homebrew/) and running `brew install git` (my preferred method), installing the [Heroku Toolbelt](https://toolbelt.heroku.com), or downloading it from <http://git-scm.com/downloads/>.

On Windows you got a copy with RailsInstaller, but alternately you could get a copy from the [Heroku Toolbelt](https://toolbelt.heroku.com), or downloading it from <http://git-scm.com/downloads/>.

### 2) You know how to use git

Well out of the scope of this document. I wrote [some simple instructions](http://dd9.com/2012/07/git-primer/), or you can use [GitRef.org](http://gitref.org/index.html), my preferred reference for the tricky stuff.

Installation
------------

### 1) Download and Install MAMP or XAMPP

There are [XAMPP](http://www.apachefriends.org/en/xampp-windows.html#641) downloads for both Mac and Windows, although I’ve never used it on the Mac; I use [MAMP](http://www.mamp.info/en/index.html) instead.

If you’re feeling adventurous, you could try WAMPServer or EasyPHP or something. I don’t have any experience with XAMPP; I just picked it out because quick googling suggested it was currently the most popular. There are [lots of alternatives](http://en.wikipedia.org/wiki/Comparison_of_WAMPs).

### 2) Install Wordpress

[Download Wordpress](http://wordpress.org/download/) and extract it to the directory where you want to keep this project. Then set up the database: create a new database in XAMPP/MAMP’s phpMyAdmin, and configure wp-config.php to use it. Alternately, you may want to import the database from the live application; if you do so, see step 5 below and remember to [change the site URL](http://codex.wordpress.org/Changing_The_Site_URL).

### 3) Get a copy of the project

From within the project directory, run:

`git clone <project’s git url> .`

For example, if you wanted to get the code for dd9.com, you’d run:

`git clone git@github.com:DD9/dd9.git .`

Don’t forget the period at the end; that clones it directly into your project directory (the same place you installed wordpress) instead of into a subdirectory where it won’t do anything. I recommend running this command after installing Wordpress, because git is better at adding files without overwriting things than your operating system probably is. I think.

Note that if we’re working on a project hosted by a third party (like HRT), you may need to run “svn checkout” instead of “git clone”, but the rest of the process should be more or less the same.

### 4) Tell MAMP/XAMPP to serve up your application

In MAMP you can set any directory you want to be your site root. Set it to your project directory. In XAMPP, as far as I can tell, the siteroot is hardcoded to `\xampp\htdocs`, so put your files there. XAMPP recommends [working in a subdirectory](http://www.apachefriends.org/en/xampp-windows.html#1168). If this seems lame to you, you might want to try WAMPServer or something to see if it’s better.

And don’t forget to turn on Apache and MySQL via your MAMP/XAMPP control panel.

### 5) Import the database

In MAMP, when you start the server it opens a web browser which takes you to another control panel. XAMPP does the same thing when you click the “Admin...” button. That interface should have a link to your local phpMyAdmin, where you can import the export you generated from the live application, or make an export if you’re trying to move your data in the other direction.

Note that an imported database won't work if you haven't [changed the site URL](http://codex.wordpress.org/Changing_The_Site_URL). I recommend using the wp-config.php method, which consists of adding the following to wp-config.php:

`define('WP_HOME','http://localhost:8888');`  
`define('WP_SITEURL','http://localhost:8888');`

If your MAMP/XAMPP's Apache isn't configured to serve on port 8888, you'll need to change the port above.

### 6) Does it work?

As of now, your application should theoretically be working at <http://localhost:8888>, or whatever URL MAMP/XAMPP is giving you. There are a bunch of things which could go wrong, however:

- Links in your application may be broken if you’re running in a subdirectory. You can put your WP site in the place of `\xampp\htdocs`, and swap the old directory back in place when you need it. Personally, I think this sucks. Have we figured this out yet?
- Wordpress, particularly the admin area, may not let you use it if you aren’t visiting from the original domain. Don't forget to [change the site URL](http://codex.wordpress.org/Changing_The_Site_URL).

Deployment
----------

If the project you’re working on uses wpengine, you can use [git for deployment](http://git.wpengine.com/getting-started/), which is awesome. Set it up like this:

### 1) Make sure you’re authorized on our wpengine account

Your ssh key (in ~/.ssh/id_rsa.pub) needs to be sent in a support ticket to wpengine to be associated with our account. Without this all your attempts to connect to wpengine’s git server will fail. Ask Taavo if you aren't sure.

### 2) Set up git remotes

From within your project, run:

`git remote add wpengine git@git.wpengine.com:production/<<project name>>.git`  
`git remote add staging git@git.wpengine.com:staging/<<project name>>.git`

For example, if you’re working on dd9.com, those commands would look like this:

`git remote add wpengine git@git.wpengine.com:production/dd9.git`  
`git remote add staging git@git.wpengine.com:staging/dd9.git`

### 3) Deploy

To deploy to staging, run:

`git push staging`

And to deploy to production run:

`git push wpengine`

This will upload the latest code on your computer to wpengine and tell them to deploy it.