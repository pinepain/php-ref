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

#ifndef PHP_WEAK_H
#define PHP_WEAK_H

#include "php.h"

extern zend_module_entry php_weak_module_entry;
#define phpext_weak_ptr &php_weak_module_entry

#ifndef PHP_WEAK_VERSION
#define PHP_WEAK_VERSION "0.2.3"
#endif

#ifndef PHP_WEAK_REVISION
#define PHP_WEAK_REVISION "release"
#endif

#if PHP_VERSION_ID <= 70002
#define PHP_WEAK_PATCH_SPL_OBJECT_HASH
#endif

#define PHP_WEAK_NS "Weak"

#ifdef PHP_WIN32
#    define PHP_WEAK_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#    define PHP_WEAK_API __attribute__ ((visibility("default")))
#else
#    define PHP_WEAK_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

ZEND_BEGIN_MODULE_GLOBALS(weak)
    HashTable *referents;
#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
    zend_bool spl_hash_replaced;
#endif
ZEND_END_MODULE_GLOBALS(weak)

ZEND_EXTERN_MODULE_GLOBALS(weak);
#define PHP_WEAK_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(weak, v)

#if defined(ZTS) && defined(COMPILE_DL_WEAK)
ZEND_TSRMLS_CACHE_EXTERN();
#endif

#endif /* PHP_WEAK_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
