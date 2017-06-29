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

#include "php_ref_functions.h"
#include "php_ref_reference.h"
#include "php_ref.h"

PHP_FUNCTION(refcounted)
{
    zval *zv;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    RETURN_BOOL(Z_REFCOUNTED_P(zv));
}

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
}

PHP_FUNCTION(softrefcounted)
{
    zval *zv;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        RETURN_BOOL(NULL != referent && zend_hash_num_elements(&referent->soft_references));
    }

    RETURN_BOOL(0);
}

PHP_FUNCTION(softrefcount)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {

        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL == referent) {
            RETURN_LONG(0);
        }

        RETURN_LONG(zend_hash_num_elements(&referent->soft_references));
    }

    RETURN_LONG(0);
}

PHP_FUNCTION(softrefs)
{
    zval *zv;
    zval  softrefs;

    php_ref_reference_t *reference;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    ZVAL_UNDEF(&softrefs);

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL != referent) {
            array_init_size(&softrefs, zend_hash_num_elements(&referent->soft_references));

            ZEND_HASH_FOREACH_PTR(&referent->soft_references, reference) {
                        add_next_index_zval(&softrefs, &reference->this_ptr);
                        Z_ADDREF(reference->this_ptr);
                    } ZEND_HASH_FOREACH_END();
        }
    }

    if (IS_UNDEF == Z_TYPE(softrefs)) {
        array_init_size(&softrefs, 0);
    }

    RETURN_ZVAL(&softrefs, 1, 1);
}

PHP_FUNCTION(weakrefcounted)
{
    zval *zv;
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        RETURN_BOOL(NULL != referent && zend_hash_num_elements(&referent->weak_references));
    }

    RETURN_BOOL(0);
}

PHP_FUNCTION(weakrefcount)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    if (IS_OBJECT == Z_TYPE_P(zv)) {

        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL == referent) {
            RETURN_LONG(0);
        }

        RETURN_LONG(zend_hash_num_elements(&referent->weak_references));
    }

    RETURN_LONG(0);
}

PHP_FUNCTION(weakrefs)
{
    zval *zv;
    zval  weakrefs;

    php_ref_reference_t *reference;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &zv) == FAILURE) {
        return;
    }

    ZVAL_UNDEF(&weakrefs);

    if (IS_OBJECT == Z_TYPE_P(zv)) {
        php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(zv));

        if (NULL != referent) {
            array_init_size(&weakrefs, zend_hash_num_elements(&referent->weak_references));

            ZEND_HASH_FOREACH_PTR(&referent->weak_references, reference) {
                add_next_index_zval(&weakrefs, &reference->this_ptr);
                Z_ADDREF(reference->this_ptr);
            } ZEND_HASH_FOREACH_END();
        }
    }

    if (IS_UNDEF == Z_TYPE(weakrefs)) {
        array_init_size(&weakrefs, 0);
    }

    RETURN_ZVAL(&weakrefs, 1, 1);
}

PHP_FUNCTION(object_handle)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &zv) == FAILURE) {
        return;
    }

    RETURN_LONG((uint32_t)Z_OBJ_HANDLE_P(zv));
}

PHP_FUNCTION(is_obj_destructor_called)
{
    zval *zv;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "o", &zv) == FAILURE) {
        return;
    }

    zend_object *obj = Z_OBJ_P(zv);

    uint32_t flags = GC_FLAGS(obj);

    RETURN_BOOL(flags & IS_OBJ_DESTRUCTOR_CALLED);
}


PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(refcounted_arg, ZEND_RETURN_VALUE, 1, _IS_BOOL, 0)
                ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(refcount_arg, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
                ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(softrefcounted_arg, ZEND_RETURN_VALUE, 1, _IS_BOOL, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(softrefcount_arg, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(softrefs_arg, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefcounted_arg, ZEND_RETURN_VALUE, 1, _IS_BOOL, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefcount_arg, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(weakrefs_arg, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(object_handle_arg, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(is_obj_destructor_called_arg, ZEND_RETURN_VALUE, 1, _IS_BOOL, 0)
                ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

const zend_function_entry php_ref_functions[] = {
    ZEND_NS_FE(PHP_REF_NS, refcounted, refcounted_arg)
    ZEND_NS_FE(PHP_REF_NS, refcount, refcount_arg)

    ZEND_NS_FE(PHP_REF_NS, softrefcounted, softrefcounted_arg)
    ZEND_NS_FE(PHP_REF_NS, softrefcount, softrefcount_arg)
    ZEND_NS_FE(PHP_REF_NS, softrefs, softrefs_arg)

    ZEND_NS_FE(PHP_REF_NS, weakrefcounted, weakrefcounted_arg)
    ZEND_NS_FE(PHP_REF_NS, weakrefcount, weakrefcount_arg)
    ZEND_NS_FE(PHP_REF_NS, weakrefs, weakrefs_arg)

    ZEND_NS_FE(PHP_REF_NS, object_handle, object_handle_arg)
    ZEND_NS_FE(PHP_REF_NS, is_obj_destructor_called, is_obj_destructor_called_arg)

    PHP_FE_END
};
