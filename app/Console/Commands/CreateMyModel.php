<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateMyModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:my_model {model} {--p|parent= : Parent for nested model} {--a|api : It is a API Controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create My Model, migration, factory, seeder and controller ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $model = ucwords($this->argument('model'));

        $parent = $this->option('parent');

        if ($parent) {
            $parent = substr($parent, 1);
        }

        Artisan::call('make:model', [
            'name'        => $model,
            '--factory'   => true,
            '--migration' => true
        ]);
        $this->line("\App\\{$model} created!");
        $this->line("{$model}Factory is created");
        $this->line("migration is created");


        Artisan::call('make:seeder', [
            'name' => str_plural($model) . "TableSeeder"
        ]);

        $this->line("DB seeder is created");


        $controllerName = str_plural($model) . "Controllers";
        $controllerOptions = [
            'name' => $controllerName,
            '-m'   => "\App\\" . $model,
        ];

        if ($this->option('api')) {
            $controllerOptions["--api"] = true;
        } else {
            $controllerOptions["--resource"] = true;
        }
        if ($parent) {
            $controllerOptions["--parent"] = "\App\\" . $parent;
        }

        Artisan::call('make:controller', $controllerOptions);


        $this->line("{$controllerName} is created!");

    }
}
