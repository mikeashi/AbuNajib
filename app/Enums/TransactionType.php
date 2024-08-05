<?php
namespace App\Enums;


enum TransactionType: string
{
    case Credit = 'Credit';
    case Debit = 'Debit';
}