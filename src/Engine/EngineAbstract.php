<?php
declare(strict_types=1);

namespace Air\Routing\Engine;

use Air\Routing\Exception\RouteException;

/**
 * Class EngineAbstract
 * @package Air\Routing\Engine
 */
abstract class EngineAbstract
{
    private const VAR_REGEX = <<<REGEX
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;

    private const VAR_REGEX_DEFAULT = '[^/]+';

    /**
     * @param string $route
     * @return array
     * @throws RouteException
     */
    protected function parseRegex(string $route): array
    {
        $routeWithoutClosingOptionals = rtrim($route, ']');
        $numOptionals = strlen($route) - strlen($routeWithoutClosingOptionals);

        //Split on [ while skipping placeholders
        $segments = preg_split('~' . self::VAR_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);

        if ($numOptionals !== count($segments) - 1) {
            //If there are any ] in the middle of the route, throw a more specific error message
            if (preg_match('~' . self::VAR_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
                throw new RouteException('Optional segments can only occur at the end of a route');
            }

            throw new RouteException("Number of opening '[' and closing ']' does not match");
        }

        $routeData = [];
        $currentRoute = '';

        foreach ($segments as $n => $segment) {
            if ($segment === '' && $n !== 0) {
                throw new RouteException('Empty optional part');
            }

            $currentRoute .= $segment;
            $routeData[] = $this->parsePlaceholders($currentRoute);
        }

        return $routeData;
    }

    /**
     * @param string $route
     * @return array|string[]
     */
    private function parsePlaceholders(string $route): array
    {
        if (!preg_match_all('~' . self::VAR_REGEX . '~x', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            return [$route];
        }

        $offset = 0;
        $routeData = [];

        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }

            $routeData[] = [
                $set[1][0],
                isset($set[2]) ? trim($set[2][0]) : self::VAR_REGEX_DEFAULT,
            ];

            $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset !== strlen($route)) {
            $routeData[] = substr($route, $offset);
        }

        return $routeData;
    }
}
