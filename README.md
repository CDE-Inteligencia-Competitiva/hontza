# hontza

*** Español ***

Hontza es una Plataforma específica para la Inteligencia Competitiva y Estratégica.

Debes instalar Hontza en un servidor LINUX con los repositorios actualizados.

Si deseas instalar Hontza en un ordenador con sistema operativo Windows, debes instalar primero una máquina virtual Linux y luego instalar Hontza en dicha máquina virtual. Para más explicaciones, por favor lee este artículo:
https://www.genbeta.com/paso-a-paso/como-crear-una-maquina-virtual-en-windows-para-ejecutar-linux

Para instalar Hontza en Linux, sólo debes ejecutar INSTALL-ES.sh y seguir los pasos indicados.

El script de instalación gestiona todo el proceso para descargar los fichros necesarios.

Cuando hayas terminado la instalación, debes deshabilitar la opción de mysql "only_full_group_by"

Para ello, debes añadir en /etc/mysql/conf.d/mysql.cnf:

[mysqld]
sql_mode=STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION

... y reiniciar el servidor mysql con sudo service mysql restart

Aquí tienes un vídeo con todo el proceso de instalación
https://www.dropbox.com/s/p702mt4gnxumjw1/hontza_5.6_installation.webm?dl=0

---------------------------
*** English ***

Hontza is a Platform for strategic and competitive intelligence.

Hontza can be installed in any Linux machine with updated repositories.

To install Hontza, you have to execute only INSTALL-ES.sh and follow the steps.

The installation script manages the process to download all the necessary files.

When installation is done, you must disable the mysql "only_full_group_by" option.

For this, you have to add in /etc/mysql/conf.d/mysql.cnf:

[mysqld]
sql_mode=STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION

And restart mysql server with sudo service mysql restart


Here you are a video with all the installation process
https://www.dropbox.com/s/p702mt4gnxumjw1/hontza_5.6_installation.webm?dl=0

