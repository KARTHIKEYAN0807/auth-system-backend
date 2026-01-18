FROM php:8.2-cli

# 1️⃣ Install system dependencies (THIS IS THE FIX)
RUN apt-get update && apt-get install -y \
    libssl-dev \
    pkg-config \
    unzip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# 2️⃣ Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3️⃣ Install Redis + MongoDB extensions WITH SSL
RUN pecl install redis mongodb \
    && docker-php-ext-enable redis mongodb

# 4️⃣ App setup
WORKDIR /app
COPY . /app

EXPOSE 8080

# 5️⃣ Start server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
