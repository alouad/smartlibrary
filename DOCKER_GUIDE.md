# Docker Setup Guide for SmartLibrary Frontend

This guide explains how to use Docker for the SmartLibrary Frontend project.

## Prerequisites

- Docker installed ([Download Docker](https://www.docker.com/products/docker-desktop))
- Docker Compose installed (comes with Docker Desktop)

## Project Structure

- **Dockerfile** - Production-ready multi-stage build
- **Dockerfile.dev** - Development environment with hot-reload
- **docker-compose.yml** - Production Docker Compose configuration
- **docker-compose.dev.yml** - Development Docker Compose configuration
- **.dockerignore** - Files excluded from Docker build

## Quick Start

### Development Environment (with hot-reload)

```bash
# Build and start the development container
docker-compose -f docker-compose.dev.yml up --build

# The app will be available at http://localhost:3000
# Code changes will automatically reload (hot-reload enabled)

# Stop the container
docker-compose -f docker-compose.dev.yml down
```

### Production Environment

```bash
# Build and start the production container
docker-compose up --build

# The app will be available at http://localhost:3000

# Stop the container
docker-compose down
```

## Building Docker Images

### Development Image

```bash
docker build -f Dockerfile.dev -t smartlibrary-frontend:dev .
docker run -p 3000:3000 smartlibrary-frontend:dev
```

### Production Image

```bash
docker build -f Dockerfile -t smartlibrary-frontend:latest .
docker run -p 3000:3000 smartlibrary-frontend:latest
```

## Pushing to Docker Hub

### Step 1: Login to Docker Hub

```bash
docker login
```

### Step 2: Tag your image

```bash
docker tag smartlibrary-frontend:latest YOUR_DOCKER_HUB_USERNAME/smartlibrary-frontend:latest
docker tag smartlibrary-frontend:latest YOUR_DOCKER_HUB_USERNAME/smartlibrary-frontend:v1.0.0
```

### Step 3: Push to Docker Hub

```bash
docker push YOUR_DOCKER_HUB_USERNAME/smartlibrary-frontend:latest
docker push YOUR_DOCKER_HUB_USERNAME/smartlibrary-frontend:v1.0.0
```

## Common Commands

### View running containers

```bash
docker ps
```

### View logs

```bash
# Follow logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f smartlibrary-frontend
```

### Enter container shell

```bash
docker-compose exec smartlibrary-frontend sh
```

### Remove containers and images

```bash
# Remove containers and volumes
docker-compose down -v

# Remove image
docker rmi smartlibrary-frontend:latest
```

## Environment Variables

Create a `.env` file in the root directory for environment-specific settings:

```env
NODE_ENV=production
# Add other variables as needed
```

## Troubleshooting

### Port 3000 already in use

```bash
# Change the port in docker-compose.yml
# From: "3000:3000"
# To:   "3001:3000"
```

### Container exits immediately

```bash
# Check logs
docker-compose logs
```

### Hot-reload not working in development

- Ensure you're using `docker-compose.dev.yml`
- Check that `CHOKIDAR_USEPOLLING=true` is set in the environment
- Restart the container: `docker-compose -f docker-compose.dev.yml restart`

## Production Deployment Tips

1. **Multi-stage builds**: The production Dockerfile uses multi-stage builds to minimize image size
2. **Alpine Linux**: Uses lightweight Alpine Linux base image
3. **serve package**: Uses `serve` to efficiently serve the static build
4. **Health checks**: Includes health checks for container orchestration
5. **Security**: Run containers as non-root when possible

## Optional: Nginx Reverse Proxy

For production, you can use Nginx as a reverse proxy. Uncomment the `nginx` service in `docker-compose.yml` and create `nginx.conf`:

```nginx
http {
    server {
        listen 80;
        server_name _;

        location / {
            proxy_pass http://smartlibrary-frontend:3000;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection 'upgrade';
            proxy_set_header Host $host;
            proxy_cache_bypass $http_upgrade;
        }
    }
}

events {
    worker_connections 1024;
}
```

## Next Steps

- Set up CI/CD pipelines (GitHub Actions, GitLab CI, etc.) to automate Docker builds
- Configure container orchestration (Kubernetes, Docker Swarm) for production
- Implement environment-specific configurations (.env files)
- Add security scanning to your Docker images
