FROM php:apache

# Set working directory
COPY . /usr/src/app

# ADD ./ /usr/src/app
WORKDIR /usr/src/app

CMD ["php", "-S 0.0.0.0:8000 -t public"]
