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

#include "php_weak.h"
#include "php_weak_reference.h"
#include "zend_exceptions.h"
#include "zend_interfaces.h"

zend_class_entry *php_weak_reference_class_entry;
#define this_ce php_weak_reference_class_entry

zend_object_handlers php_weak_reference_object_handlers;

php_weak_reference_t *php_weak_reference_init(zval *this_ptr,
                                              zval *referent_zv,
                                              zend_fcall_info notify_fci,
                                              zend_fcall_info_cache notify_fci_cache);

php_weak_reference_t *php_weak_reference_fetch_object(zend_object *obj) /* {{{ */
{
    return (php_weak_reference_t *)((char *)obj - XtOffsetOf(php_weak_reference_t, std));
} /* }}} */

php_weak_referent_t *php_weak_referent_find_ptr(zend_ulong h) /* {{{ */
{
    if (NULL == PHP_WEAK_G(referents)) {
        return NULL;
    }

    return (php_weak_referent_t*) zend_hash_index_find_ptr(PHP_WEAK_G(referents), h);
} /* }}} */

void php_weak_reference_callback_create(zend_fcall_info fci, zend_fcall_info_cache fci_cache, php_weak_reference_t *reference) /* {{{ */
{
    assert(NULL == reference->callback);

    php_weak_callback_t *callback = (php_weak_callback_t *) ecalloc(1, sizeof(*callback));

    callback->fci = fci;
    callback->fci_cache = fci_cache;

    if (fci.size) {
        Z_ADDREF(callback->fci.function_name);

        if (fci.object) {
            ZVAL_OBJ(&callback->object, fci.object);
            Z_ADDREF(callback->object);
        }
    }

    reference->callback = callback;
} /* }}} */

void php_weak_reference_callback_call(php_weak_reference_t *reference) /* {{{ */
{
    /* Further optimization: take out args and retval. Makes sense when more than on weakref for single obj exists */
    zval args;

    /* Build the parameter array */
    array_init_size(&args, 1);
    /* First argument to a notifier is weak reference object itself */
    add_index_zval(&args, 0, &reference->this_ptr);
    Z_ADDREF(reference->this_ptr);

    /* Convert everything to be callable */
    zend_fcall_info_args(&reference->callback->fci, &args);

    zval retval_tmp;
    reference->callback->fci.retval = &retval_tmp;

    /* Call the function */
    zend_call_function(&reference->callback->fci, &reference->callback->fci_cache);
    reference->callback->fci.retval = NULL;

    /* Clean up our mess */
    zend_fcall_info_args_clear(&reference->callback->fci, 1);

    zval_ptr_dtor(&args);
    zval_ptr_dtor(&retval_tmp);
} /* }}} */

void php_weak_reference_callback_destroy(php_weak_reference_t *reference) /* {{{ */
{
    if (reference->callback == NULL) {
        return;
    }

    if (reference->callback->fci.size) {
        zval_ptr_dtor(&reference->callback->fci.function_name);

        if (!Z_ISUNDEF(reference->callback->object)) {
            zval_ptr_dtor(&reference->callback->object);
        }
    }

    efree(reference->callback);
    reference->callback = NULL;
} /* }}} */

void php_weak_referent_object_dtor_obj(zend_object *object) /* {{{ */
{
    php_weak_referent_t *referent = php_weak_referent_find_ptr(object->handle);

    assert(NULL != referent);
    assert(NULL != PHP_WEAK_G(referents));

    zend_ulong    hashIndex;
    zval *hashData;
    zend_string *hashKey;

    php_weak_reference_t *reference;

    referent->original_handlers->dtor_obj(object);

    ZEND_HASH_REVERSE_FOREACH_KEY_VAL(&referent->weak_references, hashIndex, hashKey, hashData) {
        reference = (php_weak_reference_t *) Z_PTR_P(hashData);
        reference->referent = NULL;

        if (reference->callback && !EG(exception)) {
            php_weak_reference_callback_call(reference);
        }

        zend_hash_index_del(&referent->weak_references, hashIndex);
    } ZEND_HASH_FOREACH_END();

    zend_hash_index_del(PHP_WEAK_G(referents), referent->handle);
} /* }}} */

