<?php

namespace BeyondCode\SelfDiagnosis\Checks;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class DirectoriesHaveCorrectPermissions implements Check
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Collection */
    private $paths;

    /**
     * DirectoriesHaveCorrectPermissions constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * The name of the check.
     *
     * @return string
     */
    public function name(array $config): string
    {
        return 'The directories have the correct permissions.';
    }

    /**
     * The error message to display in case the check does not pass.
     *
     * @return string
     */
    public function message(array $config): string
    {
        return 'The following directories are not writable: '.PHP_EOL.$this->paths->implode(PHP_EOL);
    }

    /**
     * Perform the actual verification of this check.
     *
     * @return bool
     */
    public function check(array $config): bool
    {
        $this->paths = Collection::make(array_get($config, 'directories', []));

        $this->paths = $this->paths->reject(function ($path) {
            return $this->filesystem->isWritable($path);
        });

        return $this->paths->isEmpty();
    }
}
