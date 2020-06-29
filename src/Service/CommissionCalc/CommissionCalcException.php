<?php

namespace App\Service\CommissionCalc;

use \App\Contract\CommissionCalc\CommissionCalcException as CommissionCalcExceptionContract;
use App\Service\AppException;

class CommissionCalcException extends AppException implements CommissionCalcExceptionContract
{

}