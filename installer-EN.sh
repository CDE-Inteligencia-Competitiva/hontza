#!/bin/bash


download(){
    if [ -f ./hontza.zip ];then
        rm ./hontza.zip
    fi
    if [ -d "hontza-master" ]; then
        rm -r hontza-master
    fi
    
    echo "..*Download source from Github"
    
    wget "${REPO}"  -O "${WORKFOLDER}"/hontza.zip &> /dev/null
    if [ ${?} -ne 0 ]; then
     echo "\n ERROR downloading Hontza from server."
     exit 1
    fi
    
    echo "..*Extract files from hontza.zip file"
    unzip -qq -u hontza.zip
    if [ ${?} -ne 0 ]; then
     echo "\n ERROR!!. Unzip error."
     exit 1
    fi
    

}

install_packages(){

    apt-get -y -f install unzip zip apache2 php-gettext php5-mcrypt php-crypt-blowfish php5-mysql libapache2-mod-php5 mysql-server mysql-client php5-mysql php5-curl php5-mcrypt php-pear php-xml-dtd php-xml-htmlsax3 php-xml-parser php-xml-rpc php-xml-rpc2 php-xml-rss php-xml-serializer php5-cli php5-common php5-gd php5-imap php5-json php5-memcache  php5-memcached php5-readline php5-xmlrpc php5-xsl
    
    php5enmod mcrypt
    
}
install_red_hat_packages(){
    yum install php mysql-server php-mysql php-mbstring php-gd
}

rootUser(){
    ROOTUSER='root'
    echo -e ".....Please indicate the Username with admin rights in the database [${ROOTUSER}]"
    read BUF
    if [ -n "$BUF" ]
    then
      ROOTUSER="$BUF"
    fi

    ROOTPWD=''
    echo -e ".....${ROOTUSER} Password?"

    trap "stty echo ; exit" 1 2 15
    stty -echo
    read BUF
    stty echo
    trap "" 1 2 15

    if [ -n "$BUF" ]
    then
      ROOTPWD="$BUF"
    fi
    
    printf "[client]\npassword=${ROOTPWD}" > ./${DBROOT_F}
    RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER  2>/dev/null  )
    if [ $? != 0 ]
    then
      echo
      echo "Entered data are wrong, please retry with a different user or password; otherwise ${ROOTUSER} has not enough rights."
      echo "Hontza installation proccess Stopped"
      exit 1
    fi
}

createDB(){
    #set +e
    RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
    I=$(echo -e $RES |grep $DB )
    if [ -n "$I" ]
    then
        echo -e "\n.....${DB} exists and ${DBUSER} can access to it."
        return
    else

      echo -e "\n.....${DB} does not exist; otherwise ${DBUSER} can not access to it. I will try to create ${DB} in case it does not exist."
      CRE=$(echo "CREATE DATABASE ${DB} ;"  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
      if [ $? != 0 ]
      then
        echo ".....${DBUSER} can't create ${DB} ."

        if [ -z "${ROOTUSER}" ]
        then
            rootUser
        fi

        RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBROOT_F} -h localhost  -u $ROOTUSER 2>/dev/null )
        I=$(echo -e $RES |grep $DB )
        if [ -n "$I" ]
        then
            echo ".....${DB} exists !!"
        else
            CRE=$(echo "CREATE DATABASE ${DB} ;"  | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER )
            if [ $? != 0 ]
            then
                echo "....${ROOTPWD} can't create the Database."
                exit 1
            else
                echo ".....${DB} has been created succesfully."
            fi
        fi
      fi
    fi
    #set -e
}

