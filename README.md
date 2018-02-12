# hontza

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
