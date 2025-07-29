# Meeseeks Symfony App

This is a Symfony-based backend application running inside a Docker environment.

## 🚀 Getting Started

### Clone the repository

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

### Start Docker containers

```bash
docker compose up -d
```

### Install PHP dependencies

After containers are up, run:

```bash
docker compose exec php composer install
```

### Open in browser

Visit: [http://localhost](http://localhost)

If you see the Symfony welcome page, you're good to go!

---

## 🧾 Project Structure

```
.
├── docker/                 # Docker configs
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
├── public/                 # Web root
├── src/                    # Symfony code (Controller, Entity, etc.)
├── config/                 # Symfony config files
├── var/                    # Logs, cache, etc.
├── vendor/                 # Composer dependencies (ignored by git)
├── .env                    # Main environment file
├── composer.json
├── docker-compose.yml
└── README.md
```

---

## 🧪 Running Tests

Make sure PHPUnit is installed:

```bash
docker compose exec php ./vendor/bin/phpunit
```

Or to generate the config:

```bash
docker compose exec php php bin/phpunit
```

---