createDBUser(){
    #set +e
    RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
    I=$(echo -e $RES |grep $DB )
    if [ -z "$I" ]
    then
        echo "...${DBUSER} can not access to ${DB} database. I will try to create ${DBUSER} in case it does not exist."
        if [ -z "${ROOTUSER}" ]
        then
            rootUser
        fi
        US=$(echo "use mysql; SELECT * FROM user WHERE user='${DBUSER}'" | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER )
        if [ -z "${US}" ]
        then
            US1=$(echo "CREATE USER '${DBUSER}'@'localhost' IDENTIFIED BY '${DBPWD}';" | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER )
            US2=$(echo "GRANT USAGE ON * . * TO '${DBUSER}'@'localhost' IDENTIFIED BY '${DBPWD}' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;" | mysql --defaults-extra-file=./${DBROOT_F}  -h localhost -u $ROOTUSER )
        fi
        US3=$(echo "GRANT ALL PRIVILEGES ON ${DB} . * TO '$DBUSER'@'localhost' WITH GRANT OPTION ;" | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER )
    fi
    
    RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
    I=$(echo -e $RES |grep $DB )
    if [ -z "$I" ];then
        echo "...User \"${DBUSER}\" could not be created in MySQL; otherwise it does not have enough rights."
        echo "Hontza installation proccess Stopped"
        exit 1
    fi
    
    #set -e
}


importDB(){
    #set +e
    echo "...import::  ./hontza-master/db/hontza_blanco.sql"    
    if [ ! -f ./hontza-master/db/hontza_blanco.sql ]; then
        echo "\nERROR!!.   Sql file not found."
        exit 1
    fi
    mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB} < "${WORKFOLDER}"/hontza-master/db/hontza_blanco.sql
    #set -e
}

addCron(){
    if [ -z "${VHN}" ] || [ -n "$(echo ${VHN} |grep '127.0' )" ]; then
        VHN="$(/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}')"
    fi
    
    wget -O /dev/null http://${VHN}/${WEBFOLDER}/cron.php &> /dev/null
    if [ ${?} -ne 0 ]; then
        echo "It seens that \"http://${VHN}/${WEBFOLDER}/cron.php\" is not a valid url. Are you sure that \"${WEBROOT}\" and \"${WEBOLDER}\" are correct paths? (Ctrl+X to exit, return to continue)"
        read BUF
    fi
    
    CMD='0 * * * * wget -O /dev/null http://'${VHN}'/'${WEBFOLDER}'/cron.php &> /dev/null
15,30,45 * * * * wget http://'${VHN}'/'${WEBFOLDER}'/hontza_solr/indexar 2>&1 > /dev/null
#5,35,50 * * * * lynx -dump http://'${VHN}'/'${WEBFOLDER}'/red/solr/apachesolr_index_batch_index_remaining 2>&1 > /dev/null' 

    cat <(crontab -l|grep -v "cron.php\|indexar\|apachesolr" ) <(echo "${CMD}") | crontab -
}

moveFiles(){
    if [ -f ./dmy.php ];then
        ./dmy.php
    fi
    chmod -R 777 ./hontza-master/sites/default/files
    cat ./hontza-master/sites/default/settings.php |sed "s/db_url\['default'\].*$/db_url = 'mysql\:\/\/${DBUSER}\:${DBPWD}@localhost\/${DB}'\;/" > ./dmy.php
    mv ./dmy.php ./hontza-master/sites/default/settings.php
    cp -a ./hontza-master  "${WEBROOT}"/"${WEBFOLDER}"
    if [ ${?} -ne 0 ]; then
        echo "Error copying files to web server folder"
        echo "Hontza installation proccess Stopped"
        exit 1
    fi
    ln -s "${WEBROOT}/${WEBFOLDER}/help_popup.php" "${WEBROOT}/help_popup.php"
    
    
}

