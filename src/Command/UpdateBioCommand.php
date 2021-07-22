<?php declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateBioCommand extends Command
{
    private const GITHUB_BIO = 'Hey I\'m Jonah and I\'m a %d year old PHP developer from near Lüneburg (Germany).';

    private HttpClientInterface $githubClient;

    public function __construct(
        HttpClientInterface $githubClient
    ) {

        parent::__construct();
        $this->githubClient = $githubClient;
    }

    protected function configure()
    {
        $this
            ->setName('github:update:bio')
            ->setDescription('Update my age in the GitHub Bio.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $age = (new \DateTime('1998-07-23'))->diff(new \DateTime('today'))->y;

        $response = $this->githubClient->request(
            'PATCH',
            '/user',
            [
                'json' => [
                    'bio' => sprintf(
                        self::GITHUB_BIO,
                        $age
                    ),
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            $output->writeln('Your GitHub bio can not be updated.');

            return Command::FAILURE;
        }

        $output->writeln('Your GitHub bio has been updated.');

        return Command::SUCCESS;
    }
}
