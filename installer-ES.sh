#!/bin/bash
migrar_php(){

sudo apt-get purge `dpkg -l | grep php| awk '{print $2}' |tr "\n" " "`

sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install php5.6
sudo apt-get install php5.6-mbstring php5.6-mcrypt php5.6-mysql php5.6-xml php5.6-curl php5.6-memcached
sudo apt-get install unzip

sudo a2dismod php7.0
sudo a2enmod php5.6
sudo service apache2 restart
sudo update-alternatives –set php /usr/bin/php5.6


}

download(){
    if [ -z "${GVERSION}" ]; then
        GVERSION='master'
        wget "${LAST}"  -O ./last &> /dev/null
        if [ ${?} -ne 0 ]; then
         echo "\n ERROR buscando última versión estable."
        fi
        GVERSION="$(cat ./last | xargs )"
    fi
    
    if [ -f ./hontza.zip ];then
        rm ./hontza.zip
    fi
    if [ -d "hontza-source" ]; then
        rm -r hontza-source
    fi
    
     echo "..*Descargar fuente de GitHub"
    

    wget "${REPO}/${GVERSION}.zip"  -O "${WORKFOLDER}"/hontza.zip &> /dev/null
    ## wget "https://github.com/CDE-Inteligencia-Competitiva/hontza/archive/master.zip" -O "${WORKFOLDER}"/hontza.zip &> /dev/null
     if [ ${?} -ne 0 ]; then
     echo "ERROR descargando Hontza del servidor."
     echo "Url de descarga no valida ${REPO}/${GVERSION}.zip"
     exit 1
    fi
    
    echo "..*Extraer ficheros del fichero hontza.zip"
    unzip -qq -u hontza.zip
    if [ ${?} -ne 0 ]; then
     echo " ERROR!!. Error de Unzip."
     exit 1
    fi
    
    if [ ! -d "./hontza-${GVERSION}" ]; then
        echo "Error. Ficheros inesperados"
        exit 1
    fi
    mv "./hontza-${GVERSION}/src" './hontza-source'
##      mv "RAIZ DONDE COPIARAS LA CARPETA QUE DESCARGES EN EL SERVIDOR (PUEDE SER /HOME)/hontza-5.6/src" './hontza-source'
    
}

install_packages(){

    if [ -n "$(which apt-get 2>/dev/null)" ]; then
        apt-get update
        apt-get -y -f install unzip zip apache2 php-gettext php5-mcrypt php-crypt-blowfish php5-mysql libapache2-mod-php5 mysql-server mysql-client php5-mysql php5-curl php5-mcrypt php-pear php-xml-dtd php-xml-htmlsax3 php-xml-parser php-xml-rpc php-xml-rpc2 php-xml-rss php-xml-serializer php5-cli php5-common php5-gd php5-imap php5-json php5-memcache  php5-memcached php5-readline php5-xmlrpc php5-xsl
        php5enmod mcrypt
        
    elif [ -n "$(which yum 2>/dev/null)" ]; then
        
        if [ -z "$(yum search 'mysql-server'|grep -v "====" |grep mysql-server)" ]; then
            wget http://repo.mysql.com/mysql-community-release-el7-5.noarch.rpm
            rpm -ivh mysql-community-release-el7-5.noarch.rpm
            #yum update
        fi

        yum -y install epel-release
        yum -y install mysql-server httpd php php-php-gettext mysql-server php-mysql install php-mcrypt php-mbstring php-gd php-xml php-mysql php-pear php-xmlrpc php-snmp php-soap curl curl-devel
    
        systemctl start httpd.service
        systemctl enable httpd.service
        systemctl start mysqld
        service mysqld start
        
        if [ -n "$(ps -fe | firewalld )" ]; then
            firewall-cmd --permanent --zone=public --add-service=http 
            firewall-cmd --permanent --zone=public --add-service=https
            firewall-cmd --reload
        fi
                
    else
        echo
        echo "No se ha encontrado instalador de paquetes"
        echo 
        exit 1
    fi
    
}

