<?php
namespace Ob_Ivan\DropboxProxy\Command;

use Ob_Ivan\ResourceContainer\ResourceContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends Command
{
    private $toolbox;

    public function setToolbox(ResourceContainer $toolbox)
    {
        $this->toolbox = $toolbox;
    }

    // protected : Command //

    protected function configure()
    {
        $this
            ->setName('upload')
            ->setDescription('Uploads a file or a whole directory to Dropbox folder specified by config')
            ->addArgument(
                'file',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Paths to files to upload, relative to local storage root'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathList = $input->getArgument('file');
        // TODO: if pathList is empty, list the whole storage directory.
        $successCount = 0;
        $skippedCount = 0;
        foreach ($pathList as $relativePath) {
            $path = implode(DIRECTORY_SEPARATOR, [
                $this->getToolbox()['filesystem.storage'],
                $relativePath
            ]);
            if (! is_readable($path)) {
                $output->writeln('Cannot read path "' . $path . '", skipping.');
                ++$skippedCount;
                continue;
            }
            if (is_dir($path)) {
                $output->writeln('Path "' . $path . '" is a directory, skipping.');
                ++$skippedCount;
                continue;
            }
            if (! is_file($path)) {
                $output->writeln('Path "' . $path . '" is not a file, skipping.');
                ++$skippedCount;
                continue;
            }
            // TODO: Handle usual files.
        }
        // TODO: Output success and skipped counts.
    }

    // protected : UploadCommand //

    protected function getToolbox()
    {
        if (! $this->toolbox) {
            throw new Exception('Toolbox is not defined. Call setToolbox.');
        }
        return $this->toolbox;
    }
}
