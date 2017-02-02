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

#include "php_ref_reference.h"
#include "php_ref_notifier_exception.h"
#include "php_ref.h"
#include "zend_exceptions.h"
#include "zend_interfaces.h"

zend_class_entry *php_ref_abstract_reference_class_entry;
zend_class_entry *php_ref_soft_reference_class_entry;
zend_class_entry *php_ref_weak_reference_class_entry;

#define this_ce php_ref_abstract_reference_class_entry

zend_object_handlers php_ref_reference_object_handlers;

php_ref_reference_t *php_ref_reference_init(zval *this_ptr, zval *referent_zv, zval *notifier_zv);

static inline void php_ref_store_exceptions(zval *exceptions, zval *tmp);
static int php_ref_reference_check_notifier(zval *notifier, zval *this);


php_ref_reference_t *php_ref_reference_fetch_object(zend_object *obj) /* {{{ */
{
    return (php_ref_reference_t *) ((char *) obj - XtOffsetOf(php_ref_reference_t, std));
} /* }}} */

php_ref_referent_t *php_ref_referent_find_ptr(zend_ulong h) /* {{{ */
{
    if (NULL == PHP_REF_G(referents)) {
        return NULL;
    }

    return (php_ref_referent_t *) zend_hash_index_find_ptr(PHP_REF_G(referents), h);
} /* }}} */

void php_ref_reference_call_notifier(zval *reference, zval *notifier) /* {{{ */
{
    zval args;
    zval retval_tmp;

    zend_fcall_info fci;
    zend_fcall_info_cache fci_cache;

    char *errstr;

    if (UNEXPECTED(zend_fcall_info_init(notifier, 0, &fci, &fci_cache, NULL, &errstr))) {
        if (errstr) {
            zend_throw_error(zend_ce_type_error, "Notifier should be a valid callback, %s", errstr);
            efree(errstr);
        } else {
            zend_throw_error(zend_ce_type_error, "Notifier should be a valid callback");
        }

        return;
    }

    /* Build the parameter array */
    array_init_size(&args, 1);
    /* First argument to a notifier is weak reference object itself */
    add_index_zval(&args, 0, reference);
    Z_ADDREF_P(reference);

    /* Convert everything to be callable */
    zend_fcall_info_args(&fci, &args);

    fci.retval = &retval_tmp;

    /* Call the function */
    zend_call_function(&fci, &fci_cache);
    fci.retval = NULL;

    /* Clean up our mess */
    zend_fcall_info_args_clear(&fci, 1);

    zval_ptr_dtor(&args);
    zval_ptr_dtor(&retval_tmp);

} /* }}} */

static void php_ref_nullify_referents(HashTable *references)  /* {{{ */
{
    zend_ulong handle;
    php_ref_reference_t *reference;

    ZEND_HASH_REVERSE_FOREACH_PTR(references, reference) {
        handle = _p->h;
        reference->referent = NULL;

        zend_hash_index_del(references, handle);
    } ZEND_HASH_FOREACH_END();
} /* }}} */

static void php_ref_call_notifiers(HashTable *references, zval *exceptions, zval *tmp, zend_bool after_dtor)  /* {{{ */
{
    zend_ulong handle;
    php_ref_reference_t *reference;

    ZEND_HASH_REVERSE_FOREACH_PTR(references, reference) {
        handle = _p->h;

        if (after_dtor) {
            reference->referent = NULL;
        }

        switch (reference->notifier_type) {
            case PHP_REF_NOTIFIER_ARRAY:
                /* array notifier */
                add_next_index_zval(&reference->notifier, &reference->this_ptr);
                Z_ADDREF(reference->this_ptr);
                break;
            case PHP_REF_NOTIFIER_CALLBACK:
                /* callback notifier */
                php_ref_reference_call_notifier(&reference->this_ptr, &reference->notifier);

                if (EG(exception)) {
                    php_ref_store_exceptions(exceptions, tmp);
                }
                break;
            default:
                break;
        }

        if (!after_dtor && reference->referent && Z_REFCOUNT(reference->referent->this_ptr) > 1) {
            return;
        }

        if (after_dtor) {
            zend_hash_index_del(references, handle);
        }
    } ZEND_HASH_FOREACH_END();

} /* }}} */