reboot_apache(){
    if [ -n "$(which apache2)" ];then
        service apache2 restart
    elif  [ -n "$(which httpd)" ];then
        service httpd restart
    fi
     
}

rootUser(){
    ROOTUSER='root'
    echo
    echo  ".....Cuál es el Username con derechos de admin en la base de datos? [${ROOTUSER}]"
    read BUF
    if [ -n "$BUF" ]
    then
      ROOTUSER="$BUF"
    fi

    ROOTPWD=''
    echo ".....Password de ${ROOTUSER} ?"

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
      echo "Los datos aportados no son correctos, por favor inténtalo con un userid o password diferente; de otro modo ${ROOTUSER} no tiene suficientes derechos."
      echo "Proceso de instalación de Hontza parado"
      exit 1
    fi
}

createDB(){
    #set +e
    RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
    I=$(echo -e $RES |grep $DB )
    if [ -n "$I" ]
    then
        echo
        echo  "La base de datos ${DB} existe y ${DBUSER} puede acceder a ella."
        return
    else

      echo -e "\n.....la base de datos ${DB} no existe; de otro modo ${DBUSER} no puede acceder a ella. Intentaré crear ${DB} en caso de que no exista."
      CRE=$(echo "CREATE DATABASE ${DB} ;"  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u $DBUSER 2>/dev/null )
      if [ $? != 0 ]
      then
        echo ".....${DBUSER} no puede crear ${DB} ."

        if [ -z "${ROOTUSER}" ]
        then
            rootUser
        fi

        RES=$(echo "SHOW DATABASES;  " | mysql --defaults-extra-file=./${DBROOT_F} -h localhost  -u $ROOTUSER 2>/dev/null )
        I=$(echo -e $RES |grep $DB )
        if [ -n "$I" ]
        then
            echo ".....la base de datos ${DB} existe !!"
        else
            CRE=$(echo "CREATE DATABASE ${DB} ;"  | mysql --defaults-extra-file=./${DBROOT_F} -h localhost -u $ROOTUSER )
            if [ $? != 0 ]
            then
                echo "....${ROOTPWD} no puede crear la Base de datos."
                exit 1
            else
                echo ".....la base de datos ${DB} ha sido creada."
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
        echo "...${DBUSER} no puede acceder a la base de datos ${DB} . Intentaré crear ${DBUSER} en caso de que no exista."
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
        echo "...El usuario \"${DBUSER}\" no pudo ser creado en MySQL; de otro modo no tiene suficientes derechos."
        echo "Proceso de instalación de Hontza parado"
        exit 1
    fi
    
    #set -e
}


importDB(){
    #set +e
    echo "...importar::  ./hontza-source/db/hontza_blanco.sql"    
    if [ ! -f ./hontza-source/db/hontza_blanco.sql ]; then
        echo "\nERROR!!.   No se encuentra el fichero Sql."
        exit 1
    fi
    mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB} < "${WORKFOLDER}"/hontza-source/db/hontza_blanco.sql
    #set -e
}

