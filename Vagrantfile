# sample vagrantfile - make sure you adjust line 19 - line 23 to point to your puppet manifests.
# i like to put them under '.puppet', so that they are version controlled etc. But do as you please!

Vagrant::Config.run do |config|
  config.vm.guest = :debian
  config.vm.box = "bl-squeeze64"
  config.vm.box_url = "http://benlumley.co.uk/stuff/bl-squeeze64.box"

  # needed for nfs shares
  config.vm.network :hostonly, "33.33.33.10"

  config.vm.forward_port 8080, 8080
  config.vm.forward_port 3306, 3306

  #config.vm.share_folder("v-root", "/vagrant", ".")
  config.vm.share_folder("v-root", "/vagrant", ".", :nfs => true)
 

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = ".puppet/manifests"
    puppet.manifest_file  = "base.pp"
    puppet.module_path = ".puppet/modules"
  end
  
  config.vm.customize ["modifyvm", :id, "--rtcuseutc", "on"]

  config.vm.customize ["modifyvm", :id, "--memory", 512]

end

