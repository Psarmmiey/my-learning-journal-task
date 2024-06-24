# My Learning Journal 
The Live version can be found [here](http://35.179.77.184/)
### Login Credentials
```bash
Email: test@example.com
Password: password
```

## Introduction
My Learning Journal is a web application that allows users to create and manage journal entries. Users can register an account, log in, and start creating journal entries with a title, body, excerpt and a cover photo. The application provides a simple and intuitive interface for users to view, edit, and delete their journal entries. Users can also draft entries, publish them, and view a list of all published entries.

## Running the Project
The project can be run locally using Laravel's built-in development server or using Laravel Sail, a light-weight command-line interface for interacting with Laravel's default Docker development environment. The following sections provide instructions for running the project locally and using Docker Sail.

### Local Installation
Requirements:
- PHP 7.4 or higher
- Composer
- MySQL/PgSQL
- Node.js
- NPM
- Laravel CLI
After installing the above dependencies, you can follow these steps to run the project locally:
1. Clone the repository to your local machine:
```bash
git clone
```
2. Change into the project directory:
```bash
cd my-learning-journal
```
3. Install PHP dependencies:
```bash
composer install
```
4. Install NPM dependencies:
```bash
npm install
```
5. Create a new `.env` file by copying the `.env.example` file:
```bash
cp .env.example .env
```
6. Generate an application key:
```bash
php artisan key:generate
```

7. Update the `.env` file with your database credentials:
```bash
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

To use Sqlite, update the `.env` file with the following configuration:
```bash
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
````
8. Run the database migrations and seed the database with sample data:
```bash
php artisan migrate --seed 
```
9. Start the development server:
```bash
php artisan serve
```
10. Visit `http://localhost:8000` in your browser to view the application.
11. You can now register a new account, log in, and start creating journal entries.
12. An example user with the following credentials has been seeded into the database:
```bash
Email: test@example.com
Password: password
```

### Docker Sail
Alternatively, you can use Laravel Sail to run the project in a Docker container. Sail is a light-weight command-line interface for interacting with Laravel's default Docker development environment. To use Sail, you need to have Docker installed on your machine.

To run the project using Sail, follow these steps:
Requirements:
- Docker: https://docs.docker.com/get-docker/
- Start Docker on your machine before proceeding with the installation.

1. Clone the repository to your local machine:
```bash
git clone
```
2. Change into the project directory:
```bash
cd my-learning-journal
```
3. Copy the `.env.example` file to a new `.env` file:
```bash
cp .env.example .env
```
4. Start the Sail containers:
```bash
./vendor/bin/sail up
```
5. Install PHP dependencies:
```bash
./vendor/bin/sail composer install
```
6. Install NPM dependencies:
```bash
./vendor/bin/sail npm install
```
7. Generate an application key:
```bash
./vendor/bin/sail artisan key:generate
```
8. Run the database migrations:
```bash
./vendor/bin/sail artisan migrate --seed
```
9. Visit `http://localhost` in your browser to view the application.
10. You can now register a new account, log in, and start creating journal entries.
11. An example user with the following credentials has been seeded into the database:
```bash
Email: test@example.com
Password: password
```

## Deployment
The application is deployed on an AWS EC2 instance using Laravel Forge as the server management platform. The deployment process is automated using GitHub Actions, which triggers a deployment whenever changes are pushed to the `main` branch of the repository.

Laravel Forge is a server management and deployment platform for PHP applications. It provides a simple yet powerful interface for deploying and managing web applications on cloud servers. Forge supports popular cloud providers such as AWS, DigitalOcean, and Linode, and it automates the process of setting up servers, configuring databases, and deploying applications.

## Technologies Used
- **Laravel**: Laravel is a PHP web application framework that provides an elegant syntax and tools for building modern web applications. It follows the MVC (Model-View-Controller) architectural pattern and includes features such as routing, authentication, and database migrations.
- **PGSQL**: PostgreSQL is a powerful, open-source relational database management system that provides robust support for complex queries, transactions, and data integrity. It is widely used in production environments for its scalability, performance, and reliability.
- Docker: Docker is a platform for developing, shipping, and running applications in containers. It provides a lightweight, isolated environment for applications to run consistently across different environments.
- **AWS**: Amazon Web Services (AWS) is a cloud computing platform that offers a wide range of services, including computing power, storage, and databases. It provides scalable, reliable, and cost-effective solutions for hosting web applications and managing infrastructure.
- **Laravel Forge**: Laravel Forge is a server management and deployment platform for PHP applications. It simplifies the process of setting up servers, configuring databases, and deploying applications on cloud providers such as AWS, DigitalOcean, and Linode.
- **GitHub Actions**: GitHub Actions is a continuous integration and continuous deployment (CI/CD) platform that automates the process of building, testing, and deploying code. It allows developers to define workflows in code and trigger actions based on events such as pushes, pull requests, and issue comments.
- **Vue.js**: Vue.js is a progressive JavaScript framework for building user interfaces. It provides a simple and flexible API for creating interactive web applications and components.
- **Tailwind CSS**: Tailwind CSS is a utility-first CSS framework that provides a set of pre-built classes for styling web applications. It allows developers to quickly design responsive layouts and customize styles using a functional approach.
- **Inertia.js**: Inertia.js is a library that allows developers to build single-page applications using server-side routing and client-side rendering. It provides a seamless experience for users by preserving the server-rendered HTML and updating the page content dynamically.

## ULID
A ULID is a 128-bit universally unique identifier that is encoded as a 26-character string. It is designed to be used as a primary key for database tables and is well-suited for use in distributed systems.
Why I choose ULID as the primary key for my database tables:
- Sortability: Unlike UUIDs, which are random and do not provide any inherent sorting, ULIDs are designed to be lexicographically sortable. This means that when ULIDs are sorted, they also represent a chronological order of generation. In certain scenarios where sorting is crucial, ULIDs can be more efficient and straightforward to work with.
- Collision Avoidance: While traditional UUIDs are highly unlikely to collide due to their vast address space, collisions can still occur in distributed systems with a high volume of concurrent requests. ULIDs, with their time-based component, can reduce the likelihood of collisions, especially in scenarios with rapid ID generation.
- Compactness: ULIDs are generally more compact than standard UUIDs, which are represented in hexadecimal form with dashes. The base32 representation of ULIDs is more space-efficient, which can be beneficial in scenarios where space optimization is important.
- Lower Entropy: Some argue that the high entropy of traditional UUIDs might not always be necessary, and using ULIDs, which have a portion based on the current time, could provide sufficient uniqueness while being more predictable.
- Readability: ULIDs, being base32 encoded and containing a timestamp component, can be more human-readable than traditional UUIDs. This can be advantageous in certain contexts, such as debugging or logging.
- Deterministic Generation: Generating ULIDs does not require a centralized authority or coordination, as is the case with certain versions of UUIDs. This makes them easier to generate in distributed systems without worrying about contention or synchronization.
- Consistency in Distributed Systems: Because ULIDs have a time-based component, they can lead to a more consistent ordering of events in distributed systems, making it easier to reason about causality and chronology.
- More information about the differences between ULIDs, UUIDs and Integer can be found [here](https://blog.hassam.dev/ulid-uuid-integer-ids/).

## Email Verification
For the sake of simplicity, I have disabled email verification in the application. However, enabling email verification is a common practice in web applications to ensure that users provide a valid email address and to prevent spam or abuse. When email verification is enabled, users are required to verify their email address by clicking on a link sent to their email before they can access certain features of the application.

## Conclusion
My Learning Journal is a simple and intuitive web application that allows users to create and manage journal entries. The application provides a user-friendly interface for users to register an account, log in, and start creating journal entries with a title, body, excerpt, and cover photo. Users can draft entries, publish them, and view a list of all published entries. The application is built using Laravel, Vue.js, Tailwind CSS, and Inertia.js, and it is deployed on an AWS EC2 instance using Laravel Forge. The deployment process is automated using GitHub Actions, which triggers a deployment whenever changes are pushed to the `main` branch of the repository. My Learning Journal is a showcase of modern web development practices and technologies, and it provides a solid foundation for building more complex web applications in the future.
