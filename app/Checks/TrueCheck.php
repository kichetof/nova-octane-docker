<?php

namespace App\Checks;

use Laravel\Nova\Nova;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class TrueCheck extends Check
{
    public function run(): Result
    {
        $result = Result::make();
        $result->shortSummary(
            sprintf('Nova translations "Create :resource" = %s | "Update :resource" = %s',
                Nova::__('Create :resource', ['resource' => 'custom']),
                Nova::__('Update :resource', ['resource' => 'custom'])
            )
        );

        return $result->ok();
    }
}
