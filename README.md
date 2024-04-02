## How to run

You can run `./local_reacreate_docker.sh` script to build and run the project.
If you want console to close automatically afterbuilding process, remove or comment `read` statement in this script.

## Project structure

- `docker-compose*.yaml` - docker-compose files with containers configuration
- `./local_reacreate_docker.sh` - shell script for rebuild
- `.env` - environment variables
- `api/` - backend root directory
- `frontend/` - frontend root directory
- `docker/` - Dockerfiles, certs, configs etc