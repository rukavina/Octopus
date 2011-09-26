# OCTOPUS

## Overview

Octopus is Zend Framework PHP web application for X10 Home Automation.

Basically application manages scenes which can have a camera video as a background and X10 devices as widgets so you can execute commands directly from widgets.
In the background there's a running engine based on RabbitMQ/Mochad which send/receives X10 "events" and handles them.

![Octopus Scene](https://github.com/rukavina/Octopus/blob/master/docs/images/demo/octopus-def.png?raw=true "Example Scene with 3 Widgets")

![Octopus Dialog](https://github.com/rukavina/Octopus/blob/master/docs/images/demo/octopus-dialog.png?raw=true "Widget Dialog")


Octopus uses the following technologies:

* Apache
* MySQL
* [RabbitMQ](http://sourceforge.net/projects/mochad/)
* [Mochad](http://sourceforge.net/projects/mochad/)
* PHP Zend Framework
* JQuery / JQuery UI

![Component Diagram](https://github.com/rukavina/Octopus/blob/master/docs/images/demo/octopus-components.png?raw=true "Component Diagram")

![Database Model](/https://github.com/rukavina/Octopus/blob/master/docs/images/demo/octopus-db.png?raw=true "Database Model")

## Installation


### install apache php mysql - skip if installed already

    apt-get install apache2 php5-mysql libapache2-mod-php5 mysql-server phpmyadmin php5-cli

### mochad - x10 software

    apt-get install libusb-1.0-0-dev

download and install mochad .gz

    tar xzf mochad-...tar.gz
    cd mochad-...
    ./configure
    make
    sudo make install

### rabbitmq

    apt-get install rabbitmq-server

### zend-framework

download in /usr/share/php/libzend/ and update php.ini (apache and cli): include_path - add /usr/share/php/libzend

octopus
### 

Download from https://github.com/rukavina/Octopus to ex. /opt/octopus

create mysql db octopus from "docs/db/scripts/octopus.sql"

update configs

    cd octopus/application/configs
    sudo cp dist-application.ini application.ini

then configure all settings in application.ini

    cd octopus/public
    sudo cp dist.htaccess .htaccess

create basic users

    htpasswd -c [abs path to]octopus/application/configs/users [username]

update .htaccess line AuthUserFile [abs path to]octopus/application/configs/users

    sudo mkdir /var/log/octopus
    sudo a2enmod rewrite
    sudo vim /etc/apache2/sites-available/octopus

paste from docs/VHOST file

    sudo a2ensite octopus
    sudo /etc/init.d/apache2 restart

    chmod +x /opt/octopus/bin/octopus-pub.sh
    chmod +x /opt/octopus/bin/octopus-sub.sh

    ln -s /opt/octopus/bin/octopus-pub.sh /usr/local/bin/octopus-pub
    ln -s /opt/octopus/bin/octopus-sub.sh /usr/local/bin/octopus-sub
    ln -s /opt/octopus/bin/x10_command.php  /usr/local/bin/octopus-x10

    cp /opt/octopus/docs/etc_init.d_octopus-sub /etc/init.d/octopus-sub
    cp /opt/octopus/docs/etc_init.d_octopus-pub /etc/init.d/octopus-pub
    chmod +x /etc/init.d/octopus-sub
    chmod +x /etc/init.d/octopus-pub
