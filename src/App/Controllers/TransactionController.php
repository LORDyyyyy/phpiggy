<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    ValidatorService,
    TransactionService
};

class TransactionController
{
    private TemplateEngine $view;
    private ValidatorService $validator;
    private TransactionService $transactionService;

    public function __construct(
        TemplateEngine $view,
        ValidatorService $validator,
        TransactionService $transactionService
    ) {
        $this->view = $view;
        $this->validator = $validator;
        $this->transactionService = $transactionService;
    }

    public function createView()
    {
        echo $this->view->render("transactions/create.php");
    }

    public function create()
    {
        $this->validator->validateTransaction($_POST);

        $this->transactionService->create($_POST);

        redirectTo('/');
    }

    public function editView(array &$params)
    {
        // Middleware: TransactionExistsMiddleware

        $transaction = $this->transactionService->getTransaction(
            $params['transaction']
        );


        echo var_dump($params) . "<br>";
        echo $this->view->render("transactions/edit.php", [
            'transaction' => $transaction
        ]);
    }

    public function edit(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        $this->validator->validateTransaction($_POST);

        $this->transactionService->edit(
            (int) $params['transaction'],
            $_POST
        );

        redirectTo('.');
    }

    public function delete(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        $this->transactionService->delete((int) $params['transaction']);

        redirectTo('.');
    }
}
