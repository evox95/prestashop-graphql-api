<?php
/*
 * This file is part of the evox95/prestashop-graphql-api package.
 *
 * (c) Mateusz Bartocha <contact@bestcoding.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Max-Age: 3600');

require_once __DIR__ . '/../../config/config.inc.php';
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Validator\Rules;
use GraphQL\Validator\Rules\QueryDepth;
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Type\MutationType;
use PrestaShop\API\GraphQL\Type\QueryType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Context;

// @todo: Allow enabled front-office token
if (Configuration::get('PS_TOKEN_ENABLE')) {
    Configuration::updateValue('PS_TOKEN_ENABLE', false);
}

//try {
// See docs on schema options:
// https://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
$schema = new Schema([
    'query' => Types::get(QueryType::class)(),
    'mutation' => new MutationType(),
    'typeLoader' => static fn (string $name): Type => Types::byTypeName($name),
]);

$apiContext = new ApiContext();
$apiContext->shopContext = Context::getContext();
$apiContext->request = $_REQUEST;

$validationRules = GraphQL::getStandardValidationRules();
if (!_PS_MODE_DEV_) {
    $validationRules = array_merge(
        $validationRules,
        [
            new Rules\QueryComplexity(100),
            new Rules\DisableIntrospection(),
            new QueryDepth(10),
        ]
    );
}

$config = ServerConfig::create()
    ->setSchema($schema)
    ->setContext($apiContext)
    ->setDebugFlag(
        _PS_MODE_DEV_
            ? (DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::RETHROW_UNSAFE_EXCEPTIONS | DebugFlag::INCLUDE_TRACE)
            : DebugFlag::NONE
    )
    ->setValidationRules($validationRules);

// See docs on server options:
// https://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
$server = new StandardServer($config);

$server->handleRequest();

//} catch (Throwable $error) {
//    StandardServer::send500Error($error);
//}