void php_weak_globals_referents_ht_dtor(zval *zv) /* {{{ */
{
    php_weak_referent_t *referent = (php_weak_referent_t *) Z_PTR_P(zv);

    assert(NULL != referent);

    zend_hash_destroy(&referent->weak_references);
    Z_OBJ(referent->this_ptr)->handlers = referent->original_handlers;

    efree(referent);
} /* }}} */

void php_weak_referent_weak_references_ht_dtor(zval *zv) /* {{{ */
{
    php_weak_reference_t *reference = (php_weak_reference_t *) Z_PTR_P(zv);

    /* clean links to ht & release callbacks as we don't need them already*/
    reference->referent = NULL; /* no need to free anything at this point here */

    php_weak_reference_callback_destroy(reference);
} /* }}} */

php_weak_referent_t * php_weak_referent_get_or_create(zval *referent_zv) /* {{{ */
{
    php_weak_referent_t *referent = php_weak_referent_find_ptr((zend_ulong)Z_OBJ_HANDLE_P(referent_zv));

    if (referent != NULL) {
        return referent;
    }

    referent = (php_weak_referent_t *) ecalloc(1, sizeof(php_weak_referent_t));

    zend_hash_init(&referent->weak_references, 0, NULL, php_weak_referent_weak_references_ht_dtor, 0);
    referent->original_handlers = Z_OBJ_P(referent_zv)->handlers;

    ZVAL_COPY_VALUE(&referent->this_ptr, referent_zv);
    referent->handle = Z_OBJ_HANDLE_P(referent_zv);

    memcpy(&referent->custom_handlers, referent->original_handlers, sizeof(zend_object_handlers));
    referent->custom_handlers.dtor_obj = php_weak_referent_object_dtor_obj;

    Z_OBJ_P(referent_zv)->handlers = &referent->custom_handlers;

    if (NULL == PHP_WEAK_G(referents)) {
        ALLOC_HASHTABLE(PHP_WEAK_G(referents));
        zend_hash_init(PHP_WEAK_G(referents), 1, NULL, php_weak_globals_referents_ht_dtor, 0);
    }

    zend_hash_index_add_ptr(PHP_WEAK_G(referents), (zend_ulong)Z_OBJ_HANDLE_P(referent_zv), referent);

    return referent;
} /* }}} */

void php_weak_reference_attach(php_weak_reference_t *reference, php_weak_referent_t *referent) /* {{{ */
{
    reference->referent = referent;
    zend_hash_index_add_ptr(&referent->weak_references, (zend_ulong)Z_OBJ_HANDLE_P(&reference->this_ptr), reference);
} /* }}} */

void php_weak_reference_unregister(php_weak_reference_t *reference) /* {{{ */
{
    zend_hash_index_del(&reference->referent->weak_references, (zend_ulong)Z_OBJ_HANDLE_P(&reference->this_ptr));
} /* }}} */

void php_weak_reference_maybe_unregister(php_weak_reference_t *reference) /* {{{ */
{
    if (NULL == reference->referent) {
        return;
    }

    php_weak_reference_unregister(reference);
} /* }}} */

php_weak_reference_t *php_weak_reference_init(zval *this_ptr,
                                              zval *referent_zv,
                                              zend_fcall_info notify_fci,
                                              zend_fcall_info_cache notify_fci_cache)  /* {{{ */
{
    php_weak_referent_t *referent;

    PHP_WEAK_REFERENCE_FETCH_INTO(this_ptr, reference);
    ZVAL_COPY_VALUE(&reference->this_ptr, this_ptr);

    referent = php_weak_referent_get_or_create(referent_zv);

    if (notify_fci.size) {
        php_weak_reference_callback_create(notify_fci, notify_fci_cache, reference);
    }

    php_weak_reference_attach(reference, referent);

    return reference;
} /* }}} */