void php_ref_referent_object_dtor_obj(zend_object *object) /* {{{ */
{
    php_ref_referent_t *referent = php_ref_referent_find_ptr(object->handle);

    zval initial_exception;
    zval exceptions;
    zval tmp;

    ZVAL_UNDEF(&initial_exception);
    ZVAL_UNDEF(&exceptions);

    assert(NULL != referent);
    assert(NULL != PHP_REF_G(referents));

    if (EG(exception)) {
        ZVAL_OBJ(&initial_exception, EG(exception));
        Z_ADDREF(initial_exception);
        zend_clear_exception();
    }

    php_ref_call_notifiers(&referent->soft_references, &exceptions, &tmp, 0);

    if (Z_REFCOUNT(referent->this_ptr) == 1) {
        if (referent->original_handlers->dtor_obj) {
            referent->original_handlers->dtor_obj(object);

            if (EG(exception)) {
                php_ref_store_exceptions(&exceptions, &tmp);
            }
        }

        php_ref_nullify_referents(&referent->soft_references);

        php_ref_call_notifiers(&referent->weak_references, &exceptions, &tmp, 1);

        zend_hash_index_del(PHP_REF_G(referents), referent->handle);
    } else {
        zend_object *obj = Z_OBJ(referent->this_ptr);
        GC_FLAGS(obj) = GC_FLAGS(obj) & ~IS_OBJ_DESTRUCTOR_CALLED;
    }

    if (!Z_ISUNDEF(initial_exception)) {
        zend_throw_exception_object(&initial_exception);
    }

    if (!Z_ISUNDEF(exceptions)) {
        zval exception;
        php_ref_create_notifier_exception(&exception, "One or more exceptions thrown during notifiers calling", &exceptions);

        zend_throw_exception_object(&exception);
    }
} /* }}} */

void php_ref_globals_referents_ht_dtor(zval *zv) /* {{{ */
{
    php_ref_referent_t *referent = (php_ref_referent_t *) Z_PTR_P(zv);

    assert(NULL != referent);

    zend_hash_destroy(&referent->soft_references);
    zend_hash_destroy(&referent->weak_references);

    Z_OBJ(referent->this_ptr)->handlers = referent->original_handlers;

    efree(referent);
} /* }}} */

void php_ref_referent_weak_references_ht_dtor(zval *zv) /* {{{ */
{
    php_ref_reference_t *reference = (php_ref_reference_t *) Z_PTR_P(zv);

    /* clean links to ht & release callbacks as we don't need them already*/
    reference->referent = NULL; /* no need to free anything at this point here */
} /* }}} */

php_ref_referent_t *php_ref_referent_get_or_create(zval *referent_zv) /* {{{ */
{
    php_ref_referent_t *referent = php_ref_referent_find_ptr((zend_ulong) Z_OBJ_HANDLE_P(referent_zv));

    if (referent != NULL) {
        return referent;
    }

    referent = (php_ref_referent_t *) ecalloc(1, sizeof(php_ref_referent_t));

    zend_hash_init(&referent->soft_references, 0, NULL, NULL, 0);
    zend_hash_init(&referent->weak_references, 0, NULL, php_ref_referent_weak_references_ht_dtor, 0);

    referent->original_handlers = Z_OBJ_P(referent_zv)->handlers;

    ZVAL_COPY_VALUE(&referent->this_ptr, referent_zv);
    referent->handle = Z_OBJ_HANDLE_P(referent_zv);

    memcpy(&referent->custom_handlers, referent->original_handlers, sizeof(zend_object_handlers));
    referent->custom_handlers.dtor_obj = php_ref_referent_object_dtor_obj;

    Z_OBJ_P(referent_zv)->handlers = &referent->custom_handlers;

    if (NULL == PHP_REF_G(referents)) {
        ALLOC_HASHTABLE(PHP_REF_G(referents));
        zend_hash_init(PHP_REF_G(referents), 1, NULL, php_ref_globals_referents_ht_dtor, 0);
    }

    zend_hash_index_add_ptr(PHP_REF_G(referents), (zend_ulong) Z_OBJ_HANDLE_P(referent_zv), referent);

    return referent;
} /* }}} */

