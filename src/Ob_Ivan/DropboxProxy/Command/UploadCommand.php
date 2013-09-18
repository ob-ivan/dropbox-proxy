<?php
namespace Ob_Ivan\DropboxProxy\Command;

use Dropbox\WriteMode;
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
            ->setDescription('Uploads a file or a whole directory to Dropbox folder specified by config.')
            ->addArgument(
                'file',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Paths to files to upload, relative to local storage root.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathList = $input->getArgument('file');

        // If pathList is empty, list the whole storage directory.
        if (empty($pathList)) {
            $dir = dir($this->getStoragePath());
            while (false !== ($entry = $dir->read())) {
                // Special files whose name starts with a full stop (.) are ignored.
                if (preg_match('~^\.~', $entry)) {
                    continue;
                }
                $pathList[] = $entry;
            }
            $dir->close();
        }

        $successCount   = 0;
        $skippedCount   = 0;
        $errorCount     = 0;
        $toolbox = $this->getToolbox();
        $root    = $toolbox['dropbox.root'];
        $client  = $toolbox['dropbox.client'];
        foreach ($pathList as $relativePath) {

            // TODO: Check dropbox folder to see if the same file is already
            // present there.

            $localPath = implode(DIRECTORY_SEPARATOR, [
                $this->getStoragePath(),
                $relativePath
            ]);
            if (! is_readable($localPath)) {
                $output->writeln('Cannot read path "' . $localPath . '", skipping.');
                ++$skippedCount;
                continue;
            }
            if (is_dir($localPath)) {
                $output->writeln('Path "' . $localPath . '" is a directory, skipping.');
                ++$skippedCount;
                continue;
            }
            if (! is_file($localPath)) {
                $output->writeln('Path "' . $localPath . '" is not a file, skipping.');
                ++$skippedCount;
                continue;
            }

            // Handle usual files.
            $remotePath = implode('/', [$root, $relativePath]);
            $output->writeln('Uploading ' . $localPath . ' to ' . $remotePath);
            $file = fopen($localPath, 'rb');
            $result = $client->uploadFile($remotePath, WriteMode::add(), $file);
            fclose($file);
            if (is_array($result) && isset($result['path']) && $result['path'] === $remotePath) {
                $output->writeln('Successfully uploaded ' . $localPath . ' to ' . $remotePath);
                ++$successCount;
            } else {
                $output->writeln('Unexpected result while uploading ' . $localPath . ' to ' . $remotePath);
                ++$errorCount;
            }
        }
        // Output overall report.
        $output->writeln('-- ');
        $output->writeln('Total ' . count($pathList) . ' file(s).');
        $output->writeln($successCount . ' file(s) uploaded successfully.');
        $output->writeln($skippedCount . ' file(s) skipped.');
        $output->writeln($errorCount . ' error(s) occured.');
    }

    // protected : UploadCommand //

    protected function getStoragePath()
    {
        return $this->getToolbox()['filesystem.storage'];
    }

    protected function getToolbox()
    {
        if (! $this->toolbox) {
            throw new Exception('Toolbox is not defined. Call setToolbox.');
        }
        return $this->toolbox;
    }
}
