<?php

namespace Phparch\SpaceTradersCLI\Command\Fleet;

use InvalidArgumentException;
use Minicli\Command\CommandController;
use Phparch\SpaceTraders\ServiceContainer;
use Phparch\SpaceTradersRest\Client;
use Phparch\SpaceTradersCLI\Command\HelpInfo;
use Phparch\SpaceTradersCLI\Trait\TerminalOutputHelper;

#[HelpInfo(description: "Get mount details for a ship", params: ['ship symbol'])]
class GetShipMountsController extends CommandController
{
    use TerminalOutputHelper;

    public function handle(): void
    {
        $client = ServiceContainer::get(Client\Fleet::class);

        $args = $this->getArgs();
        $ship = $args[3] ?? null;
        if (!$ship) {
            throw new InvalidArgumentException("Please specify ship symbol as third parameter");
        }
        $response = $client->getShipMounts($ship);
        $this->outputVar($response);
    }
}
