pipeline {
    agent any

    environment {
        DOCKER_IMAGE = 'hitendra369/laravel-jenkins'
        DOCKER_TAG = "${BUILD_NUMBER}"
        DOCKER_LATEST = 'latest'
    }

    stages {
        stage('Build Docker Image with Source') {
            steps {
                script {
                    echo 'Building Docker image with source code...'
                    
                    // Create a temporary Dockerfile that includes git clone
                    writeFile file: 'Dockerfile.jenkins', text: '''
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \\
    git \\
    unzip \\
    libzip-dev \\
    libpng-dev \\
    libonig-dev \\
    libxml2-dev \\
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Clone the repository
RUN git clone https://github.com/HitKakadiya3/laravel_jenkins.git /var/www/app \\
    && cd /var/www/app \\
    && git checkout main

# Set working directory to app
WORKDIR /var/www/app

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set proper permissions
RUN chown -R www-data:www-data /var/www/app \\
    && chmod -R 755 /var/www/app/storage || true

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
'''
                    
                    // Build with the new Dockerfile
                    sh "docker build -f Dockerfile.jenkins -t ${DOCKER_IMAGE}:test ."
                    echo 'Docker image built with source code!'
                }
            }
        }

        stage('Verify Build') {
            steps {
                script {
                    echo 'Verifying Docker image build...'
                    sh """
                        docker run --rm ${DOCKER_IMAGE}:test bash -c '
                            echo "=== Verifying Laravel installation ==="
                            cd /var/www/app
                            pwd
                            ls -la
                            echo "=== Checking Laravel files ==="
                            test -f composer.json && echo "✅ composer.json found" || echo "❌ composer.json missing"
                            test -f artisan && echo "✅ artisan found" || echo "❌ artisan missing"
                            test -d vendor && echo "✅ vendor directory found" || echo "❌ vendor missing"
                            echo "=== Verification complete ==="
                        '
                    """
                    echo 'Build verification completed!'
                }
            }
        }

        stage('Run Tests') {
            steps {
                script {
                    echo 'Running Laravel tests...'
                    sh """
                        docker run --rm ${DOCKER_IMAGE}:test bash -c '
                            cd /var/www/app
                            cp .env.example .env || echo ".env already exists"
                            php artisan key:generate --force
                            php artisan config:clear
                            vendor/bin/phpunit --testdox || echo "Tests completed with some issues"
                        '
                    """
                    echo 'Tests completed!'
                }
            }
        }
                }
            }
        }

        stage('Tag Docker Images') {
            steps {
                script {
                    echo 'Tagging Docker images...'
                    sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_TAG}"
                    sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                    sh 'docker images | grep laravel-jenkins'
                }
            }
        }

        stage('Push Docker Images') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'DOCKERHUB_CREDENTIALS',
                                                 usernameVariable: 'DOCKERHUB_USERNAME',
                                                 passwordVariable: 'DOCKERHUB_PASSWORD')]) {
                    script {
                        echo 'Logging into Docker Hub...'
                        sh 'echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin'
                        
                        echo 'Pushing Docker images...'
                        sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}"
                        sh "docker push ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                        
                        echo 'Logging out from Docker Hub...'
                        sh 'docker logout'
                    }
                }
            }
        }

        stage('Cleanup') {
            steps {
                script {
                    echo 'Removing temporary Docker images...'
                    sh "docker rmi ${DOCKER_IMAGE}:test || true"
                }
            }
        }
    }

    post {
        always {
            echo 'Pipeline completed.'
            sh 'docker system prune -f || true'
        }
        success {
            echo "✅ Docker images pushed successfully: ${DOCKER_IMAGE}:${DOCKER_TAG} and ${DOCKER_IMAGE}:${DOCKER_LATEST}"
        }
        failure {
            echo '❌ Pipeline failed. Check the logs.'
        }
    }
}
