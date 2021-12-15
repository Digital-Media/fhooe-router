# fhooe-router

*fhooe-router* is a simple object-oriented router developed for PHP classes in the [Media Technology and Design](https://www.fh-ooe.at/en/hagenberg-campus/studiengaenge/bachelor/media-technology-and-design/) program at the [University of Applied Sciences Upper Austria](https://www.fh-ooe.at/en/hagenberg-campus/). It is primarily designed for educational purposes (learning the concept of routing and object-oriented principles). Its functionality is limited by design (e.g., only GET and POST protocols are supported). Use it for "public" applications at your own risk.

## Installation

The recommended way to use *fhooe-router* in your project is through Composer:

    composer require fhooe/router

Alternatively, you can use the [fhooe-router-skeleton](https://github.com/Digital-Media/fhooe-router-skeleton) project that gives you a fully working example built upon *fhooe-router* (including some simple views):

    composer create-project fhooe/router-skeleton path/to/install

Composer will create a project in the `path/to/install` directory.

## Basic Usage

*fhooe-router* can be used in two ways:

### Using a `Router` Object

1. Instantiate the `Router` class.

   ```php
   $router = new Router();
   ```

2. Define routes using the `get()` and `post()` methods. Supply a URI pattern to match against and a callback that is executed when pattern an protocol both match.

   ```php
   $router->get("/", function() {
       // e.g., load a view
   });
   ```

3. Set a 404 callback to load a view or trigger behavior when no route matches.

   ```php
   $router->set404Callback(function() {
       // e.g., load a 404 view
   });
   ```

4. Optional: define a base path if your application is not located in your server's document root. 

   ```php
   $router->setBasePath("/path/to/your/files");
   ```

5. Run the router. This will fetch the current URI, match it against the defined routes and execute them if a match is found.

   ```php
   $router->run();
   ```

### Using the Static Routing Method `Router::getRoute()`

1. Invoke the static method. Provide a base path as argument if your project is not located in your server's document root. The method returns the route as a string in the form of `PROTOCOL /pattern` , e.g. `GET /` when a GET request was made to the root directory.

   ```php
   $route = Router::getRoute("/path/to/your/files");
   ```

2. Use a conditional expression to decide what to do with the matched route.

   ```php
   switch($route) {
       case "GET /":
           // e.g., load a view
           break;
       default:
           // e.g., load the 404 view
           break;
   }
   ```

## Contributing

If you'd like to contribute, please refer to [CONTRIBUTING](https://github.com/Digital-Media/fhooe-router/blob/main/CONTRIBUTING.md) for details.

## License

*fhooe-router* is licensed under the MIT license. See [LICENSE](https://github.com/Digital-Media/fhooe-router/blob/main/LICENSE) for more information.

