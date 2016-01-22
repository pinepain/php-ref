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

class Reference
{
    /**
     * @param object                  $referent Referent object
     * @param callable | array | null $notify   Optional notifier to signal when referent object destroyed.
     *
     * If notifier is callback, it will be called with a current Weak\Reference object as a first argument. Note, that
     * referent object at that time will be already destroyed. Callback will not be called if referent object destructor
     * or previous notify callback throws exception.
     *
     * If notifier is array, current Weak\Reference object will be appended to it regardless any exception thrown by
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

    /**
     * Get referent object. This method is alias of Reference::get().
     *
     * @return object | null
     */
    public function __invoke()
    {
    }
}
