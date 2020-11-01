<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');


        file_put_contents(app_path('Services/' . $name . '.php'), "<?php

namespace App\Services;

class {$name} extends Service 
{
    /**
     * Keys or fields that should be present on
     * the data.
     * @var array
     */
    protected \$keys = [];


}");

        $this->line("$name Service successfully created.");

        return 0;
    }
}
