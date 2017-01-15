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

#ifndef PHP_REF_REFERENCE_H
#define PHP_REF_REFERENCE_H

#include "php.h"

#ifdef ZTS
#include "TSRM.h"
#endif

extern zend_class_entry *php_ref_abstract_reference_class_entry;
extern zend_class_entry *php_ref_soft_reference_class_entry;
extern zend_class_entry *php_ref_weak_reference_class_entry;


typedef struct _php_ref_referent_t php_ref_referent_t;
typedef struct _php_ref_reference_t php_ref_reference_t;

typedef void (*php_ref_register)(php_ref_reference_t *reference, php_ref_referent_t *referent);
typedef void (*php_ref_unregister)(php_ref_reference_t *reference);

extern php_ref_reference_t *php_ref_reference_fetch_object(zend_object *obj);
extern php_ref_referent_t *php_ref_referent_find_ptr(zend_ulong h);
extern void php_ref_globals_referents_ht_dtor(zval *zv);


#define PHP_REF_REFERENCE_FETCH(zv) php_ref_reference_fetch_object(Z_OBJ_P(zv))
#define PHP_REF_REFERENCE_FETCH_INTO(pzval, into) php_ref_reference_t *(into) = PHP_REF_REFERENCE_FETCH((pzval));

#define PHP_REF_NOTIFIER_INVALID    0
#define PHP_REF_NOTIFIER_NULL       1
#define PHP_REF_NOTIFIER_ARRAY      2
#define PHP_REF_NOTIFIER_CALLBACK   3

struct _php_ref_referent_t {
    zval this_ptr;
    uint32_t handle;

    zend_object_handlers custom_handlers;
    const zend_object_handlers *original_handlers;

    HashTable soft_references;
    HashTable weak_references;
};

struct _php_ref_reference_t {
    php_ref_referent_t *referent;

    zval notifier;
    int notifier_type;

    php_ref_register register_reference;
    php_ref_unregister unregister_reference;

    zval this_ptr;
    zend_object std;
};


PHP_MINIT_FUNCTION(php_ref_reference);


#endif /* PHP_REF_REFERENCE_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
