<?php

namespace App\Console\Commands;

use App\Models\ApiKeys;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;

class GenerateKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:keys {count=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate api keys and inserts it into database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');
        $keys = [];
        for ($i = 0; $i < $count; $i++) {
            $keys[] = [
                'key' => 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher'))),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $keys = array_chunk($keys, 10);
        foreach ($keys as $ks) {
            ApiKeys::insert($ks);
        }

        $word = ($count == 1) ? 'key' : 'keys';
        $this->info("{$count} {$word} added successfully.");
    }
}
