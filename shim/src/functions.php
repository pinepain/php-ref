<?php declare(strict_types=1);

/*
 * This file is part of the pinepain/php-ref PHP extension.
 *
 * Copyright (c) 2016-2018 Bogdan Padalko <pinepain@gmail.com>
 *
 * Licensed under the MIT license: http://opensource.org/licenses/MIT
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source or visit
 * http://opensource.org/licenses/MIT
 */


namespace Ref;


/**
 * @internal
 *
 * @param string|null $kind
 *
 * @return array
 */
function __storage(string $kind = null)
{
    static $__storage = ['soft' => [], 'weak' => []];

    if ($kind) {
        return $__storage[$kind];
    }

    return $__storage;
}


/**
 * Check whether value is refcounted
 *
 * @param mixed $value
 *
 * @return bool
 */
function refcounted($value): bool
{
    return \is_object($value);
}

/**
 * Get real value references count (not counting value reference passed as a function argument)
 *
 * @param mixed $value
 *
 * @return int
 */
function refcount($value): int
{
    if (!\is_object($value)) {
        return 0;
    }

    ob_start();
    debug_zval_dump($value);
    $res = ob_get_clean();

    preg_match('/^object\(.+\)#\d+ \(\d+\) refcount\((\d+)\){/', $res, $matches);
    assert(count($matches) == 2);

    return $matches[1] - 1;
}

/**
 * Check whether object has soft references
 *
 * @param object $value
 *
 * @return bool
 */
function softrefcounted($value): bool
{
    if (!\is_object($value)) {
        return false;
    }

    return isset(__storage('soft')[spl_object_hash($value)];
}

/**
 * Get object's soft references count
 *
 * @param object $value
 *
 * @return int
 */
function softrefcount($value): int
{
    if (!softrefcounted($value)) {
        return 0;
    }

    return count(__storage('soft')[spl_object_hash($value)]);
}

/**
 * Get object's soft references
 *
 * @param object $value
 *
 * @return mixed
 */
function softrefs(object $value): array
{
    if (!softrefcounted($value)) {
        return [];
    }

    return __storage('soft')[spl_object_hash($value)];
}

/**
 * Check whether object has weak references
 *
 * @param object $value
 *
 * @return bool
 */
function weakrefcounted(object $value): bool
{
    if (!refcounted($value)) {
        return false;
    }

    return isset(__storage('weak')[spl_object_hash($value)]);
}

/**
 * Get object's weak references count
 *
 * @param object $value
 *
 * @return int
 */
function weakrefcount($value): int
{
    if (!weakrefcounted($value)) {
        return 0;
    }

    return count(__storage('weak')[spl_object_hash($value)]);
}

/**
 * Get object's weak references
 *
 * @param object $value
 *
 * @return mixed
 */
function weakrefs($value): array
{
    if (!weakrefcounted($value)) {
        return [];
    }

    return __storage('weak')[spl_object_hash($value)];
}

/**
 * Get object's handle id
 *
 * @param object $value
 *
 * @return int
 */
function object_handle($value): int
{
    if (!\is_object($value)) {
        return 0;
    }

    ob_start();
    debug_zval_dump($value);
    $res = ob_get_clean();

    preg_match('/^object\(.+\)#\d+ \((\d+\)) refcount\(\d+\){/', $res, $matches);
    assert(count($matches) == 2);

    return $matches[1];
}

/**
 * Check whether object's destructor was called
 *
 * @param object $value
 *
 * @return bool
 */
function is_obj_destructor_called(object $value): bool
{
    return false;
}