webserver(){
    
    APACHE=$(apachectl -V)
    if [  ${?} -ne 0 ]; then
        echo "ERROR !! It seems that Apache web server is not running. This installer only runs with Apache web server."
        echo "Hontza installation proccess Stopped"
        echo
        exit 1
    fi
    
    if [ -z "$(apachectl configtest 2>&1 |grep -i 'syntax ok')" ]; then
        echo "It seems that Apache's config file is broken. I can't continue."
        echo "Hontza installation proccess Stopped"
        echo "Probably Hontza is ready to run."
        echo "But rewrite module is off, parameter \"AllowOverride All\" has not been set up and clean urls are disabled" 
        echo
        return 
    fi
            
    echo "Enable web server rewrite module"
    a2enmod rewrite
        
    VMLIST=$(apachectl -t -D DUMP_VHOSTS 2>/dev/null |egrep '\(.*\:[0-9]+\)')
    OLDIFS=$IFS
    MAX=$(echo -e "${VMLIST}" |wc -l )
    
    if [ ${MAX} -gt 1 ]; then
        IFS=$'\n'
        I=1
        echo
        echo "These are the Apache virtual hosts in your server."
        for VH in ${VMLIST}
        do
            echo " ${I} ) ${VH}"
            I=$(expr $I + 1)
            
        done
        IFS=$OLDIFS
        MAX=$(echo -e "${VMLIST}" |wc -l )
        echo "Which one do you want to use for Hontza? ( 1-${MAX})"
        read VHD
        re='^[0-9]+$'
        while ! [[ ${VHD} =~ $re ]] || [ ${VHD} -gt ${MAX} ] || [ ${VHD} -lt 1 ]
        do
            echo "Invalid Option."
            echo "Which one do you want to use for Hontza? ( 1-${MAX})"
            read VHD
        done
    else
        VHD=1
    fi
    FF=$(echo -e "${VMLIST}" |sed "${VHD}q;d" )
    VHF=$(expr "${FF}" : '.*(\([^_]*\)\:.*') 
    VHN=$( echo "${FF}" | awk '{print $(NF-1)}')
    
    echo "   Apache host name ${VHN}"
    
    VHF_BACK="${VHF}.back"
    BB1=$(cat ${VHF} | sed -n '/<Director/,/<\/Directory/p'|grep AllowOverride )
    BB2=$(cat ${VHF} | sed -n '/<Director/,/<\/Directory/p'|grep AllowOverride |egrep -wv "All" )
    if [ -n "$BB1" ] && [ -z "$BB2" ]; then
        echo "It seems that clean urls are enabled."
        echo "Hontza is ready to run."
        return
    fi
    LL='    <Directory '${WEBROOT}'>
        Options Indexes FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>

    <Location /'${WEBFOLDER}'>
        Options Indexes FollowSymLinks
        AllowOverride All
        Order allow,deny
        Allow from all
    </Location>
</VirtualHost>
'   
    SS=$(cat ${VHF} |replace '</VirtualHost>' "${LL}" )
    
    echo
    echo "I am going to create a backup of Apache virtual host with name \"${VHF_BACK}\","
    echo "and then, I am going to modify \"${VHF}\" to enable clean urls. Do you agree? ( yes / [NO] )" 
    read CONTINUE
    if [ "$CONTINUE" != "yes" ]
    then
      echo "Hontza installation proccess Stopped"
      echo "Probably Hontza is ready to run."
      echo "But \"AllowOverride All\" has not been set up and clean urls are disabled" 
      echo "You must enable clean urls in Apache manually"
      echo
      return 
    fi
    cp -b ${VHF} ${VHF_BACK}
    if [ ${?} -ne 0 ]; then
        echo "I can't copy  \"${VHF}\"  to   \"${VHF_BACK}"
        echo "I can't continue the Hontza installation process if backup is not created"
        echo "Probably Hontza is ready to run and you can enable clean urls in Apache manually"
        echo 
        return 
    fi
        
    echo -e "${SS}" > ${VHF}
    
    if [ -z "$(apachectl configtest 2>&1 |grep -i 'syntax ok')" ]; then
        echo "It seems that Apache's config file has been broken with the changes. I revert the changes."
        cp ${VHF_BACK} ${VHF}
        
        echo "Hontza installation proccess Stopped"
        echo "Probably Hontza is ready to run."
        echo "But rewrite module is off and  \"AllowOverride All\" has not been set up and clean urls are disabled" 
        echo
        return
    fi
   
    service apache2 reload
    
}

