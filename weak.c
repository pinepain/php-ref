/*
  +----------------------------------------------------------------------+
  | This file is part of the pinepain/php-weak PHP extension.            |
  |                                                                      |
  | Copyright (c) 2016 Bogdan Padalko <zaq178miami@gmail.com>            |
  |                                                                      |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT   |
  |                                                                      |
  | For the full copyright and license information, please view the      |
  | LICENSE file that was distributed with this source or visit          |
  | http://opensource.org/licenses/MIT                                   |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php_weak_functions.h"
#include "php_weak_reference.h"
#include "php_weak.h"

#include "ext/standard/info.h"

ZEND_DECLARE_MODULE_GLOBALS(weak)

/* True global resources - no need for thread safety here */
static int le_weak;

PHP_MINIT_FUNCTION(weak) /* {{{ */
{
    PHP_MINIT(php_weak_reference)(INIT_FUNC_ARGS_PASSTHRU);

    return SUCCESS;
} /* }}} */

PHP_MSHUTDOWN_FUNCTION(weak) /* {{{ */
{
    return SUCCESS;
} /* }}} */

PHP_RINIT_FUNCTION(weak) /* {{{ */
{
#if defined(COMPILE_DL_WEAK) && defined(ZTS)
    ZEND_TSRMLS_CACHE_UPDATE();
#endif
    return SUCCESS;
} /* }}} */

PHP_RSHUTDOWN_FUNCTION(weak) /* {{{ */
{
    if (NULL != PHP_WEAK_G(referents)) {
        zend_hash_destroy(PHP_WEAK_G(referents));
        FREE_HASHTABLE(PHP_WEAK_G(referents));
        PHP_WEAK_G(referents) = NULL;
    }

    return SUCCESS;
} /* }}} */


PHP_MINFO_FUNCTION(weak) /* {{{ */
{
    php_info_print_table_start();
    php_info_print_table_header(2, "Version",   PHP_WEAK_VERSION);
    php_info_print_table_header(2, "Revision",  PHP_WEAK_REVISION);
    php_info_print_table_header(2, "Compiled",  __DATE__ " @ "  __TIME__);

    php_info_print_table_end();
} /* }}} */

static PHP_GINIT_FUNCTION(weak) /* {{{ */
{
    weak_globals->referents = NULL;
#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
    weak_globals->spl_hash_replaced = 0;
#endif

} /* }}} */

#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
static const zend_module_dep php_weak_deps[] = {
    ZEND_MOD_REQUIRED("spl")
    ZEND_MOD_CONFLICTS("weakref")
    ZEND_MOD_END
};
#endif

zend_module_entry php_weak_module_entry = { /* {{{ */
#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
    STANDARD_MODULE_HEADER_EX,  /* size, zend_api, zend_debug, zts*/
    NULL,                       /* ini_entry */
    php_weak_deps,              /* deps */
#else
    STANDARD_MODULE_HEADER,
#endif
    "weak",
    php_weak_functions,
    PHP_MINIT(weak),
    PHP_MSHUTDOWN(weak),
    PHP_RINIT(weak),
    PHP_RSHUTDOWN(weak),
    PHP_MINFO(weak),
    PHP_WEAK_VERSION,
    PHP_MODULE_GLOBALS(weak),
    PHP_GINIT(weak),
    NULL,
    NULL,
    STANDARD_MODULE_PROPERTIES_EX
}; /* }}} */

#ifdef COMPILE_DL_WEAK
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE();
#endif
ZEND_GET_MODULE(php_weak)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
