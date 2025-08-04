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

### Install NPM dependencies

Run the following inside the PHP container:

```bash
docker compose exec exec php npm install
```

### Build frontend assets

Run the following inside the PHP container:

```bash
docker compose exec exec npm run dev
```

### Open in browser

Visit: [http://localhost](http://localhost)

If you see the Button, you're good to go!