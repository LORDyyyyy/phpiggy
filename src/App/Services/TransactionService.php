<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(array $formData): void
    {
        $formattedDate = "{$formData['date']} 00:00:00";

        $this->database->query(
            "INSERT INTO transactions (user_id, amount, description, date)
            VALUES (:user_id, :amount, :description, :date)",
            [
                'user_id' => $_SESSION['user'],
                'amount' => $formData['amount'],
                'description' => $formData['description'],
                'date' => $formattedDate
            ]
        );
    }

    public function getTransactions(int $length, int $offset): array
    {
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_');
        $params = [
            'user_id' => $_SESSION['user'],
            'searchTerm' => "%{$searchTerm}%"
        ];

        $transactions =  $this->database->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
            FROM transactions
            WHERE user_id = :user_id
            AND description LIKE :searchTerm
            LIMIT {$length} OFFSET {$offset}",
            $params
        )->findAll();

        $transactions = array_map(function (array $transaction) {
            $transaction['receipts'] = $this->database->query(
                "SELECT *
                FROM receipts
                WHERE transaction_id = :transaction_id",
                ['transaction_id' => $transaction['id']]
            )->findAll();

            return $transaction;
        }, $transactions);

        $transactionCount = $this->database->query(
            "SELECT COUNT(*)
            FROM transactions
            WHERE user_id = :user_id
            AND description LIKE :searchTerm",
            $params
        )->count();

        return [$transactions, $transactionCount];
    }

    public function getTransaction(string $id): array | false
    {
        return $this->database->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
            FROM transactions
            WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->find();
    }

    public function edit(int $id, array $formData): void
    {
        $formattedDate = "{$formData['date']} 00:00:00";

        $this->database->query(
            "UPDATE transactions
            SET amount = :amount,
                description = :description,
                date = :date
            WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user'],
                'amount' => $formData['amount'],
                'description' => $formData['description'],
                'date' => $formattedDate
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->database->query(
            "DELETE FROM transactions
            WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        );
    }
}
