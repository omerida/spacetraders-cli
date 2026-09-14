<?php

namespace Phparch\SpaceTradersCLI\Command\Fleet;

use Minicli\Command\CommandController;
use Phparch\SpaceTraders\ServiceContainer;
use Phparch\SpaceTradersRest\Client;
use Phparch\SpaceTradersCLI\Command\HelpInfo;
use Phparch\SpaceTradersCLI\Render;
use Phparch\SpaceTradersCLI\Trait\TerminalOutputHelper;

#[HelpInfo(description: "List all ships")]
class DefaultController extends CommandController
{
    use TerminalOutputHelper;

    public function handle(): void
    {
        $client = ServiceContainer::get(Client\Fleet::class);

        $response = $client->listShips();

        if (!$response->ships) {
            echo "No ships found.";
            return;
        }

        foreach ($response->ships as $ship) {
            $render = new Render\Ship($ship);
            echo $render->output();
        }
    }
}
