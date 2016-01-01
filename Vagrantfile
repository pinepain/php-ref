# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box     = "wily64"
  config.vm.box_url = "https://cloud-images.ubuntu.com/vagrant/wily/current/wily-server-cloudimg-amd64-vagrant-disk1.box"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network  "private_network", ip: "192.168.33.102"

  # NOTE: temporary workaround
  #config.vm.hostname = "php-weak.vagrant"
  config.vm.provision :shell, inline: "hostnamectl set-hostname php-weak.vagrant"

  config.ssh.insert_key = false

  config.vm.synced_folder ".", "/home/vagrant/php-weak"

  config.vm.provider "virtualbox" do |vb|
    # Don't boot with headless mode
    #vb.gui = true

    #vb.customize ["modifyvm", :id, "--memory", 2048]
    #vb.customize ["modifyvm", :id, "--cpus", 4]
  end

  config.vm.provision "shell", path: './provision/provision.sh', privileged: false
end
