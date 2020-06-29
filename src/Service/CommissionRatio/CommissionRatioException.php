<?php

namespace App\Service\CommissionRatio;

use \App\Contract\CommissionRatio\CommissionRatioException as CommissionRatioContract;
use App\Service\AppException;

class CommissionRatioException extends AppException implements CommissionRatioContract
{

}