void php_ref_abstract_reference_attach(php_ref_reference_t *reference, php_ref_referent_t *referent) /* {{{ */
{
} /* }}} */

void php_ref_abstract_reference_maybe_unregister(php_ref_reference_t *reference) /* {{{ */
{
} /* }}} */

void php_ref_soft_reference_attach(php_ref_reference_t *reference, php_ref_referent_t *referent) /* {{{ */
{
    reference->referent = referent;
    zend_hash_index_add_ptr(&referent->soft_references, (zend_ulong) Z_OBJ_HANDLE_P(&reference->this_ptr), reference);
} /* }}} */

void php_ref_soft_reference_unregister(php_ref_reference_t *reference) /* {{{ */
{
    zend_hash_index_del(&reference->referent->soft_references, (zend_ulong) Z_OBJ_HANDLE_P(&reference->this_ptr));
} /* }}} */

void php_ref_soft_reference_maybe_unregister(php_ref_reference_t *reference) /* {{{ */
{
    if (NULL == reference->referent) {
        return;
    }

    php_ref_soft_reference_unregister(reference);
} /* }}} */

void php_ref_reference_attach(php_ref_reference_t *reference, php_ref_referent_t *referent) /* {{{ */
{
    reference->referent = referent;
    zend_hash_index_add_ptr(&referent->weak_references, (zend_ulong) Z_OBJ_HANDLE_P(&reference->this_ptr), reference);
} /* }}} */

void php_ref_reference_unregister(php_ref_reference_t *reference) /* {{{ */
{
    zend_hash_index_del(&reference->referent->weak_references, (zend_ulong) Z_OBJ_HANDLE_P(&reference->this_ptr));
} /* }}} */

void php_ref_reference_maybe_unregister(php_ref_reference_t *reference) /* {{{ */
{
    if (NULL == reference->referent) {
        return;
    }

    php_ref_reference_unregister(reference);
} /* }}} */

php_ref_reference_t *php_ref_reference_init(zval *this_ptr, zval *referent_zv, zval *notifier_zv)  /* {{{ */
{
    php_ref_referent_t *referent;

    PHP_REF_REFERENCE_FETCH_INTO(this_ptr, reference);

    int notifier_type = php_ref_reference_check_notifier(notifier_zv, this_ptr);

    if (PHP_REF_NOTIFIER_INVALID == notifier_type) {
        return reference;
    }

    ZVAL_COPY_VALUE(&reference->this_ptr, this_ptr);

    referent = php_ref_referent_get_or_create(referent_zv);

    reference->register_reference(reference, referent);

    if (NULL != notifier_zv) {
        ZVAL_COPY(&reference->notifier, notifier_zv);
    } else {
        ZVAL_NULL(&reference->notifier);
    }

    reference->notifier_type = notifier_type;

    return reference;
} /* }}} */

static inline void php_ref_store_exceptions(zval *exceptions, zval *tmp)
{
    if (Z_ISUNDEF_P(exceptions)) {
        array_init(exceptions);
    }

    ZVAL_OBJ(tmp, EG(exception));
    Z_ADDREF_P(tmp);
    add_next_index_zval(exceptions, tmp);

    zend_clear_exception();
}

