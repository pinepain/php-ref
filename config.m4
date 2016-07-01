dnl $Id$
dnl config.m4 for extension weak

PHP_ARG_ENABLE(weak, whether to enable weak support,
dnl Make sure that the comment is aligned:
[  --enable-weak           Enable weak support])

if test "$PHP_WEAK" != "no"; then

    if test -z "$TRAVIS" ; then
        type git &>/dev/null

        if test $? -eq 0 ; then
            git describe --abbrev=0 --tags &>/dev/null

            if test $? -eq 0 ; then
                AC_DEFINE_UNQUOTED([PHP_WEAK_VERSION], ["`git describe --abbrev=0 --tags`-`git rev-parse --abbrev-ref HEAD`-dev"], [git version])
            fi

            git rev-parse --short HEAD &>/dev/null

            if test $? -eq 0 ; then
                AC_DEFINE_UNQUOTED([PHP_WEAK_REVISION], ["`git rev-parse --short HEAD`"], [git revision])
            fi
        else
            AC_MSG_NOTICE([git not installed. Cannot obtain php-weak version tag. Install git.])
        fi
    fi

    PHP_NEW_EXTENSION(weak, [           \
        weak.c                          \
        php_weak_notifier_exception.c   \
        php_weak_reference.c            \
        php_weak_functions.c            \
    ], $ext_shared,, -DZEND_ENABLE_STATIC_TSRMLS_CACHE=1)
fi
