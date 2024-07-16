# Log Aggregation Dashboard

This project provides a solution to read an aggregated log file containing logs from various services and save it into a database. The goal is to enable the display of a dashboard with log information while continuously updating the database as new logs are written. The project includes a service with an endpoint `/count` that returns the count of log entries matching specified filters.

## Table of Contents

- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
- [Endpoints](#endpoints)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)

## Architecture

The project consists of the following components:

- **Producer**: Reads the aggregated log file in chunks and pushes the logs to RabbitMQ.
- **Consumer**: Listens to RabbitMQ, processes log entries, and inserts them into the database in batches.
- **Symfony Application**: Provides an endpoint to query log entries based on specified filters.

## Prerequisites

- Docker
- Docker Compose

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/your-username/log-aggregation-dashboard.git
    cd log-aggregation-dashboard
    ```


2. Build and start the Docker containers:

    ```bash
    make up
    ```


3. Run Consumer:

    ```bash
    make consume
    ```
5. Run Producer:

    ```bash
    make producer
    ```


 
## Usage

### Access the Symfony Application

- The Symfony application will be available at: `http://localhost:8080`

### Running Tests

- Run Symfony tests:

    ```bash
    make test
    ```

### Accessing the RabbitMQ Management UI

- RabbitMQ Management UI will be available at: `http://localhost:15672`
- Default credentials: `guest:guest`

## Endpoints

### `/count`

Returns a count of log entries matching the specified filters.

#### Request

- **Method**: GET
- **URL**: `http://localhost:8080/count`

#### Filters

- `serviceNames`: (Optional) Comma-separated list of service names. - indexed in db 
- `statusCode`: (Optional) HTTP status code. - indexed in db 
- `startDate`: (Optional) Start date (ISO 8601 format). - indexed in db 
- `endDate`: (Optional) End date (ISO 8601 format). - indexed in db 

#### Example

```sh
curl --location 'http://localhost:8080/count?serviceNames=USER-SERVICE&statusCode=200'