static int php_ref_reference_check_notifier(zval *notifier, zval *this) /* {{{ */
{
    if (NULL == notifier) {
        /* no value provided at all, nothing to check */
        return PHP_REF_NOTIFIER_NULL;
    }

    if (IS_NULL == Z_TYPE_P(notifier)) {
        /* no notifier */
        return PHP_REF_NOTIFIER_NULL;
    }

    /* maybe callback notifier */
    if (!zend_is_callable(notifier, 0, NULL)) {

        if (IS_ARRAY == Z_TYPE_P(notifier)) {
            /* array notifier */
            return PHP_REF_NOTIFIER_ARRAY;
        }

        zend_throw_error(zend_ce_type_error,
                         "Argument 2 passed to %s::%s() must be callable, array or null, %s given",
                         ZSTR_VAL(Z_OBJCE_P(this)->name), get_active_function_name(), zend_zval_type_name(notifier));

        return PHP_REF_NOTIFIER_INVALID;
    }

    return PHP_REF_NOTIFIER_CALLBACK;
} /* }}} */

static HashTable *php_ref_reference_gc(zval *object, zval **table, int *n) /* {{{ */
{
    PHP_REF_REFERENCE_FETCH_INTO(object, reference);

    *table = &reference->notifier;
    *n = 1;

    return zend_std_get_properties(object);
} /* }}} */

static void php_ref_reference_free(zend_object *object) /* {{{ */
{
    php_ref_reference_t *reference = php_ref_reference_fetch_object(object);

    /* unregister weak reference from tracking object, if not done already before at some place (e.g. obj dtored) */
    if (reference->unregister_reference) {
        reference->unregister_reference(reference);
    }

    zval_ptr_dtor(&reference->notifier);
    ZVAL_UNDEF(&reference->notifier);

    /* freeing original object */
    zend_object_std_dtor(&reference->std);
} /* }}} */

static void php_ref_reference_dtor(zend_object *object) /* {{{ */
{
    php_ref_reference_t *reference = php_ref_reference_fetch_object(object);

    /* unregister weak reference from tracking object, if not done already before at some place (e.g. obj dtored) */
    if (reference->unregister_reference) {
        reference->unregister_reference(reference);
    }

    zval_ptr_dtor(&reference->notifier);
    ZVAL_UNDEF(&reference->notifier);

    /* call standard dtor */
    zend_objects_destroy_object(object);
} /* }}} */

static zend_object *php_ref_abstract_reference_ctor(zend_class_entry *ce)  /* {{{ */
{
    php_ref_reference_t *reference;

    reference = (php_ref_reference_t *) ecalloc(1, sizeof(php_ref_reference_t) + zend_object_properties_size(ce));

    zend_object_std_init(&reference->std, ce);
    object_properties_init(&reference->std, ce);

    reference->std.handlers = &php_ref_reference_object_handlers;

    reference->register_reference = php_ref_abstract_reference_attach;
    reference->unregister_reference = php_ref_abstract_reference_maybe_unregister;

    return &reference->std;
} /* }}} */

static zend_object *php_ref_soft_reference_ctor(zend_class_entry *ce)  /* {{{ */
{
    php_ref_reference_t *reference;

    reference = (php_ref_reference_t *) ecalloc(1, sizeof(php_ref_reference_t) + zend_object_properties_size(ce));

    zend_object_std_init(&reference->std, ce);
    object_properties_init(&reference->std, ce);

    reference->std.handlers = &php_ref_reference_object_handlers;

    reference->register_reference = php_ref_soft_reference_attach;
    reference->unregister_reference = php_ref_soft_reference_maybe_unregister;

    return &reference->std;
} /* }}} */

static zend_object *php_ref_weak_reference_ctor(zend_class_entry *ce)  /* {{{ */
{
    php_ref_reference_t *reference;

    reference = (php_ref_reference_t *) ecalloc(1, sizeof(php_ref_reference_t) + zend_object_properties_size(ce));

    zend_object_std_init(&reference->std, ce);
    object_properties_init(&reference->std, ce);

    reference->std.handlers = &php_ref_reference_object_handlers;

    reference->register_reference = php_ref_reference_attach;
    reference->unregister_reference = php_ref_reference_maybe_unregister;

    return &reference->std;
} /* }}} */

