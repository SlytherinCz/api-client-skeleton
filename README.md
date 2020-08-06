# API Client Skeleton

## Getting started

1) Add the repository
    ```json
    "repositories": [
        {
          "type": "vcs",
          "url": "git@github.com:SlytherinCz/api-client-skeleton.git"
        },
    ], 
    ...
    ```
    You might need to set 
    ```json
    "minimum-stability": "dev",
    "prefer-stable": true,
    ...  
    ```
2) Install the package 
    ```bash
    composer require slytherincz/api-client 
    ```
   
   You might want to run Find and Replace on the src dir to use 
   your own namespace. If you do so, don't forget to edit
   composer.json autoload entry. Don't rename the ApiClientContracts unless you
   fork that repo.
   
3) Use PSR17 compliant http client
    ```php
       $httpClient = new \GuzzleHttp\Client();
    ```
4) Instantiate the client
    ```php
    $dochubApiClient = new \SlytherinCz\ApiClientSkeleton\Client(
        $httpClient,
        'localhost:3190',
        [
            'listFields' => [
                'items' => 'docs',
                'totalItems' => 'totalDocs',
                'currentPage' => 'page',
                'previousPage' => 'prevPage'
            ]
        ]
    );
    ```
   
