#!/bin/bash

wget -q  https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php -O wp-readme.php 
docker run -it --rm --name my-running-script -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:7.4-cli php wp-readme.php
rm wp-readme.php 
