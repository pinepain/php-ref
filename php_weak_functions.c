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

#include "php_weak_functions.h"
#include "php_weak_reference.h"
#include "php_weak.h"
#include "ext/spl/php_spl.h"

PHP_FUNCTION(refcounted) /* {{{ */
{
    zval *zv;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    RETURN_BOOL(Z_REFCOUNTED_P(zv));
} /* }}} */

PHP_FUNCTION(refcount)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (Z_REFCOUNTED_P(zv)) {
        RETURN_LONG(Z_REFCOUNT_P(zv) - 1); /* -1 to skip passed argument counting */
    }

    RETURN_LONG(0);
} /* }}} */

PHP_FUNCTION(weakrefcounted) /* {{{ */
{
    zval *zv;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_weak_referent_t *referent = php_weak_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        RETURN_BOOL(NULL != referent);
    }

    RETURN_BOOL(0);
} /* }}} */

PHP_FUNCTION(weakrefcount) /* {{{ */
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {

        php_weak_referent_t *referent = php_weak_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL == referent) {
            RETURN_LONG(0);
        }

        RETURN_LONG(zend_hash_num_elements(&referent->weak_references));
    }

    RETURN_LONG(0);
} /* }}} */

PHP_FUNCTION(weakrefs) /* {{{ */
{
    zval *zv;
    zval  weakrefs;

    zend_ulong   hashIndex;
    zval        *hashData;
    zend_string *hashKey;

    php_weak_reference_t *reference;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    ZVAL_UNDEF(&weakrefs);

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_weak_referent_t *referent = php_weak_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL != referent) {
            array_init_size(&weakrefs, zend_hash_num_elements(&referent->weak_references));

            ZEND_HASH_FOREACH_KEY_VAL(&referent->weak_references, hashIndex, hashKey, hashData) {
                reference = (php_weak_reference_t *) Z_PTR_P(hashData);

                add_next_index_zval(&weakrefs, &reference->this_ptr);
                Z_ADDREF(reference->this_ptr);
            } ZEND_HASH_FOREACH_END();
        }
    }

    if (IS_UNDEF == Z_TYPE(weakrefs)) {
        array_init_size(&weakrefs, 0);
    }

    RETURN_ZVAL(&weakrefs, 1, 1);
} /* }}} */


PHP_FUNCTION(object_handle) /* {{{ */
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &zv) == FAILURE) {
        RETURN_NULL();
    }

    RETURN_LONG((uint32_t)Z_OBJ_HANDLE_P(zv));
}  /* }}} */

#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
PHP_FUNCTION(spl_object_hash_patched)
{
    zval *obj;
    zend_string *hash = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "o", &obj) == FAILURE) {
        return;
    }

    php_weak_referent_t *referent = php_weak_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(obj));

    if (NULL != referent) {
        Z_OBJ_P(obj)->handlers = referent->original_handlers;
        hash = php_spl_object_hash(obj);
        Z_OBJ_P(obj)->handlers = &referent->custom_handlers;
    }

    if (NULL == hash) {
        hash = php_spl_object_hash(obj);
    }

    RETURN_NEW_STR(hash);
} /* }}} */
#endif


ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(refcounted_arg, 0, 1, _IS_BOOL, NULL, 0)
                ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(refcount_arg, 0, 1, IS_LONG, NULL, 0)
                ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefcounted_arg, 0, 1, _IS_BOOL, NULL, 0)
                ZEND_ARG_TYPE_INFO(0, object, IS_OBJECT, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefcount_arg, 0, 1, IS_LONG, NULL, 0)
                ZEND_ARG_TYPE_INFO(0, object, IS_OBJECT, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefs_arg, 0, 1, IS_ARRAY, NULL, 0)
                ZEND_ARG_TYPE_INFO(0, object, IS_OBJECT, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(object_handle_arg, 0, 1, IS_LONG, NULL, 0)
                ZEND_ARG_TYPE_INFO(0, object, IS_OBJECT, 0)
ZEND_END_ARG_INFO()

#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
ZEND_BEGIN_ARG_INFO_EX(arginfo_spl_object_hash, 0, 0, 1)
    ZEND_ARG_INFO(0, obj)
ZEND_END_ARG_INFO()
#endif


const zend_function_entry php_weak_functions[] = { /* {{{ */
    PHP_NAMED_FE(Weak\\refcounted, PHP_FN(refcounted), refcounted_arg)
    PHP_NAMED_FE(Weak\\refcount, PHP_FN(refcount), refcount_arg)

    PHP_NAMED_FE(Weak\\weakrefcounted, PHP_FN(weakrefcounted), weakrefcounted_arg)
    PHP_NAMED_FE(Weak\\weakrefcount, PHP_FN(weakrefcount), weakrefcount_arg)
    PHP_NAMED_FE(Weak\\weakrefs, PHP_FN(weakrefs), weakrefs_arg)

    PHP_NAMED_FE(Weak\\object_handle, PHP_FN(object_handle), object_handle_arg)

#ifdef PHP_WEAK_PATCH_SPL_OBJECT_HASH
    PHP_NAMED_FE(Weak\\spl_object_hash, PHP_FN(spl_object_hash_patched), arginfo_spl_object_hash)
#endif

    PHP_FE_END
}; /* }}} */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
