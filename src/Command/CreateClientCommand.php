<?php

namespace Cerberus\Command;

use Cerberus\OAuth\Service\Client\ClientService;
use Cerberus\OAuth\Service\Client\CreateClientRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClientCommand extends Command
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('oauth:client:create')
            ->setDescription("Create a new OAuth client")
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the client'
            )
            ->addArgument(
                'redirect-uri',
                InputArgument::IS_ARRAY,
                "One or more redirect URI's"
            )
            ->addOption(
                'grant-types',
                'g',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'The grant type(s) the client is allowed to use'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new CreateClientRequest(
            $input->getArgument('name'),
            $input->getArgument('redirect-uri'),
            $input->getOption('grant-types')
        );

        $result = $this->clientService->create($request);

        $output->writeln("Client was created:");
        $output->writeln("Name: {$result->getClient()->getName()}");
        $output->writeln("Id: {$result->getClient()->getIdentifier()}");
        $output->writeln("Secret: {$result->getClientSecret()}");
    }
}
