<?php
namespace Ob_Ivan\DropboxProxy\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('upload')
            ->setDescription('Uploads a file or a whole directory to Dropbox folder specified by config')
            ->addArgument(
                'file',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Path to a file or a directory to upload'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: Read [config][root] if <file> argument is empty.

        foreach ($input->getArgument('file') as $path) {
            if (! is_readable($path)) {
                $output->writeln('Cannot read path "' . $path . '", skipping.');
                continue;
            }
            if (is_dir($path)) {
                // TODO
            } elseif (is_file($path)) {
                // TODO
            } else {
                $output->writeln('Path "' . $path . '" is neither a file, nor a directory.');
            }
        }
    }
}
