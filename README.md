# <img src="https://raw.githubusercontent.com/Digital-Media/fhooe-router-skeleton/076902786d9e13145b315154b0ea30b6222e3055/views/images/fhooe-router-logo.svg" height="32" alt="The fhooe/router Logo: Three containers arrows going in different directions: left, up, and right."> fhooe/router

*fhooe/router* is a simple object-oriented router developed for PHP classes in the [Media Technology and Design](https://fh-ooe.at/en/degree-programs/media-technology-and-design-bachelor) program at the [University of Applied Sciences Upper Austria](https://fh-ooe.at/en/campus-hagenberg). It is primarily designed for educational purposes (learning the concept of routing and object-oriented principles). Its functionality is limited by design (e.g., only GET and POST methods are supported). Use it for "public" applications at your own risk.

## Installation

The recommended way to use *fhooe-router* in your project is through Composer:

```bash
composer require fhooe/router
```

Alternatively, you can use the [fhooe/router-skeleton](https://github.com/Digital-Media/fhooe-router-skeleton) project that gives you a fully working example built upon *fhooe/router* (including some simple views):

```bash
composer create-project fhooe/router-skeleton path/to/install
```

Composer will create a project in the `path/to/install` directory.

## Basic Usage

*fhooe/router* can be used in two ways:

### Using a `Router` Object (recommended)

1. Instantiate the `Router` class.

   ```php
   $router = new Router();
   ```
   **Adding a logger:** Pass an instance of a PSR-3 compatible logger application, e.g., [Monolog](https://packagist.org/packages/monolog/monolog), to receive predefined log messages about routes being added and executed. This can be useful to track what the Router is doing.

   ```php
   $logger = new Logger("skeleton-logger");
   // add processors, formatters or handlers to the logger
   $router = new Router($logger);
   ```

2. Define routes using the `get()` and `post()` methods. Supply a URI pattern to match against and a callback that is executed when the pattern and HTTP method match.

   ```php
   $router->get("/", function () {
       // e.g., load a view
   });
   ```
   **Placeholders:** You can define route placeholders using curly brackets. The name of the placeholder will be available as a parameter in the callback, the actual value in the URI will be its argument.

   ```php
   $router->get("/product/{id}", function ($id) {
      // e.g., load a view to display the product
   });

   **Optional parts:** You can make parts of the route optional by putting it in square brackets. That way a route will match in both ways. This can be, for example, used to make a route work with or without a trailing slash.

   ```php
   $router->get("/form[/]", function () {
      // e.g., load a view
   });
   ```

3. Set a 404 callback to load a view or trigger behavior when no route matches.

   ```php
   $router->set404Callback(function () {
       // e.g., load a 404 view
   });
   ```

4. Optional: define a base path if your application is not located in your server's document root. 

   ```php
   $router->setBasePath("/path/to/your/files");
   ```

5. Run the router. This will fetch the current URI, match it against the defined routes, and execute them if a match is found.

   ```php
   $router->run();
   ```

### Using the Static Routing Method `Router::getRoute()`

1. Invoke the static method. Provide a base path as an argument if your project is not located in your server's document root. The method returns the route as a string in the form of `METHOD /pattern` , e.g., `GET /`, when a GET request was made to the root directory.

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

The static method is intended primarily for quickly getting the route string. It offers less functionality, e.g., no reverse route resolution.

## Contributing

If you'd like to contribute, please refer to [CONTRIBUTING](https://github.com/Digital-Media/fhooe-router/blob/main/CONTRIBUTING.md) for details.

## License

*fhooe/router* is licensed under the MIT license. See [LICENSE](https://github.com/Digital-Media/fhooe-router/blob/main/LICENSE) for more information.
