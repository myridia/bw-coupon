#± /bin/sh

if [ -f /usr/local/bin/wp2md ] #check if file exists
then
   echo "...wp2md exists, execute it now"
   wp2md -i readme.txt -o README.md
else
   echo "...wp2md does not exist, install it and after execute it"    
   sudo wget https://github.com/wpreadme2markdown/wp2md/releases/latest/download/wp2md.phar -O /usr/local/bin/wp2md
   sudo chmod a+x /usr/local/bin/wp2md
   wp2md -i readme.txt -o README.md
fi


