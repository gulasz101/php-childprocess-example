Example of usage symfony/process and react/child-process.

To see it in action:
- first:
```
docker --rm -it -v $PWD:/app composer install
```
- symfony/process:
```
docker run --rm -it -v $PWD:/app -w /app/src php:7.4 php symfony-process.php
```
- react/child-process
```
docker run --rm -it -v $PWD:/app -w /app/src php:7.4 php symfony-process.php
```
Both examples are doing as following:

1) creating five parallel processes by executing command (which internally after initial output to stdout sleeps 5 sec, and writes to stderr as well as randomly throwing error)
2) keeping track of number of running processes as well as keeping track of 30 seconds timeout
3) writing everything from child processes do stderr / stdout