static HashTable * php_weak_reference_gc(zval *object, zval **table, int *n) /* {{{ */
{
    PHP_WEAK_REFERENCE_FETCH_INTO(object, reference);

    int size = 0;

    if (NULL != reference->callback)  {
        size++;

        if (IS_UNDEF != Z_TYPE(reference->callback->object)) {
            size++;
        }
    }

    if (reference->gc_data_count < size) {
        reference->gc_data = (zval *)safe_erealloc(reference->gc_data, (size_t)size, sizeof(zval), 0);
    }

    reference->gc_data_count = size;

    int i = 0;
    if (NULL != reference->callback) {
        ZVAL_COPY_VALUE(&reference->gc_data[i++], &reference->callback->fci.function_name);

        if (IS_UNDEF != Z_TYPE(reference->callback->object)) {
            ZVAL_COPY_VALUE(&reference->gc_data[i++], &reference->callback->object);
        }
    }

    *table = reference->gc_data;
    *n     = reference->gc_data_count;

    return zend_std_get_properties(object);
} /* }}} */

static void php_weak_reference_free(zend_object *object) /* {{{ */
{
    php_weak_reference_t *reference = php_weak_reference_fetch_object(object);

    /* unregister weak reference from tracking object, if not done already before at some place (e.g. obj dtored) */
    php_weak_reference_maybe_unregister(reference);

    /* freeing original object */
    zend_object_std_dtor(&reference->std);
} /* }}} */

static void php_weak_reference_dtor(zend_object *object) /* {{{ */
{
    php_weak_reference_t *reference = php_weak_reference_fetch_object(object);

    /* unregister weak reference from tracking object, if not done already before at some place (e.g. obj dtored) */
    php_weak_reference_maybe_unregister(reference);

    /* call standard dtor */
    zend_objects_destroy_object(object);
} /* }}} */

static zend_object * php_weak_reference_ctor(zend_class_entry *ce)  /* {{{ */
{
    php_weak_reference_t *reference;

    reference = (php_weak_reference_t *) ecalloc(1, sizeof(php_weak_reference_t) + zend_object_properties_size(ce));

    zend_object_std_init(&reference->std, ce);
    object_properties_init(&reference->std, ce);

    reference->std.handlers = &php_weak_reference_object_handlers;

    return &reference->std;
} /* }}} */

static zend_object* php_weak_reference_clone_obj(zval *object) /* {{{ */
{
    zend_object *old_object;
    zend_object *new_object;

    old_object = Z_OBJ_P(object);

    new_object = php_weak_reference_ctor(old_object->ce);

    php_weak_reference_t *old_reference = php_weak_reference_fetch_object(old_object);
    php_weak_reference_t *new_reference = php_weak_reference_fetch_object(new_object);

    ZVAL_OBJ(&new_reference->this_ptr, new_object);

    if (old_reference->callback) {
        php_weak_reference_callback_create(old_reference->callback->fci, old_reference->callback->fci_cache, new_reference);
    }

    if (old_reference->referent) {
        php_weak_reference_attach(new_reference, old_reference->referent);
    }

    zend_objects_clone_members(new_object, old_object);

    return new_object;
} /* }}} */

static HashTable * php_weak_get_debug_info(zval *object, int *is_temp) /* {{{ */
{
    HashTable *debug_info;
    zend_string *key;
    HashTable *props;

    PHP_WEAK_REFERENCE_FETCH_INTO(object, reference);
    *is_temp = 1;
    props = Z_OBJPROP_P(object);

    ALLOC_HASHTABLE(debug_info);
    ZEND_INIT_SYMTABLE_EX(debug_info, zend_hash_num_elements(props) + 1, 0);

    zend_hash_copy(debug_info, props, (copy_ctor_func_t)zval_add_ref);

    key = zend_mangle_property_name(ZSTR_VAL(this_ce->name), ZSTR_LEN(this_ce->name), "referent", sizeof("referent") - 1, 0);

    if (NULL != reference->referent) {
        zend_symtable_update(debug_info, key, &reference->referent->this_ptr);
        Z_TRY_ADDREF(reference->referent->this_ptr);
    } else {
        zval tmp;
        ZVAL_NULL(&tmp);
        zend_symtable_update(debug_info, key, &tmp);
    }

    zend_string_release(key);

    return debug_info;
} /* }}} */

