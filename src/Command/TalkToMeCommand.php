<?php

namespace App\Command;

use App\Service\MixRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:talk-to-me',
    description: 'A self aware command that can only do one thing.',
)]
class TalkToMeCommand extends Command
{

    public function __construct(private MixRepository $mixRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Your name')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'Make name uppercase')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name') ?? 'whoever you are!';
        $message = null;

        if ($name) {
            $message = sprintf('Hey %s!', $name);
        }

        if ($input->getOption('yell')) {
            $message = strtoupper($message);
        }

        $io->text($message);

        if ($io->confirm('Do you want a mix recommendation?')) {
            $all_mixes = $this->mixRepository->findAll();
            $mix = $all_mixes[array_rand($all_mixes)];
            $io->note(sprintf('I recommend %s', $mix['title']));
        }

        return Command::SUCCESS;
    }
}
