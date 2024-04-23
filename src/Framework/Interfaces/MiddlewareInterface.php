<?php

declare(strict_types=1);

namespace Framework\Interfaces;

/**
 * Interface MiddlewareInterface
 * 
 * Represents a middleware interface that can process a request and call the next middleware in the chain.
 */
interface MiddlewareInterface
{
    /**
     * Process the request and call the next middleware in the chain.
     * 
     * @param callable $next The next middleware to be called.
     * @return mixed The response returned by the next middleware.
     */
    public function process(callable $next);
}
