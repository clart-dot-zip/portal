<?php

namespace App\Console\Commands;

use App\Services\Pim\PimService;
use Illuminate\Console\Command;

class PimEnforceCommand extends Command
{
    protected $signature = 'pim:enforce';

    protected $description = 'Deactivate expired PIM activations.';

    public function handle(PimService $pimService): int
    {
        if (!$pimService->isEnabled()) {
            $this->info('PIM is disabled.');
            return self::SUCCESS;
        }

        $pimService->enforceExpirations();
        $this->info('Checked for expired PIM activations.');

        return self::SUCCESS;
    }
}
