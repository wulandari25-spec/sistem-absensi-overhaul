FROM php:8.3-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    sqlite-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite gd mbstring zip bcmath

# Copy composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies ignoring platform requirements & skipping scripts during image build
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs --no-scripts

# Build frontend assets
RUN npm install && npm run build

# Set permissions and entrypoint execution
RUN chmod +x /app/entrypoint.sh && chmod -R 777 storage bootstrap/cache database

EXPOSE 8080

CMD ["/app/entrypoint.sh"]
