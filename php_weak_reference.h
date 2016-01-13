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

#ifndef PHP_WEAK_REFERENCE_H
#define PHP_WEAK_REFERENCE_H

#include "php.h"

extern zend_class_entry *php_weak_reference_class_entry;


typedef struct _php_weak_referent_t php_weak_referent_t;
typedef struct _php_weak_reference_t php_weak_reference_t;


extern php_weak_reference_t *php_weak_reference_fetch_object(zend_object *obj);
extern php_weak_referent_t *php_weak_referent_find_ptr(zend_ulong h);
extern void php_weak_globals_referents_ht_dtor(zval *zv);


#define PHP_WEAK_REFERENCE_FETCH(zv) php_weak_reference_fetch_object(Z_OBJ_P(zv))
#define PHP_WEAK_REFERENCE_FETCH_INTO(pzval, into) php_weak_reference_t *(into) = PHP_WEAK_REFERENCE_FETCH((pzval));


struct _php_weak_referent_t {
    zval this_ptr;
    uint32_t handle;

    zend_object_handlers custom_handlers;
    const zend_object_handlers *original_handlers;

    HashTable weak_references;
};

struct _php_weak_reference_t {
    php_weak_referent_t *referent;

    zval notifier;

    zval this_ptr;
    zend_object std;
};


PHP_MINIT_FUNCTION(php_weak_reference);


#endif /* PHP_WEAK_REFERENCE_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
