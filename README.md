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

### Quick Start
How do generate a custom token ?

    use KolayIK\Auth\Facades\KolayAuth;
    
    class AuthenticateController extends Controller
    {
        public function authenticate(Request $request)
        {
            // your add custom login code
            $userId = "kolayik";
            
            return response()->json(KolayAuth::generate($userId));
        }
    }
    
How do authenticate via token in custom middleware ?
        
        namespace App\Http\Middleware;
        use KolayIK\Auth\Facades\KolayAuth;
        
        class CustomAuth
        {
            public function handle($request, Closure $next)
            {   
                $token = KolayAuth::authenticate();
    
                if ($token->isExpired()) {
                    throw new \Exception('Session expired!');
                }
    
                return $next($request);
            }
        }
        
### Authentication
To make authenticated requests via http using the built in methods, you will need to set an authorization header as follows:
    
    Authorization: Bearer {yourtoken}
    
**Note to Apache users**

Apache seems to discard the Authorization header if it is not a base64 encoded user/pass combo. So to fix this you can add the following to your apache config

    RewriteEngine On
    RewriteCond %{HTTP:Authorization} ^(.*)
    RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
    
Alternatively you can include the token `via a query string`

    http://api.mysite.com/me?authorization_key={yourtoken}
    
To get the token from the request you can do:

    $token = KolayAuth::getToken();

**Middleware**

You can use `kolay.auth` middleware:
    
    Route::group(['prefix' => '/api/v1', 'middleware' => 'kolay.auth'], function () {
        //your code
    });
## License

The MIT License (MIT)
