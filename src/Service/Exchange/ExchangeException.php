<?php

namespace App\Service\Exchange;

use \App\Contract\Exchange\ExchangeException as ExchangeExceptionContract;
use App\Service\AppException;

class ExchangeException extends AppException implements ExchangeExceptionContract
{

}