static zend_object *php_ref_reference_clone_obj(zval *object) /* {{{ */
{
    zend_object *old_object;
    zend_object *new_object;

    old_object = Z_OBJ_P(object);

    new_object = old_object->ce->create_object(old_object->ce);

    php_ref_reference_t *old_reference = php_ref_reference_fetch_object(old_object);
    php_ref_reference_t *new_reference = php_ref_reference_fetch_object(new_object);

    ZVAL_OBJ(&new_reference->this_ptr, new_object);
    ZVAL_COPY(&new_reference->notifier, &old_reference->notifier);
    new_reference->notifier_type = old_reference->notifier_type;

    if (old_reference->referent) {
        old_reference->register_reference(new_reference, old_reference->referent);
    }

    zend_objects_clone_members(new_object, old_object);

    return new_object;
} /* }}} */

static HashTable *php_ref_get_debug_info(zval *object, int *is_temp) /* {{{ */
{
    HashTable *debug_info;
    zend_string *key;
    HashTable *props;

    PHP_REF_REFERENCE_FETCH_INTO(object, reference);
    *is_temp = 1;
    props = Z_OBJPROP_P(object);

    ALLOC_HASHTABLE(debug_info);
    ZEND_INIT_SYMTABLE_EX(debug_info, zend_hash_num_elements(props) + 2, 0);

    zend_hash_copy(debug_info, props, (copy_ctor_func_t) zval_add_ref);

    key = zend_mangle_property_name(ZSTR_VAL(this_ce->name), ZSTR_LEN(this_ce->name), "referent",
                                    sizeof("referent") - 1, 0);

    if (NULL != reference->referent) {
        zend_symtable_update(debug_info, key, &reference->referent->this_ptr);
        Z_TRY_ADDREF(reference->referent->this_ptr);
    } else {
        zval tmp;
        ZVAL_NULL(&tmp);
        zend_symtable_update(debug_info, key, &tmp);
    }

    zend_string_release(key);

    key = zend_mangle_property_name(ZSTR_VAL(this_ce->name), ZSTR_LEN(this_ce->name), "notifier",
                                    sizeof("notifier") - 1, 0);
    zend_symtable_update(debug_info, key, &reference->notifier);
    Z_TRY_ADDREF(reference->notifier);

    zend_string_release(key);

    return debug_info;
}/* }}} */

static int php_ref_compare_objects(zval *object1, zval *object2) /* {{{ */
{
    zval result;
    int res;

    PHP_REF_REFERENCE_FETCH_INTO(object1, reference1);
    PHP_REF_REFERENCE_FETCH_INTO(object2, reference2);

    /* Compare referent objects */
    if (NULL == reference1->referent && NULL == reference2->referent) {
        /* skip */
    } else if(NULL == reference1->referent) {
        return 1;
    } else if (NULL == reference2->referent) {
        return -1;
    } else {
        res = std_object_handlers.compare_objects(&reference1->referent->this_ptr, &reference2->referent->this_ptr);

        if (res != 0) {
            return res;
        }
    }

    /* Compare notifiers */
    ZVAL_LONG(&result, 0);

    compare_function(&result, &reference1->notifier, &reference2->notifier);

    if (Z_LVAL(result) != 0) {
        return (int) Z_LVAL(result);
    }

    /* Compare standard objects */
    return std_object_handlers.compare_objects(object1, object2);
} /* }}} */

static PHP_METHOD(WeakReference, __construct)  /* {{{ */
{
    zval *referent_zv;
    zval *notifier_zv = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "o|z!", &referent_zv, &notifier_zv) == FAILURE) {
        return;
    }

    php_ref_reference_init(getThis(), referent_zv, notifier_zv);
} /* }}} */

static PHP_METHOD(WeakReference, get)  /* {{{ */
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    PHP_REF_REFERENCE_FETCH_INTO(getThis(), reference);

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

    PHP_REF_REFERENCE_FETCH_INTO(getThis(), reference);

    RETURN_BOOL(NULL != reference->referent);
} /* }}} */