function reboot_tomcat(){
    I=0
    while [ -n "$(ps -fe |grep -v grep |grep tomcat)" ]; do
        echo "Shutdown tomcat"
        /usr/local/tomcat/bin/shutdown.sh
        sleep 2
        if [ $I -gt 5 ]; then
            kill -9  $(ps -fe |grep -v grep |grep tomcat |awk '{print $2}')
            sleep 2 
        fi
        if [ $I -gt 15 ]; then
            echo "I can't reboot Tomcat."
            echo "Abort hontza Installation."
            exit 1
        fi
        I=$(expr ${I} + 1)
    done
    chown -R tomcat:tomcat /usr/local/tomcat
    sudo -u tomcat /usr/local/tomcat/bin/startup.sh

    sleep 3
}

function install_tomcat(){
    
    echo
    echo "  Install java"
    echo "Hontza uses SOLR (http://lucene.apache.org/solr/) as search engine."
    echo "SOLR needs java and Tomcat"
    echo "Would you like to install JAVA and continue? ( yes/[NO] )"
    read CONTINUE

    if [ "$CONTINUE" != "yes" ]
    then
      echo "Hontza installation proccess Stopped"
      echo "Hontza is ready to run, but it can not use SOLR search engine."
      echo
      exit
    fi

    apt-get -y -f install openjdk-7-jdk
    #yum install java-1.7.0-openjdk
    
    echo "  Download and install Tomcat"
    echo
    useradd -Mb /usr/local/ tomcat
    wget http://apache.rediris.es/tomcat/tomcat-7/v${TOMCAT_VERSION}/bin/apache-tomcat-${TOMCAT_VERSION}.tar.gz
    if [  ${?} -ne 0 ]; then 
        echo "Error downloading Tomcat files"
        echo "I can't continue"
        exit 1
    fi
     
    tar -C /usr/local -zxf apache-tomcat-${TOMCAT_VERSION}.tar.gz
    if [ ${?} -ne 0 ]; then 
        echo "Error extracting  Tomcat"
        echo "I can't continue"
        exit 1
    fi
    if [ ! -d /usr/local/apache-tomcat-"${TOMCAT_VERSION}" ]; then
        echo "Unexpected error extracting files in Tomcat"
        echo "I can't continue"
        exit
    fi
    mv /usr/local/apache-tomcat-${TOMCAT_VERSION}  /usr/local/tomcat
    if [ ${?} -ne 0 ]; then
        echo "Error installing files in Tomcat"
        echo "I can't continue"
        exit 1
    fi

    sed -i s/8080/8983/g /usr/local/tomcat/conf/server.xml

    chown -R tomcat:tomcat /usr/local/tomcat
    
    
    reboot_tomcat

}

