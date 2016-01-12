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
 * Stores a weak-reference to an object, and optionally a callback to trigger when the referent is no longer available.
 *
 * Two Reference objects are considered loosely-equal if they point to the same referent, regardless of the callback either one was constructed with.
 */
class Reference
{
    /**
     * @param object        $referent Referent object
     * @param callable|null $notify   Callback to notify when referent object destroyed.
     *
     * Callback will receive current Weak\Reference object as a first argument. Note, that referent object at that time
     * will be already destroyed.
     */
    public function __construct(object $referent, callable $notify = null)
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
}
