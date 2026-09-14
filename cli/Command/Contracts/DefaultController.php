<?php

namespace Phparch\SpaceTradersCLI\Command\Contracts;

use Minicli\Command\CommandController;
use Phparch\SpaceTraders\ServiceContainer;
use Phparch\SpaceTradersRest\Client;
use Phparch\SpaceTradersCLI\Command\HelpInfo;
use Phparch\SpaceTradersCLI\Render\Contract;
use Phparch\SpaceTradersCLI\Trait\TerminalOutputHelper;

#[HelpInfo(description: "Show My Contracts")]
class DefaultController extends CommandController
{
    use TerminalOutputHelper;

    public function handle(): void
    {
        $client = ServiceContainer::get(Client\Contracts::class);

        $response = $client->MyContracts();

        foreach ($response->contracts as $contract) {
            $renderer = new Contract($contract);
            echo $renderer->output();
        }
    }
}
