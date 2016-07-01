<?php

/*
 * This file is part of the pinepain/php-weak PHP extension.
 *
 * Copyright (c) 2016 Bogdan Padalko <zaq178miami@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit http://opensource.org/licenses/MIT
 */


namespace Weak;

/**
 * Check whether value is refcounted
 *
 * @param mixed $value
 *
 * @return bool
 */
function refcounted(mixed $value) : bool {}

/**
 * Get real value references count (not counting value reference passed as a function argument)
 *
 * @param mixed $value
 *
 * @return int
 */
function refcount(mixed $value) : int {}

/**
 * Check whether object has weak references
 *
 * @param object $value
 *
 * @return bool
 */
function weakrefcounted(object $value) : bool {}

/**
 * Get object's weak references count

 * @param object $value
 *
 * @return int
 */
function weakrefcount(object $value) : int {}

/**
 * Get object's weak references
 *
 * @param object $value
 *
 * @return mixed
 */
function weakrefs(object $value) : array {}

/**
 * Get object's handle id
 *
 * @param object $value
 *
 * @return int
 */
function object_handle(object $value) : int {}
