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
 * Class AbstractReference
 *
 * Abstract base class for reference objects. This class defines the operations common to all reference objects.
 * Because of reference objects implementation details which depends on how GC in PHP works, this class may not be
 * subclassed directly.
 */
abstract class AbstractReference
{
    /**
     * @param object                  $referent Referent object
     * @param callable | array | null $notify   Optional notifier to signal when referent object destroyed.
     *
     * If notifier is callback, it will be called with a current reference object as a first argument.
     *
     * For SoftReference, notifiers are called before object will (or will not) be destructed. If referent object
     * prevented from being destroyed (regular reference to it created during SoftReference notifiers calling), original
     * object's destructor will not be called and next time referent object refcount will reach 0 it will be a subject of
     * full destructing cycle again. For WeakReference, referent object will be already destroyed at the time of
     * notifiers calling.
     *
     * If notifier is array, current reference object will be appended to it regardless any exception thrown by
     * referent object destructor or previous notify callback.
     */
    public function __construct(object $referent, $notify = null)
    {
    }

    /**
     * Get referent object
     *
     * @return object | null
     */
    public function get()
    {
    }

    /**
     * Whether referent object exists
     *
     * @return bool
     */
    public function valid() : bool
    {
    }

    /**
     * Get notifier
     *
     * @param callable | array | null $notify Notifier to replace existent notifier with. Same as in constructor.
     *
     * If any value provided, any existent notifier will be replaced and returned.
     *
     * @return callable | array | null Current notifier or the old one when replacing it with provided value.
     */
    public function notifier($notify = null)
    {
    }
}
