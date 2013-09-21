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
        $toolbox    = $this->getToolbox();
        $remoteRoot = $toolbox['dropbox.root'];
        $client     = $toolbox['dropbox.client'];
        $checksumCacheCollection = $toolbox['cache']->collection('checksum');
        foreach ($pathList as $relativePath) {

            // Check whether local file is accessible.
            $localPath = implode(DIRECTORY_SEPARATOR, [
                $this->getStoragePath(),
                $relativePath
            ]);
            if (! file_exists($localPath)) {
                $output->writeln('Path "' . $localPath . '" does not exist, skipping.');
                ++$skippedCount;
                continue;
            }
            if (! is_readable($localPath)) {
                $output->writeln('Cannot read path "' . $localPath . '", skipping.');
                ++$skippedCount;
                continue;
            }
            if (is_dir($localPath)) {
                // TODO: Implement directory recursion, when -R option is specified.
                $output->writeln('Path "' . $localPath . '" is a directory, skipping.');
                ++$skippedCount;
                continue;
            }
            if (! is_file($localPath)) {
                // Then what is it? A link? Won't work.
                $output->writeln('Path "' . $localPath . '" is not a file, skipping.');
                ++$skippedCount;
                continue;
            }

            // Check local file hash to see if it has changed since the previous upload.
            $checksumCacheElement = $checksumCacheCollection->element($relativePath);
            $actualChecksum = md5_file($localPath);
            $storedChecksum = $checksumCacheElement->get();
            if ($storedChecksum === $actualChecksum) {
                $output->writeln('Path "' . $localPath . '" has no changes, skipping.');
                ++$skippedCount;
                continue;
            }

            // Force upload disregarding any server-side content.
            $remotePath = implode('/', [$remoteRoot, $relativePath]);
            $output->writeln('Uploading ' . $localPath . ' to ' . $remotePath);
            $file = fopen($localPath, 'rb');
            $response = $client->uploadFile($remotePath, WriteMode::force(), $file);
            fclose($file);

            // Handle response.
            if (is_array($response) && isset($response['path']) && $response['path'] === $remotePath) {
                $output->writeln('Successfully uploaded ' . $localPath . ' to ' . $remotePath);
                $checksumCacheElement->set($actualChecksum);
                ++$successCount;
            } else {
                $output->writeln('Unexpected response while uploading ' . $localPath . ' to ' . $remotePath);
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
