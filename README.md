## Documentation

For version `dev-master` See the below for documentation.

-----------------------------------

### Installation
To install this package you will need:

- Laravel 4 or 5
- PHP 5.4 +

Install package via composer require

    composer require kolayik/auth:dev-master
or edit your composer.json

    "require": {

        "kolayik/auth": "dev-master"
    }
Then run composer update in your terminal to pull it in.

Once this has finished, you will need to add the service provider to the providers array in your app.php config as follows:

    KolayIK\Auth\Providers\LaravelServiceProvider::class
    
Finally, you will want to publish the config and migration file using the following command:

**Laravel 5:**

    $ php artisan vendor:publish --provider="KolayIK\Auth\Providers\LaravelServiceProvider"

### Configuration

Open `.env` file and change according to your request.

Token time to live - KOLAY_AUTH_TTL

Storage - KOLAY_AUTH_DRIVER

    KOLAY_AUTH_DRIVER:"database" or "cache"

    KOLAY_AUTH_TTL:1440


## License

The MIT License (MIT)
