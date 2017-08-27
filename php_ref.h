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

#ifndef PHP_REF_H
#define PHP_REF_H

#include "php.h"

extern zend_module_entry php_ref_module_entry;
#define phpext_ref_ptr &php_ref_module_entry

#ifndef PHP_REF_VERSION
#define PHP_REF_VERSION "0.5.0"
#endif

#ifndef PHP_REF_REVISION
#define PHP_REF_REVISION "dev"
#endif

#if PHP_VERSION_ID < 70100
// should never get her, but just in case
#error PHP >= 7.1 required
#endif

#define PHP_REF_NS "Ref"

#ifdef PHP_WIN32
#    define PHP_REF_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#    define PHP_REF_API __attribute__ ((visibility("default")))
#else
#    define PHP_REF_API
#endif


// Add zend_type support (new since PHP 7.2)
#ifdef ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX
    #define PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(name, return_reference, required_num_args, classname, allow_null) \
                    ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(name, return_reference, required_num_args, classname, allow_null)

    #define PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, type, allow_null) \
                    ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, type, allow_null)
#else
    #define PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(name, return_reference, required_num_args,             classname, allow_null) \
                   ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, IS_OBJECT, #classname, allow_null)

    #define PHP_REF_ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, type,       allow_null) \
                    ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(name, return_reference, required_num_args, type, NULL, allow_null)
#endif


#ifdef ZTS
#include "TSRM.h"
#endif

ZEND_BEGIN_MODULE_GLOBALS(ref)
    HashTable *referents;
ZEND_END_MODULE_GLOBALS(ref)

ZEND_EXTERN_MODULE_GLOBALS(ref);
#define PHP_REF_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(ref, v)

#if defined(ZTS) && defined(COMPILE_DL_REF)
ZEND_TSRMLS_CACHE_EXTERN();
#endif

#endif /* PHP_REF_H */
