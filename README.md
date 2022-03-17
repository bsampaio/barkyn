# Barkyn Coding Challenge

The project goal is to know me and how do I code a bit better.
The challenge description can be found [here](https://gist.github.com/barkyndev/3048763d21f80a3b6355f10ee7510b6a)

## About me
My name is Breno Grillo. I'm based in Brazil and I mostly develop projects using Laravel and Lumen.
In this challenge, Laravel is not allowed so I've used [Lumen](https://lumen.laravel.com/docs/9.x)

## About the code
The code was made considering SOLID principles and PSR standards. A Service layer was used to organize the code better.

Unit testing was also developed and can be seen in ```tests/``` directory. It can runs with PHPUnit.

I also made a request library with [Insomnia](https://insomnia.rest/download) which is present in the root folder named as ```Imsonia.json```

## Setup

First you'll need to copy the env file from the example ```cp .env.example .env```. The file contains database configurations to run the application with docker.

The project comes a Dockerfile and can run with ```docker-compose```

After the build of the images, access the container using: 
```docker exec -it challenge-api-1 sh```

After that you need to run:
* Migrations
  * ```php artisan migrate```
* Seeds (_optional_)
  * ```php artisan db:seed --class=CreateCustomersSeeder```


Now the project is up and running and can be found at ```127.0.0.1:8080```.

Contact me at: [brenogrillo@gmail.com](mailto:brenogrillo@gmail.com)
