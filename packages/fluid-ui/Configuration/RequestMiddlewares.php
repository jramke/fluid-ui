<?php


return [
    'frontend' => [
        'fluid-ui/portal' => [
            'target' => \Jramke\FluidUI\Middleware\PortalMiddleware::class,
            // https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/RequestLifeCycle/Middlewares.html#request-handling-enriching-response
            'after' => [
                'typo3/cms-frontend/content-length-headers'
            ],
        ],
    ]
];
