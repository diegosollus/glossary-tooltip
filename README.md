DEPENDENCIES
-------------------
To create the development environment you need to install docker-compose:

    sudo apt install docker-compose

PROJECT
-------------------

Afterwards, it is necessary to run this command from the root of the project to upload the environment:

    docker-compose up -d

VENDOR
-------------------

    composer install


Services exposed outside the environment:
-------------------

Service|Address outside containers
-------|--------------------------
Webserver|[localhost:8080](http://localhost:8080)
PostgreSQL|**host:** `localhost`<br> **port:** `8004`<br>**user:** `drupal`<br>**password:** `drupal`