static PHP_METHOD(WeakReference, notifier)  /* {{{ */
{
    zval *notifier_zv = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "|z!", &notifier_zv) == FAILURE) {
        return;
    }

    PHP_REF_REFERENCE_FETCH_INTO(getThis(), reference);

    if (ZEND_NUM_ARGS() < 1) {
        RETURN_ZVAL(&reference->notifier, 1, 0);
    }

    /* Change existent notifier */

    int notifier_type = php_ref_reference_check_notifier(notifier_zv, getThis());

    if (PHP_REF_NOTIFIER_INVALID == notifier_type) {
        return;
    }

    RETVAL_ZVAL(&reference->notifier, 1, 1);

    if (NULL == notifier_zv) {
        ZVAL_NULL(&reference->notifier);
    } else {
        ZVAL_COPY(&reference->notifier, notifier_zv);
    }

    reference->notifier_type = notifier_type;

} /* }}} */


ZEND_BEGIN_ARG_INFO_EX(arginfo_ref_reference___construct, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 1)
                ZEND_ARG_INFO(0, referent)
                ZEND_ARG_INFO(0, notify)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_ref_reference_get, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_ref_reference_valid, ZEND_RETURN_VALUE, 0, _IS_BOOL, NULL, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_ref_reference_notifier, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 0)
                ZEND_ARG_INFO(0, notify)
ZEND_END_ARG_INFO()


static const zend_function_entry php_ref_abstract_reference_methods[] = { /* {{{ */
        PHP_ME(WeakReference, __construct, arginfo_ref_reference___construct, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)

        PHP_ME(WeakReference, get, arginfo_ref_reference_get, ZEND_ACC_PUBLIC)
        PHP_ME(WeakReference, valid, arginfo_ref_reference_valid, ZEND_ACC_PUBLIC)
        PHP_ME(WeakReference, notifier, arginfo_ref_reference_notifier, ZEND_ACC_PUBLIC)

        PHP_FE_END
}; /* }}} */

static const zend_function_entry php_ref_weak_reference_methods[] = { /* {{{ */
        PHP_FE_END
}; /* }}} */

static const zend_function_entry php_ref_soft_reference_methods[] = { /* {{{ */
        PHP_FE_END
}; /* }}} */


PHP_MINIT_FUNCTION (php_ref_reference) /* {{{ */
{
    zend_class_entry ce;

    INIT_NS_CLASS_ENTRY(ce, PHP_REF_NS, "AbstractReference", php_ref_abstract_reference_methods);
    this_ce = zend_register_internal_class(&ce);
    this_ce->ce_flags |= ZEND_ACC_EXPLICIT_ABSTRACT_CLASS;
    this_ce->create_object = php_ref_abstract_reference_ctor;
    this_ce->serialize = zend_class_serialize_deny;
    this_ce->unserialize = zend_class_unserialize_deny;

    memcpy(&php_ref_reference_object_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));

    php_ref_reference_object_handlers.offset = XtOffsetOf(php_ref_reference_t, std);
    php_ref_reference_object_handlers.free_obj = php_ref_reference_free;
    php_ref_reference_object_handlers.dtor_obj = php_ref_reference_dtor;
    php_ref_reference_object_handlers.get_gc = php_ref_reference_gc;
    php_ref_reference_object_handlers.clone_obj = php_ref_reference_clone_obj;
    php_ref_reference_object_handlers.get_debug_info = php_ref_get_debug_info;
    php_ref_reference_object_handlers.compare_objects = php_ref_compare_objects;

    INIT_NS_CLASS_ENTRY(ce, PHP_REF_NS, "SoftReference", php_ref_weak_reference_methods);
    php_ref_soft_reference_class_entry = zend_register_internal_class_ex(&ce, php_ref_abstract_reference_class_entry);
    php_ref_soft_reference_class_entry->create_object = php_ref_soft_reference_ctor;

    INIT_NS_CLASS_ENTRY(ce, PHP_REF_NS, "WeakReference", php_ref_weak_reference_methods);
    php_ref_weak_reference_class_entry = zend_register_internal_class_ex(&ce, php_ref_abstract_reference_class_entry);
    php_ref_weak_reference_class_entry->create_object = php_ref_weak_reference_ctor;

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
