# Base Wordpress Install

This is a plain vanilla wordpress install with a few things I like on every project pre-done. I clone it to start new projects.

 - Wordpress itself is a submodule under /wordpress. This makes gitting, sorting out wordpress upgrades, etc much easier.
 - wp-content is not within WordPress, it's been moved to wordpress–content, which makes it possible to keep WordPress itself out of version control but still version control themes/plugins.
 - Ideally it would be nice to have the plugins as submodules too - but few seem to be on git, as wp still use svn.
 - It's got my VagrantPuppet repo in as a submodule, and includes a vagrantfile. You should be able to clone this repo, run 'vagrant up', and have a dev environment ready to go. Customise the vagrant config in .puppet/manifests/base.pp