<?php

namespace Jramke\FluidUI\Middleware;

use Jramke\FluidUI\Registry\PortalRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use TYPO3\CMS\Core\Http\NullResponse;

class PortalMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($response instanceof NullResponse) {
            return $response;
        }

        $content = (string) $response->getBody();
        $portalledHtmlStrings = PortalRegistry::getAll();

        if (empty($portalledHtmlStrings)) {
            return $response;
        }

        $concatenatedHtml = implode("\n", array_map('trim', $portalledHtmlStrings));

        // Only add the html before the first body tag as it could be that we have multiple
        $needle = '</body>';
        $replace = $concatenatedHtml . $needle;
        $pos = strpos($content, $needle);
        if ($pos !== false) {
            $content = substr_replace($content, $replace, $pos, strlen($needle));
        }

        $response->getBody()->rewind();
        $response->getBody()->write($content);

        PortalRegistry::clear();

        return $response;
    }
}
