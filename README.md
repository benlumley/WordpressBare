# Base Wordpress Install

This is a plain vanilla wordpress install with the folder structure and some plugins I like to use on evert project pre-installed. I clone it to start new projects.

Quick summary of what I've done:

 - Wordpress itself is a submodule under /wordpress. This makes gitting, sorting out wordpress upgrades, etc much easier.
 - wp-content is not within WordPress, it's been moved to wordpress–content, which makes it possible to keep WordPress itself out of version control but still version control themes/plugins.
 
## Vagrant

If you don't know what vagrant is - it's a tool for automating the creation and configuration of virtual machines for development - see http://vagrantup.com/

If you don't want to use vagrant - ignore this section. You can delete the .puppet folder and the file called Vagrantfile.

If you do want to use vagrant... This repo has got my VagrantPuppet repo in as a submodule, and includes a vagrantfile. 

You should be able to clone this repo, run 'vagrant up', and have a dev environment ready to go - accessible at http://wpsite.vcap.me:8080/

(It will need to download my base box the first time you vagrantup. Currently a few hundred MB.)

You can customise the config of the development machine in:

     .puppet/manifests/base.pp

