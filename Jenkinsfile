pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                sh 'docker build -t laravel_jenkins .'
            }
        }

        stage('Run Commands in Container') {
            steps {
                sh 'docker run --rm -v $PWD:/var/www laravel_jenkins php --version'
                sh 'docker run --rm -v $PWD:/var/www laravel_jenkins composer --version'
                sh 'docker run --rm -v $PWD:/var/www laravel_jenkins composer install --no-interaction --prefer-dist --optimize-autoloader'
                sh 'docker run --rm -v $PWD:/var/www laravel_jenkins php artisan key:generate'
                sh 'docker run --rm -v $PWD:/var/www laravel_jenkins vendor/bin/phpunit --testdox'
            }
        }
    }
}
