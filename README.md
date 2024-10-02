# Cryptocurrency Data Monitoring Hub with Scalable Design
- Laravel, Filament, Sqlite, TimescaleDB (pgsql), Redis (pub/sub), WebSockets (workerman)

This is the central hub for a real-time cryptocurrency data monitoring system. It serves as a coordinator and data aggregator for multiple crypto-monitor-service instances, each monitoring different cryptocurrency exchanges.

https://github.com/user-attachments/assets/31a813e4-a208-4089-8707-f48a8c4030d1

## System Architecture Overview

1. Central Hub:
   - Laravel application with Filament admin panel.
   - Receives data from all monitoring services via Redis pub/sub.
   - Provides real-time monitoring of trading pairs for all exchanges (or a specific one) via WebSockets in the browser.
   - API for querying prices by pair/exchange/time (can query all machines for specific data)

2. Monitoring Services (one per exchange):
   - Can be deployed on separate servers with their own databases.
   - Consists of two components:
     a) Monitor: Uses CCXT to subscribe to exchange tickers and publish data to Redis.
     b) Aggregator: Listens to the corresponding Redis channel, analyzes, and writes data to the database.

3. Data Storage in TimescaleDB for optimized time-series data handling:
   a) Main table `ticker_data`:
      - Contains records for all pairs with second precision.
      - Data insertion occurs in batches with a configurable interval (default 5 seconds).
      - Data is kept for 24 hours.
   b) Materialized views with continuous aggregators:
      - `ticker_data_1m`: 1-minute aggregation, stored for 7 days.
      - `ticker_data_1h`: 1-hour aggregation, stored indefinitely.
   c) Composite indexes on trading pairs and time.
   d) Table partitioning by trading pairs and time.

4. Containerization and Scalability:
   - The system is fully dockerized, ensuring quick deployment and scaling.
   - Easy addition of new monitoring services for additional exchanges.
Note: Estimated database size for 1 exchange after 1 year: ~1 GB.


## Installation

### Using Docker

1. Create a shared network (if not already):
~~~
docker network create shared-services-network
~~~

2. Clone this repository:
~~~
git clone https://github.com/smaiht/crypto-monitor-hub.git
cd crypto-monitor-hub
~~~

3. Configure the hub (or leave it as is):
Edit the `docker.env` file with appropriate settings for Redis and TimescaleDB connections.

4. Start the hub service:
~~~
docker-compose up --build
~~~
Note: this will also run redis that is needed to communicate with services. 

5. After successful start, you can access the admin panel at [http://0.0.0.0:9000/admin/](http://0.0.0.0:9000/admin/)
Use the following credentials: 
- Email: `admin@admin.com`
- Password: `admin`

### Local Installation

Before installing this hub, ensure you have the following:
- Redis server
- TimescaleDB instance (can be set up using the provided docker-compose file)

1. Install necessary system packages and PHP extensions:
~~~
sudo apt-get update && sudo apt-get install -y 
libpq-dev 
libzip-dev 
libicu-dev 
libgmp-dev 
libssl-dev 
libevent-dev 
libev-dev 
php-pdo 
php-pgsql 
php-zip 
php-bcmath 
php-intl 
php-gmp 
php-pcntl 
php-sockets
sudo pecl install redis
sudo phpenmod redis
~~~

2. Install project dependencies:
~~~
composer install
~~~
~~~
npm install
~~~
3. Configure the `.env` file with appropriate settings.

4. Run Laravel project and WebSocket server in separate terminals:
~~~
php artisan serve
~~~
~~~
php bin/server.php start
~~~

5. After successful start, you can access the admin panel at [http://0.0.0.0:8000/admin/](http://0.0.0.0:8000/admin/)
Use the following credentials: 
- Email: `admin@admin.com`
- Password: `admin`

## Usage

After starting the hub, you can set up multiple crypto-monitor-service instances to connect to this hub. Each service should be configured to use the same Redis server as the hub.

For instructions on setting up crypto-monitor-service instances, please refer to:
https://github.com/smaiht/crypto-monitor-service

## API Endpoints

1. Get Price Data:
- GET /price?exchange={exchange}&symbol={symbol}&datetime={datetime}
Example: [http://0.0.0.0:8000/price?exchange=okx&symbol=BTC/USDT&datetime=2024-10-01%2005:00:00](http://127.0.0.1:8000/price?exchange=okx&symbol=BTC/USDT&datetime=2024-10-01%2005:00:00)

2. Generate Top 100 Trading Pairs:
- GET /get-top-100/{exchangeName}/{quote}
- This endpoint generates a `pairs.json` file containing the top 100 trading pairs for a specific exchange.
Example: [http://0.0.0.0:8000/get-top-100/okx/usdt](http://0.0.0.0:8000/get-top-100/okx/usdt)

## Testing

Run the test suite with:
~~~
php artisan test
~~~
