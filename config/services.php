<?php

use Doctrine\DBAL;
use Doctrine\ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use League\Route;
use Phparch\SpaceTraders;
use Phparch\SpaceTraders\Data;
use Phparch\SpaceTraders\Entity;
use Phparch\SpaceTraders\Middleware;
use Phparch\SpaceTraders\Repository;
use Phparch\SpaceTraders\Routes;
use Phparch\SpaceTraders\ServiceContainer;
use Phparch\SpaceTraders\TwigExtensions;
use Phparch\SpaceTradersRest\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

function getSpaceTradersToken(): string {
    $token = ServiceContainer::getEnv('spacetraders_token');
    if ($token) {
        assert(is_string($token));
        return $token;
    }
    return '';
}

return [
    Client\Agents::class => static function(): Client\Agents {
        return new Client\Agents(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Client\Contracts::class => static function(): Client\Contracts {
        return new Client\Contracts(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Client\Fleet::class => static function(): Client\Fleet {
        return new Client\Fleet(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Client\ShipActions::class => static function(): Client\ShipActions {
        return new Client\ShipActions(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Client\ShipTravel::class => static function(): Client\ShipTravel {
        return new Client\ShipTravel(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Client\Systems::class => static function() {
        return new Client\Systems(
            getSpaceTradersToken(),
            ServiceContainer::get(\GuzzleHttp\Client::class),
            ServiceContainer::get(EventDispatcherInterface::class),
        );
    },
    Predis\Client::class => static function () {
        return new Predis\Client($_ENV['REDIS_URI']);
    },
    GuzzleHttp\Client::class => static function () {
        if (
            isset($_ENV['REDIS_CACHE_TTL'])
            && ctype_digit($_ENV['REDIS_CACHE_TTL'])
        ) {
            $ttl = (int) $_ENV['REDIS_CACHE_TTL'];
        } else {
            $ttl = 900;
        }

        $adapter = new RedisAdapter(
            redis: ServiceContainer::get(Predis\Client::class),
            namespace: '',
            defaultLifetime: $ttl,
        );

        $strategy = new GreedyCacheStrategy(
            new Psr6CacheStorage($adapter),
            $_ENV['GUZZLE_REQUEST_CACHE_TTL'] ?? 900,
        );
        $stack = GuzzleHttp\HandlerStack::create();
        $stack->push(new CacheMiddleware($strategy), 'cache');
        return new GuzzleHttp\Client(['handler' => $stack]);
    },
];