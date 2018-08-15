# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "debian/stretch64"
  config.vm.network "private_network", ip: "172.16.0.222"
  config.vm.synced_folder "./", "/vagrant", type: "nfs", nfs: true, :mount_options => ['nolock,vers=3,udp,noatime,actimeo=1']

  # Enable Host io cache
  config.vm.provider "virtualbox" do |v|
      v.customize [
          "storagectl", :id,
          "--name", "SATA Controller",
          "--hostiocache", "on"
      ]
  end

  config.vm.provision "shell", inline: <<-SHELL

    DBNAME=vagrant
    DBUSER=vagrant
    DBPASSWD=vagrant

    sudo apt-get -qq update
    sudo apt-get -yy upgrade

    echo "--- Install base packages ---"
    sudo apt-get -y install curl git mc ntp


    echo "--- Install Apache2 ---"
    sudo apt-get install -y apache2
    sudo sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
    sudo rm -f /etc/apache2/sites-enabled/*
    sudo echo '<VirtualHost *>
            DocumentRoot /vagrant/public_html/
            <Directory /vagrant/public_html/>
                    Options FollowSymLinks
                    AllowOverride All
                    Require all granted
                    php_admin_value memory_limit 128M
                    php_admin_value pcre.recursion_limit 14000
                    php_admin_value upload_max_filesize 8M
                    php_admin_value post_max_size 8M
            </Directory>
    </VirtualHost>' > /etc/apache2/sites-available/www.conf
    sudo ln -s /etc/apache2/sites-available/www.conf /etc/apache2/sites-enabled/www.conf

    echo "--- Enable mod_rewrite ---"
    sudo a2enmod rewrite

    sudo service apache2 restart

    echo "--- Install PHP ---"
    sudo apt-get install -y apt-transport-https ca-certificates
    sudo wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
    sudo sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list'
    sudo apt-get update -y

    sudo apt-get install -y php7.1 libapache2-mod-php7.1 php7.1-curl php7.1-mysqlnd

    echo "--- Apache threaded MPM fix ---"
    # https://bbs.archlinux.org/viewtopic.php?pid=1389601#p1389601
    sudo a2dismod mpm_event
    sudo a2enmod mpm_prefork
    sudo service apache2 restart


    echo "--- Install MySql ---"
    sudo echo "mysql-server mysql-server/root_password password ${DBPASSWD}" | debconf-set-selections
    sudo echo "mysql-server mysql-server/root_password_again password ${DBPASSWD}" | debconf-set-selections
    sudo apt-get install -y mysql-server

    sudo echo '[mysqld]
    innodb_file_per_table
    innodb_buffer_pool_size         = 128M
    innodb_additional_mem_pool_size = 32M
    innodb_file_io_threads          = 2
    innodb_lock_wait_timeout        = 50

    innodb_log_buffer_size          = 5M
    innodb_log_file_size            = 5M

    innodb_flush_log_at_trx_commit  = 2
    innodb_flush_method             = O_DIRECT
    transaction-isolation           = READ-COMMITTED
    bind-address                    = 0.0.0.0
    ' > /etc/mysql/conf.d/vagrant.cnf

    sudo cp /etc/mysql/my.cnf /etc/mysql/my.cnf-dist-orig
    sed -i 's/^skip-networking/#skip-networking/' /etc/mysql/my.cnf

    echo "--- Restart mysql ---"
    sudo service mysql restart

    echo '--- Create schema ---'
    mysql -uroot -p${DBPASSWD} -e "CREATE DATABASE IF NOT EXISTS ${DBNAME} DEFAULT CHARACTER SET utf8"
    mysql -uroot -p${DBPASSWD} -e "grant all privileges on $DBNAME.* to '${DBUSER}'@'%' identified by '${DBPASSWD}'"

  SHELL
end
