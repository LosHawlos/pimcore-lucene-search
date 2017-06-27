<?php

namespace LuceneSearchBundle\Command;

use LuceneSearchBundle\Logger\ConsoleLogger;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lucenesearch')
            ->setDescription('LuceneSearch Website Crawler')
            ->addArgument(
                'crawl',
                InputArgument::OPTIONAL,
                'Crawl Website Pages with LuceneSearch.'
            )->addOption('force', 'f',
                InputOption::VALUE_NONE,
                'Force Crawl Start');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('crawl') === 'crawl') {

            /** @var \LuceneSearchBundle\Task\TaskManager $taskManager */
            $taskManager = $this->getContainer()->get('lucene_search.task_manager');

            $consoleLogger = new ConsoleLogger();
            $consoleLogger->setConsoleOutput($output);
            $taskManager->setLogger($consoleLogger);
            $taskManager->processTaskChain(['force' => $input->getOption('force')]);

            $this->output->writeln('<fg=green>LuceneSearch: Finished crawl.</>');
        }

    }

}