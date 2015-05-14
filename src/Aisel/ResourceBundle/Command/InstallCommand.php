<?php
/*
 * This file is part of the Aisel package.
 *
 * (c) Ivan Proskuryakov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Aisel\ResourceBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

/**
 * InstallCommand
 *
 * @author Ivan Proskoryakov <volgodark@gmail.com>
 */
class InstallCommand extends ContainerAwareCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('aisel:install')
            ->setHelp(<<<EOT
The <info>aisel:install</info> command launch installation process.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>**************************************</info>');
        $output->writeln('<info>********** Installing Aisel **********</info>');
        $output->writeln('<info>**************************************</info>');
        $output->writeln('');

        $this->launchSetup($input, $output);
        $output->writeln('<info>Installation finished.</info>');
    }

    /**
     * launchSetup
     */
    protected function launchSetup(InputInterface $input, OutputInterface $output)
    {
//        $output->writeln('<info>Database settings.</info>');
//        $dialog = $this->getHelperSet()->get('dialog');

//        if ($dialog->askConfirmation($output, '<question>Create database and load fixtures (Y/N)?</question>', false)) {
//            $this->setupDatabase($input, $output);
//        }
//
//        if ($dialog->askConfirmation($output, '<question>Setup files (Y/N)?</question>', false)) {
//            $this->setupFiles($output);
//        }
//        if (self::commandExists('npm')) {


        $commands = [
            'pwd',
            'sh bower.sh',
            'bin/phpunit -c app src/',
        ];

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->run(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer;
                } else {
                    echo 'OUT > '.$buffer;
                }
            });

            if (!$process->isSuccessful()) {
                throw new \RuntimeException($process->getErrorOutput());
            }
            echo $process->getOutput();
        }


        return $this;
    }

    /**
     * setupDatabase
     */
    protected function setupDatabase(InputInterface $input, OutputInterface $output)
    {
        $drop = $this->getApplication()->find('doctrine:database:drop');
        $drop_args = array(
            'command' => 'doctrine:database:drop',
            '--force' => true
        );
        $drop->run(new ArrayInput($drop_args), $output);
        $this->runCommand('doctrine:database:create', $input, $output);
        $this->runCommand('doctrine:schema:create', $input, $output);
        $this->runCommand('doctrine:fixtures:load', $input, $output);
    }

    /**
     * setupFiles
     */
    protected function setupFiles(OutputInterface $output)
    {
        $fs = new Filesystem();
        $frontendDir = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../frontend/web');
        $fs->copy($frontendDir . '/robots.txt.dist', $frontendDir . '/robots.txt');
        $fs->copy($frontendDir . '/images/logo.png.dist', $frontendDir . '/images/logo.png');
        $fs->copy($frontendDir . '/.htaccess.dist', $frontendDir . '/.htaccess');
    }

    protected function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApplication()
            ->find($command)
            ->run($input, $output);

        return $this;
    }


    /**
     * @param $command
     *
     * @return bool
     */
    private static function commandExists($command)
    {
        $installedCommand = "command -v $command";

        return (bool)shell_exec($installedCommand);
    }

}