static int php_weak_compare_objects(zval *object1, zval *object2) /* {{{ */
{
    PHP_WEAK_REFERENCE_FETCH_INTO(object1, reference1);
    PHP_WEAK_REFERENCE_FETCH_INTO(object2, reference2);

    if (NULL == reference1->referent && NULL == reference2->referent) {
        return 0;
    }

    if (NULL == reference1->referent || NULL == reference2->referent) {
        return 1;
    }

    return std_object_handlers.compare_objects(&reference1->referent->this_ptr, &reference2->referent->this_ptr);
}
/* }}} */

static PHP_METHOD(WeakReference, __construct)  /* {{{ */
{
    zval *referent_zv;

    zend_fcall_info notify_fci = empty_fcall_info;
    zend_fcall_info_cache notify_fci_cache = empty_fcall_info_cache;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "o|f!", &referent_zv, &notify_fci, &notify_fci_cache) == FAILURE) {
        return;
    }

    php_weak_reference_init(getThis(), referent_zv, notify_fci, notify_fci_cache);
} /* }}} */

static PHP_METHOD(WeakReference, get)  /* {{{ */
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    PHP_WEAK_REFERENCE_FETCH_INTO(getThis(), reference);

    if (NULL == reference->referent) {
        RETURN_NULL();
    }

    RETURN_ZVAL(&reference->referent->this_ptr, 1, 0);
} /* }}} */

static PHP_METHOD(WeakReference, valid)  /* {{{ */
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    PHP_WEAK_REFERENCE_FETCH_INTO(getThis(), reference);

    RETURN_BOOL(NULL != reference->referent);
} /* }}} */


ZEND_BEGIN_ARG_INFO_EX(arginfo_weak_reference___construct, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 1)
                ZEND_ARG_TYPE_INFO(0, referent, IS_OBJECT, 0)
                ZEND_ARG_CALLABLE_INFO(0, notify, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_weak_reference_get, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, IS_OBJECT, NULL, 1)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_weak_reference_valid, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, _IS_BOOL, NULL, 0)
ZEND_END_ARG_INFO()


static const zend_function_entry php_weak_reference_methods[] = { /* {{{ */
        PHP_ME(WeakReference, __construct, arginfo_weak_reference___construct, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)

        PHP_ME(WeakReference, get, arginfo_weak_reference_get, ZEND_ACC_PUBLIC)
        PHP_ME(WeakReference, valid, arginfo_weak_reference_valid, ZEND_ACC_PUBLIC)

        PHP_FE_END
}; /* }}} */


PHP_MINIT_FUNCTION(php_weak_reference) /* {{{ */
{
    zend_class_entry ce;

    INIT_NS_CLASS_ENTRY(ce, "Weak", "Reference", php_weak_reference_methods);
    ce.serialize = zend_class_serialize_deny;
    ce.unserialize = zend_class_unserialize_deny;
    this_ce = zend_register_internal_class(&ce);
    this_ce->create_object = php_weak_reference_ctor;

    memcpy(&php_weak_reference_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));

    php_weak_reference_object_handlers.offset    = XtOffsetOf(php_weak_reference_t, std);
    php_weak_reference_object_handlers.free_obj  = php_weak_reference_free;
    php_weak_reference_object_handlers.dtor_obj  = php_weak_reference_dtor;
    php_weak_reference_object_handlers.get_gc    = php_weak_reference_gc;
    php_weak_reference_object_handlers.clone_obj = php_weak_reference_clone_obj;
    php_weak_reference_object_handlers.get_debug_info  = php_weak_get_debug_info;
    php_weak_reference_object_handlers.compare_objects = php_weak_compare_objects;

    return SUCCESS;
} /* }}} */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
