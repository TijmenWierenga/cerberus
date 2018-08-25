# Cerberus OAuth Server
A full authentication API framework.

## Get started
If you're a Mac user, you most likely got [Make](https://www.gnu.org/software/make/) installed. If you have, simply run the following command:
```bash
make init
```

The development server will be on a different location depending on your Docker client:
* Docker For Mac - http://localhost:8080
* docker-machine - 192.168.99.100:8080 (Your IP-address could be different. Find the docker-machine ip with `docker-machine ip {MACHINE_NAME}`)

To stop the development server run:
```bash
make stop
```

## Tests
Run all the tests:
```bash
make test
```

Run unit-tests:
```bash
make unit-test
```

Run functional-tests:
```bash
make functional-test
```

Run integration-tests:
```bash
make integration-test
```