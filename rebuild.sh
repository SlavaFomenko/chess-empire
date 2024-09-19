docker stop $(docker ps -aq --filter "name=chess-empire*")
docker rm $(docker ps -aq --filter "name=chess-empire*")

docker-compose -f docker-compose.yaml -f docker-compose.local.yaml up --build -d

read