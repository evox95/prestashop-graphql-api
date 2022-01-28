<?php declare(strict_types=1);

require_once __DIR__ . '/../../config/config.inc.php';
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Error\DebugFlag;
use GraphQL\Server\ServerConfig;
use GraphQL\Validator\Rules;
use GraphQL\Validator\Rules\QueryDepth;
use GraphQL\GraphQL;

use PrestaShop\API\GraphQL\AppContext;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\API\GraphQL\Type\MutationType;
use PrestaShop\API\GraphQL\Type\QueryType;

use PrestaShop\PrestaShop\Adapter\Entity\Context;

// @todo: Allow enabled front-office token
if (Configuration::get('PS_TOKEN_ENABLE')) {
    Configuration::updateValue('PS_TOKEN_ENABLE', false);
}

try {
    // See docs on schema options:
    // https://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
        'query' => new QueryType(),
        'mutation' => new MutationType(),
        'typeLoader' => static fn(string $name): Type => Types::byTypeName($name),
    ]);

    $appContext = new AppContext();
    $appContext->shopContext = Context::getContext();; // simulated "currently logged-in user"
    $appContext->request = $_REQUEST;

    $validationRules = GraphQL::getStandardValidationRules();
    if (!_PS_MODE_DEV_) {
        $validationRules = array_merge(
            $validationRules,
            [
                new Rules\QueryComplexity(100),
                new Rules\DisableIntrospection(),
                new QueryDepth($maxDepth = 10),
            ]
        );
    }

    $config = ServerConfig::create()
        ->setSchema($schema)
        ->setContext($appContext)
        ->setDebugFlag(
            _PS_MODE_DEV_
                ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE)
                : DebugFlag::NONE
        )
        ->setValidationRules($validationRules);

    // See docs on server options:
    // https://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
    $server = new StandardServer($config);

    $server->handleRequest();
} catch (Throwable $error) {
    StandardServer::send500Error($error);
}