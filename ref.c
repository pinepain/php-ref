/*
 * This file is part of the pinepain/php-ref PHP extension.
 *
 * Copyright (c) 2016-2017 Bogdan Padalko <pinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source or visit
 * http://opensource.org/licenses/MIT
 */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php_ref_functions.h"
#include "php_ref_reference.h"
#include "php_ref_notifier_exception.h"
#include "php_ref.h"

#include "ext/standard/info.h"

ZEND_DECLARE_MODULE_GLOBALS(ref)

/* True global resources - no need for thread safety here */
static int le_ref;

PHP_MINIT_FUNCTION(ref)
{
    PHP_MINIT(php_ref_notifier_exception)(INIT_FUNC_ARGS_PASSTHRU);
    PHP_MINIT(php_ref_reference)(INIT_FUNC_ARGS_PASSTHRU);

    return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(ref)
{
    return SUCCESS;
}

PHP_RINIT_FUNCTION(ref)
{
#if defined(COMPILE_DL_REF) && defined(ZTS)
    ZEND_TSRMLS_CACHE_UPDATE();
#endif
    return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(ref)
{
    if (NULL != PHP_REF_G(referents)) {
        zend_hash_destroy(PHP_REF_G(referents));
        FREE_HASHTABLE(PHP_REF_G(referents));
        PHP_REF_G(referents) = NULL;
    }

    return SUCCESS;
}


PHP_MINFO_FUNCTION(ref)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "Ref support", "enabled");
    php_info_print_table_row(2, "Version",   PHP_REF_VERSION);
    php_info_print_table_row(2, "Revision",  PHP_REF_REVISION);
    php_info_print_table_row(2, "Compiled",  __DATE__ " @ "  __TIME__);

    php_info_print_table_end();
}

static PHP_GINIT_FUNCTION(ref)
{
    ref_globals->referents = NULL;
}

zend_module_entry php_ref_module_entry = {
    STANDARD_MODULE_HEADER,
    "ref",
    php_ref_functions,
    PHP_MINIT(ref),
    PHP_MSHUTDOWN(ref),
    PHP_RINIT(ref),
    PHP_RSHUTDOWN(ref),
    PHP_MINFO(ref),
    PHP_REF_VERSION,
    PHP_MODULE_GLOBALS(ref),
    PHP_GINIT(ref),
    NULL,
    NULL,
    STANDARD_MODULE_PROPERTIES_EX
};

#ifdef COMPILE_DL_REF
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE();
#endif
ZEND_GET_MODULE(php_ref)
#endif
