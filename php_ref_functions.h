/*
  +----------------------------------------------------------------------+
  | This file is part of the pinepain/php-ref PHP extension.             |
  |                                                                      |
  | Copyright (c) 2016 Bogdan Padalko <pinepain@gmail.com>               |
  |                                                                      |
  | Licensed under the MIT license: http://opensource.org/licenses/MIT   |
  |                                                                      |
  | For the full copyright and license information, please view the      |
  | LICENSE file that was distributed with this source or visit          |
  | http://opensource.org/licenses/MIT                                   |
  +----------------------------------------------------------------------+
*/

#ifndef PHP_REF_FUNCTIONS_H
#define PHP_REF_FUNCTIONS_H

#include "php.h"

#ifdef ZTS
#include "TSRM.h"
#endif

extern const zend_function_entry php_ref_functions[];

#endif /* PHP_REF_FUNCTIONS_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
