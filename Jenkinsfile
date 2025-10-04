pipeline {
    agent any

    environment {
        DOCKER_IMAGE = 'hitendra369/laravel-jenkins'
        DOCKER_TAG = "${BUILD_NUMBER}"
        DOCKER_LATEST = 'latest'
    }

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
                echo 'Tests completed successfully!'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo 'Building Docker image...'
                    sh "docker build -t ${DOCKER_IMAGE}:${DOCKER_TAG} ."
                    sh "docker build -t ${DOCKER_IMAGE}:${DOCKER_LATEST} ."
                    sh 'docker images | grep laravel-jenkins'
                    echo 'Docker image built successfully!'
                }
            }
        }

        stage('Push to Docker Hub') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'DOCKERHUB_CREDENTIALS',
                        usernameVariable: 'DOCKERHUB_USERNAME',
                        passwordVariable: 'DOCKERHUB_PASSWORD'
                    )
                ]) {
                    script {
                        echo 'Logging into Docker Hub...'
                        sh 'echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin'
                        
                        echo 'Pushing Docker images...'
                        sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}"
                        sh "docker push ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                        
                        echo 'Logging out from Docker Hub...'
                        sh 'docker logout'
                        
                        echo 'Docker images pushed successfully!'
                    }
                }
            }
        }

        stage('Cleanup') {
            steps {
                script {
                    echo 'Cleaning up local Docker images...'
                    sh "docker rmi ${DOCKER_IMAGE}:${DOCKER_TAG} || true"
                    echo 'Cleanup completed!'
                }
            }
        }
    }

    post {
        always {
            echo 'Pipeline execution completed!'
            sh 'docker system prune -f || true'
        }
        success {
            echo '✅ Pipeline completed successfully!'
            echo "🐳 Docker image pushed: ${DOCKER_IMAGE}:${DOCKER_TAG}"
            echo "🐳 Docker image pushed: ${DOCKER_IMAGE}:latest"
        }
        failure {
            echo '❌ Pipeline failed. Check the logs for details.'
        }
    }
}
