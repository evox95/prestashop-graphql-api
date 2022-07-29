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

if (isset($_SERVER['HTTP_HOST'])) {
    if ($_SERVER['HTTP_HOST'] == 'panel.arif.pl'){
        header('Access-Control-Allow-Origin: https://app.arif.pl');
    } else {
        header('Access-Control-Allow-Origin: http://localhost:3000');
    }

    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, content-type, Accept, Cookie');
    header('Access-Control-Max-Age: 3600');
}

// chrome and some other browser sends a preflight check with OPTIONS
// if that is found, then we need to send response that it's okay
// @link https://stackoverflow.com/a/17125550/2754557
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // need preflight here
//    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Cookie');
    // add cache control for preflight cache
    // @link https://httptoolkit.tech/blog/cache-your-cors/
    header('Access-Control-Max-Age: 86400');
    header('Cache-Control: public, max-age=86400');
//    header('Vary: origin');
    exit;
}

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
use PrestaShop\API\GraphQL\ApiContext;
use PrestaShop\API\GraphQL\Type\MutationType;
use PrestaShop\API\GraphQL\Type\QueryType;
use PrestaShop\API\GraphQL\Types;
use PrestaShop\PrestaShop\Adapter\Entity\Context;

// @todo: Allow enabled front-office token
if (Configuration::get('PS_TOKEN_ENABLE')) {
    Configuration::updateValue('PS_TOKEN_ENABLE', false);
}

//$fc = new FrontController();
//$fc->init();

try {
// See docs on schema options:
// https://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
        'query' => Types::get(QueryType::class)(),
        'mutation' => Types::get(MutationType::class)(),
        'typeLoader' => static fn(string $name): Type => Types::byTypeName($name),
    ]);

    $apiContext = new ApiContext();
    $apiContext->shopContext = Context::getContext();
    $apiContext->request = $_REQUEST;

    $input = (array)(((array)@json_decode(file_get_contents('php://input')))['variables'] ?? []);
    $_GET = [];
    foreach ($input as $key => $value) {
        if (strpos($key, 'req_') !== 0) {
            continue;
        }
        $_GET[substr($key, 4)] = $value;
    }
    if (isset($input['req_lang_id']) && (int)$input['req_lang_id'] >= 0) {
        $apiContext->shopContext->language = new Language((int)$input['req_lang_id']);
        $apiContext->shopContext->cookie->id_lang = (int)$input['req_lang_id'];
    } elseif (isset($input['req_lang_iso']) && Validate::isLangIsoCode($input['req_lang_iso'])) {
        $apiContext->shopContext->language = new Language(Language::getIdByIso($input['req_lang_iso']));
        $apiContext->shopContext->cookie->id_lang = Language::getIdByIso($input['req_lang_iso']);
    }
//    var_dump($_GET);
//    die();

//AuthService::authMiddleware($apiContext);

    $validationRules = GraphQL::getStandardValidationRules();
    if (!_PS_MODE_DEV_) {
        $validationRules = array_merge(
            $validationRules,
            [
                new Rules\QueryComplexity(100),
                new Rules\DisableIntrospection(),
                new Rules\QueryDepth(10),
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

} catch (Throwable $error) {
    $msg = sprintf(
        '%s at line %d in file %s',
        Tools::safeOutput($error->getMessage(), true),
        $error->getLine(),
        ltrim(str_replace([_PS_ROOT_DIR_, '\\'], ['', '/'], $error->getFile()), '/')
    );
    $logger = new FileLogger();
    $logger->setFilename(_PS_ROOT_DIR_ . '/var/logs/' . date('Ymd') . '_api_graphql_exception.log');
    $logger->logError($msg);
    $logger->logError($error->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'errors' => [
            $error->getMessage()
        ],
    ]);
//    StandardServer::send500Error($error);
}
