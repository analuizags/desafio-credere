FROM ubuntu:18.04

ENV DEBIAN_FRONTEND noninteractive

#Install update
RUN apt-get update && apt-get -y upgrade

#Install facilitators
RUN apt-get -y install locate mlocate wget apt-utils

RUN apt-get -y -q install apache2 php libapache2-mod-php php-cli

#PHP Install CURl
RUN apt-get -y -q install curl php-curl

#PHP Intall DOM, Json e XML
RUN apt-get install -y php-dom php-json php-xml

#PHP Install MbString
RUN apt-get install -y php-mbstring

#PHP Install PDO SqLite
#RUN apt-get -y install php-pdo php-pdo-sqlite php-sqlite3

#PHP Install PDO MySQL
RUN apt-get install -y php-pdo php-pdo-mysql php-mysql 

## ------------- Add-ons ------------------
#Install GIT
RUN apt-get -y install -y git-core zip unzip php-zip php7.2-gd

## ------------- Finishing ------------------
RUN apt-get clean

#Creating index of files
RUN updatedb

WORKDIR /var/www/html/
COPY / /var/www/html/

EXPOSE 80
CMD apachectl -D FOREGROUND