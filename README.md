# Simple Log Microservice

## 1. Overview

Simple Log Microservice  is a microservice built with Laravel 12 designed to consume events from the "Summit API" and persist them as log entries in a MongoDB database. This service acts as a centralized logging mechanism for specific events originating from the Summit API, providing a durable and queryable store for event data.

This project utilizes Laravel Sail for a seamless Docker-based local development environment.

## 2. Features

* **Log Persistence:** Stores event data as structured log entries in a MongoDB database.
* **Scalable Architecture:** Built on Laravel 12, suitable for microservice patterns.
* **Dockerized Environment:** Uses Laravel Sail for easy local setup and consistent development environments.

## 3. Tech Stack

* **Backend Framework:** Laravel 12
* **Database:** MongoDB
* **PHP Version:** 8.3
* **Web Server:** Nginx
* **Containerization:** Docker (via Laravel Sail)
* **Dependency Management:** Composer

## 4. Prerequisites

Before you begin, ensure you have the following installed on your local machine:

* Git
* Docker Desktop (or Docker Engine and Docker Compose)

## 5. Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### 5.1. Clone the Repository

```bash
git clone [https://github.com/your-username/your-repo-name.git](https://github.com/your-username/your-repo-name.git)
cd your-repo-name
```

### 5.2. Environment Configuration

1.  **Copy Environment File:**
    Laravel utilizes an `.env` file for configuration. Copy the example file:
    ```bash
    cp .env.example .env
    ```

2.  **Configure `.env`:**
    Open the `.env` file and update the following critical variables:

    * **Application Settings:**
        ```dotenv
        APP_NAME="Summit Event Logger"
        APP_ENV=local
        APP_KEY= # Will be generated later
        APP_DEBUG=true
        APP_URL=http://localhost
        ```

    * **Database Connection (MongoDB):**
      Ensure your `docker-compose.yml` (managed by Sail) includes a MongoDB service. Update these settings to match your MongoDB container configuration (Sail defaults are often `sailmongodb`, `localhost` or service name for host, port `27017`).
        ```dotenv
        DB_CONNECTION=mongodb
        DB_HOST=mongodb # Or the service name defined in docker-compose.yml for MongoDB
        DB_PORT=27017
        DB_DATABASE=summit_logs # Your desired database name
        DB_USERNAME=sail      # Your MongoDB username (if authentication is enabled)
        DB_PASSWORD=password  # Your MongoDB password (if authentication is enabled)
        ```
      *Note: Laravel Sail's default MongoDB setup might not enforce auth initially. Adjust `DB_USERNAME` and `DB_PASSWORD` if you've configured authentication on your MongoDB service.*

    * **Summit API Configuration:**
      Add variables required to connect to the "Summit API". These are examples; actual names may vary.
        ```dotenv
        SUMMIT_API_BASE_URL=[https://api.summit.example.com/v1](https://api.summit.example.com/v1)
        SUMMIT_API_KEY=your_summit_api_key
        # Add any other Summit API related credentials or settings
        ```

### 5.3. Start Sail Containers

1.  **Build and Start Containers:**
    This command will download the necessary Docker images (if not already present) and start the application, database, and other defined services.
    ```bash
    ./vendor/bin/sail up -d
    ```
    *If `vendor/bin/sail` is not found initially (e.g., very first setup before composer install), you might need a preliminary step or use the global Sail installation method if applicable. However, for a cloned project, `composer install` (next step) should bring it in.*
    *If you encounter issues, you might need to build the images first:*
    ```bash
    # Optional: ./vendor/bin/sail build --no-cache
    ```

### 5.4. Install Dependencies & Finalize Setup

1.  **Install Composer Dependencies:**
    ```bash
    ./vendor/bin/sail composer install
    ```

2.  **Generate Application Key:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

3.  **Run Database Migrations (if applicable):**
    While MongoDB is schemaless, migrations can be used for creating collections, ensuring indexes, or initial data setup if your `mongodb/laravel-mongodb` package or application logic uses them.
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

4.  **Install NPM Dependencies & Build Assets (Optional):**
    If your microservice includes any JavaScript/CSS assets that need compilation (e.g., for a small admin panel):
    ```bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev # or prod for production assets
    ```
    For a pure backend microservice, this step might not be necessary.

### 5.5. Accessing the Application

Once Sail is up and running:
* Your application should be accessible at `http://localhost` (or the `APP_PORT` defined in `.env` if changed from default 80).
* MongoDB should be accessible on its defined port (e.g., `27017` by default from within the Docker network or mapped port if specified in `docker-compose.yml`).

## 6. Environment Variables

Key environment variables to configure in your `.env` file:

* `APP_NAME`: Name of your application.
* `APP_ENV`: Application environment (`local`, `staging`, `production`).
* `APP_DEBUG`: Debug mode (`true` for local, `false` for production).
* `APP_KEY`: Laravel application encryption key.
* `APP_URL`: Base URL of your application.

* `DB_CONNECTION=mongodb`: Specifies that MongoDB is the database driver.
* `DB_HOST`: Host for the MongoDB server (usually the Docker service name, e.g., `mongodb`).
* `DB_PORT`: Port for the MongoDB server (e.g., `27017`).
* `DB_DATABASE`: Name of the MongoDB database to use.
* `DB_USERNAME`: Username for MongoDB authentication (if enabled).
* `DB_PASSWORD`: Password for MongoDB authentication (if enabled).

* `SUMMIT_API_BASE_URL`: Base URL for the Summit API.
* `SUMMIT_API_KEY`: API key for authenticating with the Summit API.
* `LOG_CHANNEL`: Typically `stack` or `daily`. For development with Sail, logs are usually visible via `sail logs`.
* `QUEUE_CONNECTION`: If using queues for event processing (e.g., `redis`, `database`, `sync`).

## 7. Event Consumption from Summit API

This microservice is responsible for fetching/receiving events from the "Summit API". The exact mechanism depends on how Summit API exposes these events:

* **Polling:** The service might periodically poll an endpoint on the Summit API for new events using a scheduled Laravel command (Task Scheduling).
* **Webhooks:** Summit API might call a specific endpoint on this microservice whenever a new event occurs. This endpoint needs to be defined in `routes/api.php` or `routes/web.php`.
* **Message Queues:** The service might listen to a message queue (e.g., RabbitMQ, Redis, Kafka) where Summit API publishes events.

**Implementation Details:**
*(This section should be filled in with the specific logic for your microservice)*
* e.g., "Events are consumed by a scheduled job `App\Jobs\FetchSummitEventsJob` which runs every 5 minutes."
* e.g., "An API endpoint `/api/summit-webhook` is exposed to receive POST requests from Summit API."
* Detail any specific event types consumed and how they are mapped to log entries.

## 8. Database Schema (MongoDB)

Log entries are stored in a MongoDB collection (e.g., `logs` or `summit_event_logs`). A typical log document structure might be:

```json
{
  "_id": "ObjectId(...)",
  "event_type": "USER_LOGIN_SUCCESS", // Type of event from Summit API
  "event_source": "SummitAPI/v1/users", // Source of the event
  "payload": { ... }, // Original event payload from Summit API
  "processed_at": "ISODate(...)", // Timestamp when the event was processed by this microservice
  "summit_event_id": "unique_event_id_from_summit", // Optional: ID from Summit API
  "level": "info", // Log level (info, error, warning)
  "message": "User X successfully logged in.", // A descriptive message
  "context": { ... }, // Additional contextual information
  "created_at": "ISODate(...)", // Timestamp from Laravel (Eloquent)
  "updated_at": "ISODate(...)"  // Timestamp from Laravel (Eloquent)
}

