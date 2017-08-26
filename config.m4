dnl $Id$
dnl config.m4 for extension ref

PHP_ARG_ENABLE(ref, whether to enable ref support,
dnl Make sure that the comment is aligned:
[  --enable-ref           Enable ref support])

if test "$PHP_REF" != "no"; then

  AC_MSG_CHECKING([Check for supported PHP versions])
  PHP_REF_FOUND_VERSION=`${PHP_CONFIG} --version`
  PHP_REF_FOUND_VERNUM=`${PHP_CONFIG} --vernum`
  
  if test "$PHP_REF_FOUND_VERNUM" -lt "70100"; then
    AC_MSG_ERROR([not supported. PHP version >= 7.1 required (found $PHP_REF_FOUND_VERSION)])
  else
    AC_MSG_RESULT([supported ($PHP_REF_FOUND_VERSION)])
  fi

    echo "$PHP_VERSION_ID"

    if test -z "$TRAVIS" ; then
        type git &>/dev/null

        if test $? -eq 0 ; then
            git describe --abbrev=0 --tags &>/dev/null

            if test $? -eq 0 ; then
                AC_DEFINE_UNQUOTED([PHP_REF_VERSION], ["`git describe --abbrev=0 --tags`-`git rev-parse --abbrev-ref HEAD`-dev"], [git version])
            fi

            git rev-parse --short HEAD &>/dev/null

            if test $? -eq 0 ; then
                AC_DEFINE_UNQUOTED([PHP_REF_REVISION], ["`git rev-parse --short HEAD`"], [git revision])
            fi
        else
            AC_MSG_NOTICE([git not installed. Cannot obtain php-ref version tag. Install git.])
        fi
    fi

    PHP_NEW_EXTENSION(ref, [            \
        ref.c                           \
        php_ref_notifier_exception.c    \
        php_ref_reference.c             \
        php_ref_functions.c             \
    ], $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
