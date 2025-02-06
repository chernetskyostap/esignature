

# Run following commands to setup the project:

cp .env.example .env
docker-compose up -d

docker exec -it esignature bash
- compose install
- php artisan migrate
- php artisan key:generate
- php artisan l5-swagger:generate

# NOW YOUR API DOCUMENTATION IS ACCESSIBLE AT URL:
- http://localhost:8000/api/documentation