function install_solr(){
    echo
    echo "  Download and install SOLR"
    echo
    
    wget http://archive.apache.org/dist/lucene/solr/4.3.0/solr-4.3.0.tgz
    tar -zxf solr-4.3.0.tgz

    cp solr-4.3.0/dist/solrj-lib/* /usr/local/tomcat/lib/
    cp solr-4.3.0/example/resources/log4j.properties /usr/local/tomcat/conf/
    cp solr-4.3.0/dist/solr-4.3.0.war /usr/local/tomcat/webapps/solr.war


    printf '<Context docBase="/usr/local/tomcat/webapps/solr.war" debug="0" crossContext="true">
  <Environment name="solr/home" type="java.lang.String" value="/usr/local/tomcat/solr" override="true" />
</Context>' > /usr/local/tomcat/conf/Catalina/localhost/solr.xml

    mkdir -p /usr/local/tomcat/solr
    cp -r solr-4.3.0/example/solr/collection1/conf /usr/local/tomcat/solr/
    
    reboot_tomcat

}

function install_drupal_solr(){

    echo
    echo "  Download and install DRUPAL MODULE FOR SOLR"
    echo

    wget http://ftp.drupal.org/files/projects/apachesolr-6.x-1.8.tar.gz
    tar -zxf apachesolr-6.x-1.8.tar.gz

    rsync -av apachesolr/*  /usr/local/tomcat/solr/conf/
    cp hontza-master/db/solrconfig.xml /usr/local/tomcat/solr/conf/
    cp hontza-master/db/schema.xml /usr/local/tomcat/solr/conf/

    printf '<?xml version="1.0" encoding="UTF-8" ?>
<solr persistent="false">
  <cores adminPath="/admin/cores">
    <core name="hontza" instanceDir="hontza" />
  </cores>
</solr>' > /usr/local/tomcat/solr/solr.xml

    mkdir /usr/local/tomcat/solr/hontza
    cp -r /usr/local/tomcat/solr/conf /usr/local/tomcat/solr/hontza/
    chown -R tomcat:tomcat /usr/local/tomcat
    
    
    
    LL2='<security-constraint>
    <web-resource-collection>
      <web-resource-name>Restrict access to Solr admin</web-resource-name>
      <url-pattern>/</url-pattern>
      <http-method>GET</http-method>
      <http-method>POST</http-method>
    </web-resource-collection>
    <auth-constraint>
      <role-name>manager-gui</role-name>
    </auth-constraint>
  </security-constraint>

  <login-config>
    <auth-method>BASIC</auth-method>
    <realm-name>wwww?</realm-name>
  </login-config>
</web-app>'

    echo
    if [ -n "$(grep 'wwww?' /usr/local/tomcat/webapps/solr/WEB-INF/web.xml )" ];then
        SS1=$(cat /usr/local/tomcat/webapps/solr/WEB-INF/web.xml |replace '</web-app>' "${LL2}" )
        echo -e "${SS1}" > /usr/local/tomcat/webapps/solr/WEB-INF/web.xml
    fi
    
    SOLRPASS="h${RANDOM}_${RANDOM}"
    
    LL3='<role rolename="manager-gui"/>
    <user username="hontza" password="'${SOLRPASS}'" roles="manager-gui"/>
    </tomcat-users>
    '
    
    echo
    SS2=$(cat /usr/local/tomcat/conf/tomcat-users.xml |grep -v "username=\"hontza\" password=" | grep -v "rolename=\"manager-gui\""  |replace '</tomcat-users>' "${LL3}"  )
    echo -e "${SS2}" > /usr/local/tomcat/conf/tomcat-users.xml
    
    echo "echo \"UPDATE apachesolr_environment SET url='http://hontza:${SOLRPASS}@localhost:8983/solr/hontza'  WHERE env_id='solr' \"  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB}"
    echo "UPDATE apachesolr_environment SET url='http://hontza:${SOLRPASS}@localhost:8983/solr/hontza'  WHERE env_id='solr' "  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB}

    reboot_tomcat
    
    clean_cache
    
    service apache2 restart

}

function clean_cache(){
    echo 
    echo ".....Clean cache"
    echo
    echo 'TRUNCATE cache;
TRUNCATE cache_apachesolr;
TRUNCATE cache_block;
TRUNCATE cache_content;
TRUNCATE cache_filter;
TRUNCATE cache_form;
TRUNCATE cache_l10n_update;
TRUNCATE cache_menu;
TRUNCATE cache_page;
TRUNCATE cache_tax_image;
TRUNCATE cache_update;
TRUNCATE cache_views;
TRUNCATE cache_views_data;
DELETE FROM '${DB}'.variable WHERE variable.name = "red_registrar_is_registrado_cache";'  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB}
}


######################################################
#
#       MAIN
#
#######################################################

WORKFOLDER=/tmp/hwork
REPO="https://github.com/CDE-Inteligencia-Competitiva/hontza/archive/master.zip"
DB='hontza'
DBUSER='hontza'
DBPWD='hontza'
WEBROOT='/var/www/html'
WEBFOLDER='hontza'
DBUSER_F='mysuser'
DBROOT_F='myroot'
TOMCAT_VERSION='7.0.69'


#set +e

echo
echo "..............HONTZA SERVER NEW INSTALLATION SCRIPT.........."
echo "This script tries to install Hontza packages, and then it tries to configure them."
echo "If you prefer, you can install Hontza manually by following the guidelines available at:"
echo " https://github.com/CDE-Inteligencia-Competitiva/hontza "
echo
echo "Would you like to continue? ( yes/[NO] )"
read CONTINUE

if [ "$CONTINUE" != "yes" ]
then
  echo "Hontza installation proccess Stopped"
  echo
  exit
fi

INSTALLER=$0
IPATH=$(dirname "${INSTALLER}");
if [ "$(pwd )" != "${IPATH}" ]
then
    cd $IPATH
fi

echo -e "A temporary folder is necessary for the Hontza installation process. It will be created in this path:  [${WORKFOLDER}]"
read BUF
if [ -n "$BUF" ]
then
  WORKFOLDER="$BUF"
fi

if [ ! -d $WORKFOLDER ]; then
 mkdir $WORKFOLDER
fi

cd ${WORKFOLDER}



echo
echo "---------------------------------------------------------------------"
echo "1.- Install dependencies."
echo "---------------------------------------------------------------------"
echo
install_packages


echo 
echo "---------------------------------------------------------------------"
echo "2.- Download Hontza from repository."
echo "---------------------------------------------------------------------"
echo
download

echo
echo "---------------------------------------------------------------------"
echo "3. Setup DB"
echo "---------------------------------------------------------------------"
echo 

echo
echo "-Hontza database name: [${DB}]"
read BUF
if [ -n "$BUF" ]
then
  DB="$BUF"
fi
       
echo "- \"${DB}\" database username: [${DBUSER}]"
read BUF
if [ -n "$BUF" ]
then
  DBUSER="$BUF"
fi
        
echo "\"${DBUSER}\" user password for \"${DB}\":"

trap "stty echo ; exit" 1 2 15
stty -echo
read BUF
stty echo
trap "" 1 2 15

if [ -n "$BUF" ]
then
  DBPWD="$BUF"
fi

if [ -f ./${DBUSER_F} ]; then
    rm ./${DBUSER_F}
fi
if [ -f ./${DBROOT_F} ]; then
    rm ./${DBROOT_F}
fi

printf "[client]\npassword=${DBPWD}" > ./${DBUSER_F}
#set +e

RES=$(echo "SHOW DATABASES;  " | mysql--defaults-extra-file=./${DBUSER_F} -h localhost -u$DBUSER  &>/dev/null )
if [ $? != 0 ]
then
  echo -e "\nI can't query to DB. May be \"${DBUSER}\" has not been created yet."
fi


echo
echo "...*Creating database"
createDB

echo
echo "...*Creating database user"
createDBUser

echo
echo "...*Importing database content"
importDB

echo
echo "---------------------------------------------------------------------"
echo "3. Move files to web root. If necessary, folders will be created."
echo "---------------------------------------------------------------------"
echo

echo
echo "Web server root path: [${WEBROOT}]"
read BUF
if [ -n "$BUF" ]
then
  WEBROOT="$BUF"
fi

if [ ! -d "${WEBROOT}" ]; then
    echo "Web server root does not exist. Are you sure that \"${WEBROOT}\" is a correct path? ( yes/[NO] )"
    read CONTINUE
    if [ "$CONTINUE" != "yes" ]
    then
      echo "Hontza installation proccess Stopped"
      echo
      exit
    fi
fi
if [ -n "$BUF" ]
then
  WEBFOLDER="$BUF"
fi

echo
echo "Web folder name: [${WEBFOLDER}]"
read BUF
if [ -n "$BUF" ]
then
  WEBFOLDER="$BUF"
fi

moveFiles

echo
echo "---------------------------------------------------------------------"
echo "4. Add new job to crontab."
echo "---------------------------------------------------------------------"
echo
addCron

echo
echo "---------------------------------------------------------------------"
echo "5. Enable clean urls in Apache web server."
echo "---------------------------------------------------------------------"
echo
webserver

addCron

echo
echo "---------------------------------------------------------------------"
echo "6. INSTALL SOLR."
echo "---------------------------------------------------------------------"
echo
install_tomcat
install_solr
install_drupal_solr

echo
echo
echo "Enjoy it!"
echo
#set -e

