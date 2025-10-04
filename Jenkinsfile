pipeline {
    // agent {
    //     docker {
    //         image 'laravel_jenkins:latest'   // <-- your Laravel image
    //         args '-v $PWD:/var/www'         // mount workspace
    //     }
    // }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Environment Check') {
            steps {
                sh 'php --version'
                sh 'composer --version'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
                sh 'php artisan config:clear'
                sh 'vendor/bin/phpunit --testdox'
            }
        }
    }
}