addCron(){
    if [ -z "${VHN}" ] || [ -n "$(echo ${VHN} |grep '127.0' )" ]; then
        VHN=$(ifconfig | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1'|head -1)
    fi
    
    
    wget -O /dev/null http://${VHN}/${WEBFOLDER}/cron.php &> /dev/null
    if [ ${?} -ne 0 ]; then
        if [ -n "$(which getenforce)" ]; then
            if [ "$(getenforce )" = 'Enforcing' ];then
                echo "WARNING!!!. You have SELinux activated, and SELinux must be configure to permit Hontza."
                echo "Now, to continue installation, I change setenforce to Permissive."
                echo "Are you agree? ( yes/[NO]"
                if [ "$CONTINUE" != "yes" ]
                then
                  echo "Abortando instalación Hontza."
                  echo
                  exit
                fi
                /usr/sbin/setenforce Permissive
            else
                echo "Parece que \"http://${VHN}/${WEBFOLDER}/cron.php\" no es una url válida. Estás seguro de que \"${WEBROOT}\" y \"${WEBOLDER}\" son caminos correctos? (Ctrl+X para salir, return para continuar)"
                read BUF
            fi
        fi
    fi
    
    CMD='0 * * * * wget -O /dev/null http://'${VHN}'/'${WEBFOLDER}'/cron.php &> /dev/null
15,30,45 * * * * wget http://'${VHN}'/'${WEBFOLDER}'/hontza_solr/indexar 2>&1 > /dev/null
15,30,45 * * * * wget http://'${VHN}'/'${WEBFOLDER}'/cron_google_sheet.php 2>&1 > /dev/null
#5,35,50 * * * * lynx -dump http://'${VHN}'/'${WEBFOLDER}'/red/solr/apachesolr_index_batch_index_remaining 2>&1 > /dev/null' 

    cat <(crontab -l|grep -v "cron.php\|indexar\|apachesolr" ) <(echo "${CMD}") | crontab -
}

moveFiles(){
    if [ -f ./dmy.php ];then
        ./dmy.php
    fi
    chmod -R 777 ./hontza-source/sites/default/files
    cat ./hontza-source/sites/default/settings.php |sed "s/db_url\['default'\].*$/db_url = 'mysql\:\/\/${DBUSER}\:${DBPWD}@localhost\/${DB}'\;/" > ./dmy.php
    mv ./dmy.php ./hontza-source/sites/default/settings.php
    cp -a ./hontza-source  "${WEBROOT}"/"${WEBFOLDER}"
    if [ ${?} -ne 0 ]; then
        echo "Error copiando ficheros a las carpetas del servidor web"
        echo "Proceso de instalación de Hontza parado"
        exit 1
    fi
    ln -s "${WEBROOT}/${WEBFOLDER}/help_popup.php" "${WEBROOT}/help_popup.php"
    
    
}

webserver(){
    
    APACHE=$(apachectl -V)
    if [  ${?} -ne 0 ]; then
        echo "ERROR !! Parece que no hay un servidor Apache. Este instalador sólo funciona con un servidor web Apache."
        echo "Proceso de instalación de Hontza parado"
        echo
        exit 1
    fi
    
    if [ -z "$(apachectl configtest 2>&1 |grep -i 'syntax ok')" ]; then
        echo "Parece que el fichero de configuración de Apache tiene algun error. No puedo continuar."
        echo "Proceso de instalación de Hontza parado"
        echo "Probablemente Hontza ya está instalado."
        echo "Pero el módulo rewrite está desactivado, el parámetro \"AllowOverride All\" no ha sido establecido y las urls limpias están desactivadas" 
        echo
        return 
    fi
            
    echo "Permitir el modulo rewrite del servidor web"
    if [ -n "$(which a2enmod)" ]; then
        a2enmod rewrite
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
'
    
        
    VMLIST=$(apachectl -t -D DUMP_VHOSTS 2>/dev/null |egrep '\(.*\:[0-9]+\)')
    
    if [ -z "${VMLIST}" ] && [ -f /etc/httpd/conf/httpd.conf ]; then
        BB1=$(cat /etc/httpd/conf/httpd.conf | sed -n '/<Director/,/<\/Directory/p'|grep AllowOverride |egrep -w "All" )
        if [ -z "$BB1" ]; then
            echo -e "${LL}" >> /etc/httpd/conf/httpd.conf
            reboot_apache
            return
        fi
    elif [ -z "${VMLIST}" ]; then
        echo "No se ha podido encontrar información sobre los servidores virtales de Apache"
        echo "Asegurese de poner parámetro \"AllowOverride All\ en su servideo de Apache"
        return 
    fi
    
    
    OLDIFS=$IFS
    MAX=$(echo -e "${VMLIST}" |wc -l )
    
    if [ ${MAX} -gt 1 ]; then
        IFS=$'\n'
        I=1
        echo
        echo "Estos son los host virtuales Apache en tu servidor."
        for VH in ${VMLIST}
        do
            echo " ${I} ) ${VH}"
            I=$(expr $I + 1)
            
        done
        IFS=$OLDIFS
        MAX=$(echo -e "${VMLIST}" |wc -l )
        echo "Cuál deseas usar para Hontza? ( 1-${MAX})"
        read VHD
        re='^[0-9]+$'
        while ! [[ ${VHD} =~ $re ]] || [ ${VHD} -gt ${MAX} ] || [ ${VHD} -lt 1 ]
        do
            echo "Opción no válida."
            echo "Cuál deseas usar para Hontza? ( 1-${MAX})"
            read VHD
        done
    else
        VHD=1
    fi
    FF=$(echo -e "${VMLIST}" |sed "${VHD}q;d" )
    VHF=$(expr "${FF}" : '.*(\([^_]*\)\:.*') 
    VHN=$( echo "${FF}" | awk '{print $(NF-1)}')
    
    echo "   Nombre de host Apache ${VHN}"
    
    VHF_BACK="${VHF}.back"
    BB1=$(cat ${VHF} | sed -n '/<Director/,/<\/Directory/p'|grep AllowOverride )
    BB2=$(cat ${VHF} | sed -n '/<Director/,/<\/Directory/p'|grep AllowOverride |egrep -wv "All" )
    if [ -n "$BB1" ] && [ -z "$BB2" ]; then
        echo "It seems clean urls are enabled."
        echo "Hontza are ready to run."
        return
    fi
    LL=$(echo -e "${LL}\n</VirtualHost>")

    SS=$(cat ${VHF} |replace '</VirtualHost>' "${LL}" )
    
    echo
    echo "Voy a crear una copia de seguridad del servidor virtual Apache con el nombre \"${VHF_BACK}\","
    echo "y luego, voy a modificar \"${VHF}\" para permitir las urls limpias. Estás de acuerdo? ( yes / [NO] )" 
    read CONTINUE
    if [ "$CONTINUE" != "yes" ]
    then
      echo "Proceso de instalación de Hontza parado"
      echo "Probablemente Hontza ya está instalado y listo para funcionar."
      echo "Pero el parámetro \"AllowOverride All\" no ha sido establecido y las urls limpias éstán desactivadas" 
      echo "Puedes activar en Apache las urls limpias manualmente"
      echo
      return 
    fi
    cp -b ${VHF} ${VHF_BACK}
    if [ ${?} -ne 0 ]; then
        echo "No puedo copiar \"${VHF}\"  a  \"${VHF_BACK}"
        echo "No puedo continuar este proceso si el la copia de seguridad no se ha creado"
        echo "Probablemente Hontza está instalado y listo para funcionar, y puedes activar en Apache las urls limpias manualmente"
        echo 
        return 
    fi
        
    echo -e "${SS}" > ${VHF}
    
    if [ -z "$(apachectl configtest 2>&1 |grep -i 'syntax ok')" ]; then
        echo "Parece que el fichero de configuración de Apache se ha desconfigurado con los cambios. Deshago los cambios."
        cp ${VHF_BACK} ${VHF}
        
        echo "Proceso de instalación de Hontza parado"
        echo "Probablemente Hontza está instalado y listo para funcionar."
        echo "Pero el módulo rewrite está desactivado y el parámetro \"AllowOverride All\" no ha sido establecido y las urls limpias están desactivadas" 
        echo
        return
    fi
   
    reboot_apache
    
}

function reboot_tomcat(){
    I=0
    while [ -n "$(ps -fe |grep -v grep |grep tomcat)" ]; do
        echo "Parar tomcat..."
        /usr/local/tomcat/bin/shutdown.sh 2>&1 > /dev/null
        sleep 2
        if [ $I -gt 5 ]; then
            kill -9  $(ps -fe |grep -v grep |grep tomcat |awk '{print $2}') 2>&1 > /dev/null
            sleep 2 
        fi
        if [ $I -gt 15 ]; then
            echo "No puedo reiniciar Tomcat."
            echo "Abortando instalación Hontza."
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
    echo "  Instalar java"
    echo "Hontza usa SOLR (http://lucene.apache.org/solr/) como motor de búsqueda."
    echo "SOLR necesita java y Tomcat"
    echo "Deseas instalar JAVA y continuar? ( yes/[NO] )"
    read CONTINUE

    if [ "$CONTINUE" != "yes" ]
    then
      echo "Proceso de instalación de Hontza parado"
      echo "Hontza está instaldo y listo para funcionar, pero no puede usar el motor de búsqueda SOLR."
      echo
      exit
    fi
 
    if [ -n "$(which apt-get 2>/dev/null)" ]; then 
        apt-get -y -f install openjdk-8-jdk
    elif [ -n "$(which yum 2>/dev/null)" ]; then
        yum -y install java-1.7.0-openjdk
    else
        echo
        echo "No se ha encontrado instalador de paquetes"
        echo 
        exit 1
    fi
       
    
    echo "  Descargar e instalar Tomcat"
    echo
    useradd -Mb /usr/local/ tomcat
    wget http://apache.rediris.es/tomcat/tomcat-7/v${TOMCAT_VERSION}/bin/apache-tomcat-${TOMCAT_VERSION}.tar.gz
    if [  ${?} -ne 0 ]; then 
        echo "Error descargando los ficheros de Tomcat"
        echo "No puedo continuar"
        exit 1
    fi
     
    tar -C /usr/local -zxf apache-tomcat-${TOMCAT_VERSION}.tar.gz
    if [ ${?} -ne 0 ]; then 
        echo "Error extrayendo Tomcat"
        echo "No puedo continuar"
        exit 1
    fi
    if [ ! -d /usr/local/apache-tomcat-"${TOMCAT_VERSION}" ]; then
        echo "Error inesperado extrayendo ficheros en Tomcat"
        echo "No puedo continuar"
        exit 1
    fi
    mv /usr/local/apache-tomcat-${TOMCAT_VERSION}  /usr/local/tomcat
    if [ ${?} -ne 0 ]; then
        echo "Error moviendo ficheros de Tomcat"
        echo "No puedo continuar"
        exit 1
    fi

    sed -i s/8080/8983/g /usr/local/tomcat/conf/server.xml

    chown -R tomcat:tomcat /usr/local/tomcat
    
    
    reboot_tomcat

}

function install_solr(){
    echo
    echo "  Descarga e instalación de SOLR"
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
    echo "  Descarga e instalación del MODULO DE DRUPAL PARA SOLR"
    echo

    wget http://ftp.drupal.org/files/projects/apachesolr-6.x-1.8.tar.gz
    tar -zxf apachesolr-6.x-1.8.tar.gz

    rsync -av apachesolr/*  /usr/local/tomcat/solr/conf/
    cp hontza-source/db/solrconfig.xml /usr/local/tomcat/solr/conf/
    cp hontza-source/db/schema.xml /usr/local/tomcat/solr/conf/

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
    echo "UPDATE apachesolr_environment SET url='http://hontza:${SOLRPASS}@localhost:8983/solr/hontza'  WHERE env_id='solr' "  | mysql --defaults-extra-file=./${DBUSER_F} -h localhost -u ${DBUSER} ${DB}

    reboot_tomcat
    
    clean_cache
    
    reboot_apache

}

function clean_cache(){
    echo 
    echo ".....Limpiar cache"
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

function activar_demonio_solr(){
	echo "activar demonio de solr"
	cp "${WORKFOLDER}"/hontza-source/sites/all/modules/apachesolr/tomcat /etc/init.d/. 
	/etc/init.d/tomcat restart
}

function mysql_conf(){
echo "cambiar configuración de mysql para desactivar only full group by"
cp "${WORKFOLDER}"/hontza-source/db/mysql.cnf /etc/mysql/conf.d/mysql.cnf
service mysql restart

}


######################################################
#
#       MAIN
#
#######################################################

WORKFOLDER=/tmp/hwork
REPO="https://github.com/CDE-Inteligencia-Competitiva/hontza/archive"
LAST="http://www.hontza.es/last"
GVERSION="${1}"
DB='hontza'
DBUSER='hontza'
DBPWD='hontza'
WEBROOT='/var/www/html'
WEBFOLDER='hontza'
DBUSER_F='mysuser'
DBROOT_F='myroot'
TOMCAT_VERSION='7.0.91'


#set +e

echo
echo "..............SCRIPT DE INSTALACION DEL SERVIDOR DE HONTZA.........."
echo "Este script intenta instalar los paquetes de Hontza, y luego intenta configurarlos."
echo "Si lo prefieres, puedes instalar Hontza manualmente siguiendo las instrucciones disponibles en:"
echo " https://github.com/CDE-Inteligencia-Competitiva/hontza "
echo
echo "Deseas continuar? ( yes/[NO] )"
read CONTINUE

if [ "$CONTINUE" != "yes" ]
then
  echo "Abortando instalación Hontza."
  echo
  exit
fi

INSTALLER=$0
IPATH=$(dirname "${INSTALLER}");
if [ "$(pwd )" != "${IPATH}" ]
then
    cd $IPATH
fi

echo -e "Es necesario una carpeta temporal para el proceso de instalación. Se creará en esta dirección:  [${WORKFOLDER}]"
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
echo "0.- Migrar del php7 a php5.6."
echo "---------------------------------------------------------------------"
echo
migrar_php

echo
echo "---------------------------------------------------------------------"
echo "1.- Instalar dependencias."
echo "---------------------------------------------------------------------"
echo
install_packages


echo 
echo "---------------------------------------------------------------------"
echo "2.- Descargar Hontza del repositorio."
echo "---------------------------------------------------------------------"
echo
download

echo
echo "---------------------------------------------------------------------"
echo "3. Configurar la Base de Datos"
echo "---------------------------------------------------------------------"
echo 

echo
echo "-Nombre de la base de datos de Hontza: [${DB}]"
read BUF
if [ -n "$BUF" ]
then
  DB="$BUF"
fi
       
echo "Password para el usuario \"${DBUSER}\" de la base de datos \"${DB}\":"
read BUF
if [ -n "$BUF" ]
then
  DBUSER="$BUF"
fi
        
echo "\"${DBUSER}\" user password for \"${DB}\"?"

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
  echo -e "\nI can't query DB. May be \"${DBUSER}\" not exists yet."
fi


echo
echo "...*Creating database"
createDB

echo
echo "...*Importando el contenido de la base de datos"
createDBUser

echo
echo "...*Import database content"
importDB

echo
echo "---------------------------------------------------------------------"
echo "4. Mover ficheros a la raiz de la web. Si es necesario, se crearán carpetas."
echo "---------------------------------------------------------------------"
echo

echo
echo "Dirección de la raiz del servidor web: [${WEBROOT}]"
read BUF
if [ -n "$BUF" ]
then
  WEBROOT="$BUF"
fi

if [ ! -d "${WEBROOT}" ]; then
    echo "La raiz del servidor web no existe. Estás seguro de que \"${WEBROOT}\" es una dirección correcta? ( yes/[NO] )"
    read CONTINUE
    if [ "$CONTINUE" != "yes" ]
    then
      echo "Proceso de instalación de Hontza parado"
      echo
      exit
    fi
fi
if [ -n "$BUF" ]
then
  WEBFOLDER="$BUF"
fi

echo
echo "Nombre de la carpeta Web: [${WEBFOLDER}]"
read BUF
if [ -n "$BUF" ]
then
  WEBFOLDER="$BUF"
fi

moveFiles

echo
echo "---------------------------------------------------------------------"
echo "5. Añadir un trabajo nuevo a crontab."
echo "---------------------------------------------------------------------"
echo
addCron

echo
echo "---------------------------------------------------------------------"
echo "6. Configurar servidor web."
echo "---------------------------------------------------------------------"
echo
webserver

addCron

echo
echo "---------------------------------------------------------------------"
echo "7. INSTALAR SOLR."
echo "---------------------------------------------------------------------"
echo
install_tomcat
install_solr
install_drupal_solr
activar_demonio_solr

echo
echo "---------------------------------------------------------------------"
echo "8. CAMBIAR CONFIGURACIÓN MYSQL"
echo "---------------------------------------------------------------------"
echo
mysql_conf

echo
echo
echo "Disfrutalo!"
echo

