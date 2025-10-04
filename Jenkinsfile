pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
                echo 'Code checked out successfully!'
            }
        }

        stage('Environment Check') {
            steps {
                sh 'php --version'
                sh 'composer --version'
                sh 'docker --version'
                echo 'Environment check completed!'
            }
        }

        stage('Install Dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                echo 'Dependencies installed successfully!'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'cp .env.example .env'
                sh 'php artisan key:generate'
                sh 'php artisan config:clear'
                sh 'vendor/bin/phpunit --testdox'
                echo 'Tests completed!'
            }
        }

        stage('Simple Test') {
            steps {
                echo 'Starting simple test...'
                sh 'echo "Current directory: $(pwd)"'
                sh 'ls -la'
                echo 'Laravel Jenkins pipeline test successful! 🚀'
            }
        }
    }

    post {
        always {
            echo 'Pipeline execution completed!'
        }
        success {
            echo '✅ All tests passed successfully!'
        }
        failure {
            echo '❌ Pipeline failed. Check the logs for details.'
        }
    }
}