<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Interfaces\MiddlewareWithParamsInterface;
use App\Services\TransactionService;

/**
 * Middleware that checks if a transaction exists before allowing access to a route.
 */
class TransactionExistsMiddleware implements MiddlewareWithParamsInterface
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function process(callable $next, array $params)
    {
        $transaction = $this->transactionService->getTransaction(
            $params['transaction']
        );

        if (!$transaction) {
            redirectTo('/');
        }

        $next($params);
    }
}
