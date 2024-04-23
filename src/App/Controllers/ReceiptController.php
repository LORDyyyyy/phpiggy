<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\{
    TransactionService,
    ReceiptService,
    ValidatorService
};

class ReceiptController
{
    private TemplateEngine $view;
    private TransactionService $transactionService;
    private ReceiptService $receiptService;
    private ValidatorService $validator;

    public function __construct(
        TemplateEngine $view,
        TransactionService $transactionService,
        ReceiptService $receiptService,
        ValidatorService $validator
    ) {
        $this->view = $view;
        $this->transactionService = $transactionService;
        $this->receiptService = $receiptService;
        $this->validator = $validator;
    }

    public function uploadView(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        echo $this->view->render("receipts/create.php");
    }

    public function upload(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        $receiptFile = $_FILES['receipt'] ?? null;

        $this->validator->validateFileUpload($_FILES);

        // $this->receiptService->validateFile($receiptFile);

        $this->receiptService->uploadReceipt(
            $receiptFile,
            (int) $params['transaction']
        );

        redirectTo("/");
    }

    public function download(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        $receipt = $this->receiptService->getReceipt(
            $params['receipt']
        );

        if (empty($receipt)) {
            redirectTo("/");
        }

        if ((int) $receipt['transaction_id'] !== (int) $params['transaction']) {
            redirectTo("/");
        }

        $this->receiptService->read($receipt);
    }

    public function delete(array $params)
    {
        // Middleware: TransactionExistsMiddleware

        $receipt = $this->receiptService->getReceipt(
            $params['receipt']
        );

        if (empty($receipt)) {
            redirectTo("/");
        }

        if ((int) $receipt['transaction_id'] !== (int) $params['transaction']) {
            redirectTo("/");
        }

        $this->receiptService->delete($receipt);

        redirectTo("/");
    }
}
