#!/bin/bash

hash_file="/tmp/nodejshash"

check_if_npm_packages_has_to_be_installed () {
    if [ -f $hash_file ]; then
        check_if_same_hash
    else
        return 0
    fi
}

check_if_same_hash () {
    hash_new="$(md5sum .ebextensions/bin/install-nodejs.sh 2> /dev/null | cut -d ' ' -f 1)"
    hash_current="$(cat "$hash_file" 2> /dev/null | cut -d ' ' -f 1)"

    if [ $hash_new == $hash_current ]; then
        return 1
    else
        return 0
    fi
}

install_node () {
    if hash nodejs 2> /dev/null; then
        echo 'nodejs install, add more processing if needed' > /dev/null
    else
        # do this (commented) command to remove default nodejs in elastic beanstalk.
        # yum install -y gcc-c++ make
        # yum erase nodejs
        # sudo rm -f /usr/bin/node

        # need to update or change the version of node js. Please follow the link below.
        rpm -Uvh https://rpm.nodesource.com/pub_6.x/el/7/x86_64/nodejs-6.9.4-1nodesource.el7.centos.x86_64.rpm | sudo -E bash -
        yum install -y nodejs
    fi
}

install_npm_packages () {
    #/usr/bin/npm install -g npm@latest
    #echo 'update npm'
    #/usr/bin/npm install -g minimatch@3.0.2
    echo 'install minimatch 3.0.2 globlaly'
    /usr/bin/npm install -g gulp
    echo 'gulp installed' > /dev/null
    /usr/bin/npm install -g gulp-cli
    echo 'gulp-cli installed' > /dev/null
}

update_current_hash () {
    echo $hash_new > $hash_file
}

install_node

if check_if_npm_packages_has_to_be_installed; then
    install_npm_packages
    update_current_hash
fi