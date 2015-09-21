<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika;

use Symfony\Component\Finder\Finder;

/**
 * Compiles Paprika into a single Phar file.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class Compiler
{
    /**
     * Compilation process
     *
     * @param string $pharFile The phar file to create
     */
    public function compile($pharFile = 'paprika.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'paprika.phar');

        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = new Finder();

        $finder->files()
            ->ignoreVCS(true)
            ->name('*')
            ->notPath('dist')
            ->notName('paprika')
            ->notName('compile-paprika')
            ->in(__DIR__ . '/../..');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $content = file_get_contents(__DIR__ . '/../../bin/paprika');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/paprika', $content);

        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        unset($phar);
    }

    /**
     * Add file to the Phar.
     *
     * @param \Phar $phar Phar file
     * @param \SplFileInfo $file File to add
     */
    protected function addFile($phar, $file)
    {
        $path    = str_replace(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR, '', $file->getRealPath());
        $content = file_get_contents($file);

        $phar->addFromString($path, $content);
    }

    /**
     * Create a stub for the Phar archive.
     *
     * @return string Stub
     */
    protected function getStub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

Phar::mapPhar('paprika.phar');
require 'phar://paprika.phar/bin/paprika';

__HALT_COMPILER();
EOF;
        return $stub;
    }
}