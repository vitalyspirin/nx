#!/bin/bash

if [ -L $0 ] ; then
    ME=$(readlink $0)
else
    ME=$0
fi
DIR=$(dirname $ME)

sudo find $DIR/../../view -type d -exec chgrp http {} \;
sudo find $DIR/../../view -type d -exec chmod g+w {} \;
