/*
  +----------------------------------------------------------------------+
  | This file is part of the pinepain/php-ref PHP extension.            |
  |                                                                      |
  | Copyright (c) 2016 Bogdan Padalko <pinepain@gmail.com>            |
  |                                                                      |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT   |
  |                                                                      |
  | For the full copyright and license information, please view the      |
  | LICENSE file that was distributed with this source or visit          |
  | http://opensource.org/licenses/MIT                                   |
  +----------------------------------------------------------------------+
*/

#include "php_ref_notifier_exception.h"
#include "php_ref.h"
#include "zend_exceptions.h"


zend_class_entry *php_ref_notifier_exception_class_entry;
#define this_ce php_ref_notifier_exception_class_entry


static zend_object *php_ref_notifier_exception_ctor(zend_class_entry *ce) /* {{{ */
{
    zval obj, thrown;
    zend_object *object;

    Z_OBJ(obj) = object = ce->parent->create_object(ce);

    array_init_size(&thrown, 0);
    zend_update_property(php_ref_notifier_exception_class_entry, &obj, ZEND_STRL("exceptions"), &thrown);

    return object;
} /* }}} */


void php_ref_create_notifier_exception(zval *exception, const char *message, zval *thrown) /* {{{ */
{
    object_init_ex(exception, this_ce);
    zend_update_property_string(zend_ce_exception, exception, ZEND_STRL("message"), message);
    zend_update_property(php_ref_notifier_exception_class_entry, exception, ZEND_STRL("exceptions"), thrown);
} /* }}} */

static PHP_METHOD(NotifierException, __construct)  /* {{{ */
{
	zend_string *message = NULL;
	zend_long   code = 0;

	zval  tmp;
    zval *exceptions = NULL;
    zval *previous = NULL;

    if (zend_parse_parameters(ZEND_NUM_ARGS(), "|SalO!", &message, &exceptions, &code, &previous, zend_ce_throwable) == FAILURE) {
        return;
    }

    if (message) {
		zend_update_property_str(zend_ce_exception, getThis(), ZEND_STRL("message"), message);
	}

    if (exceptions) {
        zend_update_property(this_ce, getThis(), ZEND_STRL("exceptions"), exceptions);
    } else {
        array_init_size(&tmp, 0);
        zend_update_property(this_ce, getThis(), ZEND_STRL("exceptions"), &tmp);
    }

    if (code) {
        zend_update_property_long(zend_ce_exception, getThis(), ZEND_STRL("code"), code);
	}

	if (previous) {
        zend_update_property(zend_ce_exception, getThis(), ZEND_STRL("previous"), previous);
    }
}

static PHP_METHOD(NotifierException, getExceptions)  /* {{{ */
{
    zval rv;

    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    RETVAL_ZVAL(zend_read_property(php_ref_notifier_exception_class_entry, getThis(), ZEND_STRL("exceptions"), 0, &rv), 1, 0);
} /* }}} */


ZEND_BEGIN_ARG_INFO_EX(arginfo_notifier_exception___construct, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 0)
	ZEND_ARG_INFO(0, message)
    ZEND_ARG_INFO(0, exceptions)
	ZEND_ARG_INFO(0, code)
	ZEND_ARG_INFO(0, previous)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_notifier_exception_getExceptions, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 0)
ZEND_END_ARG_INFO()


static const zend_function_entry php_ref_notifier_exception_methods[] = { /* {{{ */
        PHP_ME(NotifierException, __construct,   arginfo_notifier_exception___construct,   ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
        PHP_ME(NotifierException, getExceptions, arginfo_notifier_exception_getExceptions, ZEND_ACC_PUBLIC)

        PHP_FE_END
}; /* }}} */


PHP_MINIT_FUNCTION (php_ref_notifier_exception) /* {{{ */
{
    zend_class_entry ce;

    INIT_NS_CLASS_ENTRY(ce, PHP_REF_NS, "NotifierException", php_ref_notifier_exception_methods);
    this_ce = zend_register_internal_class_ex(&ce, zend_ce_exception);
    /*this_ce->create_object = php_ref_notifier_exception_ctor;*/

    zend_declare_property_null(this_ce, ZEND_STRL("exceptions"), ZEND_ACC_PRIVATE);


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
