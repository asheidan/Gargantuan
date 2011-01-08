# Gargantuan!!!

(A minimal web framework written in php.)

As this is framework is written as an exercise/proof of concept, several
functionalities which you may have come to expect from a modern framework is
missing. These are "left as an exercise for the reader".

The framework is heavily inspired by [Rails](http://rubyonrails.org/ "Web development that doesn't hurt").

This documentation is in no way ready and if you want to use this to something
else than inspiration you might want to think twice.

## How the what now?

So, you got the idea that using this framework in your own project would be a
great idea. Are you really sure about that? I wouldn't use it and I wrote
it...

### Getting the source

As you're reading this you probably already got the source somehow. But I
digress...

The latest version of the source is available in a public repository on
[github](https://github.com/asheidan/Gargantuan).

#### The easy way

The easiest way to obtain it (and keeping it updated) is using it as a
[git submodule](http://book.git-scm.com/5_submodules.html "More information about submodules in git").

#### Two more (boring) ways

1. Clone the repository. Don't do this if you use git in your project, then
	you should really look into git submodules.

		$ git clone \
			https://github.com/tpope/vim-fugitive.git \
			${path_to_project_libs}/Gargantuan

2. Download the source as a tarball from github at
	[https://github.com/asheidan/Gargantuan/archives/master]()

### Getting started

Make sure that the directory where you put Gargantuan is in your path.

Gargantuan presumes that your application uses a certain layout:

	super_awesome_project/
		app/
			controllers/		# Your controllers
			models/				# Your models
			views/				# Your views
				layouts/
		config/
			app.ini				# Configuration of the application
		public/
			javascripts/
			stylesheets/
		vendor/
			Gargantuan/

Add your models to your path, redirect all requests to a file which requires
'Gargantuan/Entry.php' and you might be good to go. Or everything might just
break down horrible. Chances are about 50/50 these days...
