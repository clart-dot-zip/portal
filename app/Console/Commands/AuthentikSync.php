<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Authentik\AuthentikSDK;
use App\Models\User;

class AuthentikSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authentik:sync {--users : Sync users only} {--groups : Sync groups only} {--all : Sync everything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from Authentik to local database';

    protected ?AuthentikSDK $authentik;

    public function __construct(?AuthentikSDK $authentik = null)
    {
        parent::__construct();
        $this->authentik = $authentik;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->authentik) {
            $this->error('Authentik SDK is not properly configured. Please check your configuration.');
            return 1;
        }

        $syncUsers = $this->option('users') || $this->option('all');
        $syncGroups = $this->option('groups') || $this->option('all');

        if (!$syncUsers && !$syncGroups) {
            $this->error('Please specify what to sync: --users, --groups, or --all');
            return 1;
        }

        try {
            if ($syncUsers) {
                $this->syncUsers();
            }

            if ($syncGroups) {
                $this->syncGroups();
            }

            $this->info('Sync completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function syncUsers()
    {
        $this->info('Syncing users from Authentik...');
        
        $users = $this->authentik->users()->all();
        $bar = $this->output->createProgressBar(count($users));

        foreach ($users as $authentikUser) {
            // Update or create local user record
            User::updateOrCreate(
                ['email' => $authentikUser['email']],
                [
                    'name' => $authentikUser['name'] ?? $authentikUser['username'],
                    'authentik_id' => $authentikUser['pk'],
                    'username' => $authentikUser['username'],
                    'is_active' => $authentikUser['is_active'],
                    'last_login' => $authentikUser['last_login'] ? 
                        \Carbon\Carbon::parse($authentikUser['last_login']) : null,
                ]
            );
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Synced " . count($users) . " users");
    }

    protected function syncGroups()
    {
        $this->info('Syncing groups from Authentik...');
        
        $groups = $this->authentik->groups()->all();
        
        $this->table(
            ['ID', 'Name', 'Parent', 'Members'],
            array_map(function ($group) {
                return [
                    $group['pk'],
                    $group['name'],
                    $group['parent_name'] ?? 'None',
                    $group['num_pk'] ?? 0
                ];
            }, $groups)
        );

        $this->info("Found " . count($groups) . " groups");
    }
}
