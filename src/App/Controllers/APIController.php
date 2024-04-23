<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Exceptions\APIValidationException;
use App\Services\ValidatorService;

class APIController
{
    private ValidatorService $validatorService;

    public function __construct(
        ValidatorService $validatorService,
    ) {
        $this->validatorService = $validatorService;
    }

    public function trans()
    {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'id' => 1,
                'amount' => 100,
                'currency' => 'USD',
                'status' => 'approved'
            ]
        ]);
    }

    public function transParams(array $params)
    {
        if ($params['id'] != 5)
            throw new APIValidationException([
                'id' => 'Transaction not found'
            ]);

        echo json_encode([
            'status' => 'success',
            'data' => [
                'amount' => 100,
                'currency' => 'USD',
                'status' => 'approved'
            ]
        ]);
    }

    public function transParams2(array $params)
    {
        if ($params['id'] != 5)
            throw new APIValidationException([
                'id' => 'Transaction not found'
            ]);

        echo json_encode([
            'status' => 'success',
        ]);
    }
}
