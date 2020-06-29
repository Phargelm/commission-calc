<?php

namespace App\Service\Transaction;

use \App\Contract\Transaction\TransactionException as TransactionExceptionContract;
use App\Service\AppException;

class TransactionException extends AppException implements TransactionExceptionContract
{

}