redmine-git-repo-hook
=====================

Scripts for updating redmine repo info on git post-receive hook in gitlab (also works for any git repo).


About
-----

This pair of scripts allows you to execute any shell commands on remote server when git post-receive hook is fired. The main goal is to notify redmine about changes in git repositories, which are connected to redmine projects. But you can use it for any other tasks :)

Usage
-----

1. Place a "post-receive" shell script in your repo .git/hooks/ directory (or use symlink in case of more than one repo)
2. Place config.ini and pr-hook.php in one place on redmine server accessible via http
3. Edit "post-receive" shell script and change url to one, configured for step 2
4. Edit config.ini to meet your need

###### Some remarks:
Git repositories on redmine host need to be already initialized.
