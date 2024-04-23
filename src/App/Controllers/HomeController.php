<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\TemplateEngine;
use App\Services\TransactionService;

class HomeController
{
    private TemplateEngine $view;
    private TransactionService $transactionService;

    public function __construct(TemplateEngine $view, TransactionService $transactionService)
    {
        $this->view = $view;
        $this->transactionService = $transactionService;
    }

    public function home()
    {
        $page = $_GET['p'] ?? 1;
        $page = intval($page);
        $length = 3;
        $offset = ($page - 1) * $length;

        $searchTerm = $_GET['s'] ?? null;

        [$transactions, $transactionCount] = $this->transactionService
            ->getTransactions($length, $offset);

        $lastPage = ceil($transactionCount / $length);
        $pages = $lastPage ? range(1, $lastPage) : [];

        $pageLinks = array_map(
            fn ($pageNum) => http_build_query([
                'p' => $pageNum,
                's' => $searchTerm
            ]),
            $pages
        );

        echo $this->view->render("/index.php", [
            'title' => 'HomePage',
            'transactions' => $transactions,
            'currentPage' => $page,
            'prevPageQuery' => http_build_query([
                'p' => $page - 1,
                's' => $searchTerm
            ]),
            'lastPage' => $lastPage,
            'nextPageQuery' => http_build_query([
                'p' => $page + 1,
                's' => $searchTerm
            ]),
            'pageLinks' => $pageLinks,
            'searchTerm' => $searchTerm
        ]);
    }
}
