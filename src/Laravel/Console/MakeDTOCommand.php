<?php

namespace Ol3x1n\DataTransferObject\Laravel\Console;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeDTOCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto
        {name : Class name or path. Examples: UserDTO, Order/CreateOrderDTO, App\Integrations\PaymentDTO}
        {--force : Overwrite file if it exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Data Transfer Object class';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files): int
    {
        $input = trim((string) $this->argument('name'));
        $input = str_replace('/', '\\', $input); // support both separators

        // Base namespace for DTOs if full path is not provided
        $baseNamespace = 'App\\DTO';

        $isFqcn = Str::startsWith($input, 'App\\');

        $fqcn = $isFqcn
            ? $input
            : trim($baseNamespace . '\\' . ltrim($input, '\\'), '\\');

        $class = class_basename($fqcn);
        $namespace = Str::beforeLast($fqcn, '\\') ?: $baseNamespace;

        $relative = Str::after($fqcn, 'App\\');
        $path = app_path(str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php');

        if ($files->exists($path) && ! $this->option('force')) {
            $this->components->error("DTO already exists: {$path}");
            return self::FAILURE;
        }

        $files->ensureDirectoryExists(dirname($path));

        $stub = $this->resolveStub($files);

        $contents = strtr($stub, [
            '{{ namespace }}' => $namespace,
            '{{ class }}'     => $class,
        ]);

        $files->put($path, $contents);

        $this->components->info("DTO created successfully: {$fqcn}");
        $this->components->twoColumnDetail('Path', $path);

        return self::SUCCESS;
    }

    /**
     * @throws FileNotFoundException
     */
    private function resolveStub(Filesystem $files): string
    {
        $published = base_path('stubs/dto.stub');
        if ($files->exists($published)) {
            return strval($files->get($published));
        }

        $packageStub = __DIR__ . '/../../../stubs/dto.stub';

        if ($files->exists($packageStub)) {
            return strval($files->get($packageStub));
        }
        return $this->defaultStubContent();
    }

    private function defaultStubContent(): string
    {
        return <<<'PHP'
<?php

namespace {{ namespace }};

use Ol3x1n\DataTransferObject\AbstractDTO;
use Ol3x1n\DataTransferObject\Field;
use Ol3x1n\DataTransferObject\Enums\TypeEnum;

class {{ class }} extends AbstractDTO
{
    // #[Field('name', TypeEnum::STRING)]
    // public string $name;
}
PHP;
    }
}
