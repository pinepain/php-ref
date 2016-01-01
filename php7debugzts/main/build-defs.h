/*                                                                -*- C -*-
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2016 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Stig Sæther Bakken <ssb@php.net>                             |
   +----------------------------------------------------------------------+
*/

/* $Id$ */

#define CONFIGURE_COMMAND " './configure'  '--with-apxs2=/usr/bin/apxs2' '--enable-zend-signals' '--with-gd' '--with-jpeg-dir=/usr' '--with-png-dir=/usr' '--with-vpx-dir=/usr' '--with-freetype-dir=/usr' '--with-t1lib=/usr' '--enable-gd-native-ttf' '--enable-exif' '--with-config-file-path=/etc/php71' '--with-config-file-scan-dir=/etc/php71/conf.d' '--with-mysql-sock=/var/run/mysqld/mysqld.sock' '--with-zlib' '--enable-phpdbg' '--with-gmp' '--with-zlib-dir=/usr' '--with-gettext' '--with-kerberos' '--with-imap-ssl' '--with-mcrypt=/usr/local' '--with-iconv' '--enable-sockets' '--with-openssl' '--with-pspell' '--with-pdo-mysql=mysqlnd' '--with-pdo-sqlite' '--with-pgsql' '--with-pdo-pgsql' '--enable-soap' '--enable-xmlreader' '--enable-phar=shared' '--with-xsl' '--enable-ftp' '--enable-cgi' '--with-curl=/usr' '--with-tidy' '--with-xmlrpc' '--enable-mbstring' '--enable-sysvsem' '--enable-sysvshm' '--enable-shmop' '--with-readline' '--enable-pcntl' '--enable-fpm' '--with-fpm-systemd' '--enable-intl' '--enable-zip' '--with-imap' '--with-mysqli=mysqlnd' '--enable-calendar' '--enable-bcmath' '--enable-opcache-file' '--enable-debug' '--enable-maintainer-zts' '--prefix=/usr/local/php71-debug-zts'"
#define PHP_ADA_INCLUDE		""
#define PHP_ADA_LFLAGS		""
#define PHP_ADA_LIBS		""
#define PHP_APACHE_INCLUDE	""
#define PHP_APACHE_TARGET	""
#define PHP_FHTTPD_INCLUDE      ""
#define PHP_FHTTPD_LIB          ""
#define PHP_FHTTPD_TARGET       ""
#define PHP_CFLAGS		"$(CFLAGS_CLEAN) "
#define PHP_DBASE_LIB		""
#define PHP_BUILD_DEBUG		" -Wall"
#define PHP_GDBM_INCLUDE	""
#define PHP_IBASE_INCLUDE	""
#define PHP_IBASE_LFLAGS	""
#define PHP_IBASE_LIBS		""
#define PHP_IFX_INCLUDE		""
#define PHP_IFX_LFLAGS		""
#define PHP_IFX_LIBS		""
#define PHP_INSTALL_IT		"$(mkinstalldirs) '$(INSTALL_ROOT)/usr/lib/apache2/modules' &&                 $(mkinstalldirs) '$(INSTALL_ROOT)/etc/apache2' &&                  /usr/bin/apxs2 -S LIBEXECDIR='$(INSTALL_ROOT)/usr/lib/apache2/modules'                        -S SYSCONFDIR='$(INSTALL_ROOT)/etc/apache2'                        -i -a -n php7 libphp7.la"
#define PHP_IODBC_INCLUDE	""
#define PHP_IODBC_LFLAGS	""
#define PHP_IODBC_LIBS		""
#define PHP_MSQL_INCLUDE	""
#define PHP_MSQL_LFLAGS		""
#define PHP_MSQL_LIBS		""
#define PHP_MYSQL_INCLUDE	"@MYSQL_INCLUDE@"
#define PHP_MYSQL_LIBS		"@MYSQL_LIBS@"
#define PHP_MYSQL_TYPE		"@MYSQL_MODULE_TYPE@"
#define PHP_ODBC_INCLUDE	""
#define PHP_ODBC_LFLAGS		""
#define PHP_ODBC_LIBS		""
#define PHP_ODBC_TYPE		""
#define PHP_OCI8_SHARED_LIBADD 	""
#define PHP_OCI8_DIR			""
#define PHP_OCI8_ORACLE_VERSION		""
#define PHP_ORACLE_SHARED_LIBADD 	"@ORACLE_SHARED_LIBADD@"
#define PHP_ORACLE_DIR				"@ORACLE_DIR@"
#define PHP_ORACLE_VERSION			"@ORACLE_VERSION@"
#define PHP_PGSQL_INCLUDE	""
#define PHP_PGSQL_LFLAGS	""
#define PHP_PGSQL_LIBS		""
#define PHP_PROG_SENDMAIL	"/usr/sbin/sendmail"
#define PHP_SOLID_INCLUDE	""
#define PHP_SOLID_LIBS		""
#define PHP_EMPRESS_INCLUDE	""
#define PHP_EMPRESS_LIBS	""
#define PHP_SYBASE_INCLUDE	""
#define PHP_SYBASE_LFLAGS	""
#define PHP_SYBASE_LIBS		""
#define PHP_DBM_TYPE		""
#define PHP_DBM_LIB		""
#define PHP_LDAP_LFLAGS		""
#define PHP_LDAP_INCLUDE	""
#define PHP_LDAP_LIBS		""
#define PHP_BIRDSTEP_INCLUDE     ""
#define PHP_BIRDSTEP_LIBS        ""
#define PEAR_INSTALLDIR         "/usr/local/php71-debug-zts/lib/php"
#define PHP_INCLUDE_PATH	".:/usr/local/php71-debug-zts/lib/php"
#define PHP_EXTENSION_DIR       "/usr/local/php71-debug-zts/lib/php/extensions/debug-zts-20151012"
#define PHP_PREFIX              "/usr/local/php71-debug-zts"
#define PHP_BINDIR              "/usr/local/php71-debug-zts/bin"
#define PHP_SBINDIR             "/usr/local/php71-debug-zts/sbin"
#define PHP_MANDIR              "/usr/local/php71-debug-zts/php/man"
#define PHP_LIBDIR              "/usr/local/php71-debug-zts/lib/php"
#define PHP_DATADIR             "/usr/local/php71-debug-zts/share/php"
#define PHP_SYSCONFDIR          "/usr/local/php71-debug-zts/etc"
#define PHP_LOCALSTATEDIR       "/usr/local/php71-debug-zts/var"
#define PHP_CONFIG_FILE_PATH    "/etc/php71"
#define PHP_CONFIG_FILE_SCAN_DIR    "/etc/php71/conf.d"
#define PHP_SHLIB_SUFFIX        "so"
