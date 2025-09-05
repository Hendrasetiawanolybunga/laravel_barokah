<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all 
                            {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan semua cache aplikasi (config, routes, views, application cache)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Perintah ini tidak bisa dijalankan di production tanpa --force flag.');
            return 1;
        }

        $this->info('🔄 Membersihkan semua cache...');

        // Clear config cache
        $this->call('config:clear');
        $this->info('✅ Config cache dibersihkan');

        // Clear route cache
        $this->call('route:clear');
        $this->info('✅ Route cache dibersihkan');

        // Clear view cache
        $this->call('view:clear');
        $this->info('✅ View cache dibersihkan');

        // Clear application cache
        $this->call('cache:clear');
        $this->info('✅ Application cache dibersihkan');

        // Clear compiled services and packages
        if (file_exists(bootstrap_path('cache/services.php'))) {
            unlink(bootstrap_path('cache/services.php'));
            $this->info('✅ Services cache dibersihkan');
        }

        if (file_exists(bootstrap_path('cache/packages.php'))) {
            unlink(bootstrap_path('cache/packages.php'));
            $this->info('✅ Packages cache dibersihkan');
        }

        // Cache config untuk performa (opsional)
        if (app()->environment('production')) {
            $this->call('config:cache');
            $this->info('✅ Config di-cache ulang untuk production');
        }

        $this->info('🎉 Semua cache berhasil dibersihkan!');
        $this->info('💡 Pastikan untuk restart web server jika menggunakan OPcache');

        return 0;
    }
}