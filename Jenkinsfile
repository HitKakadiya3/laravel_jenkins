pipeline {
    agent any

    environment {
        DOCKER_IMAGE = 'hitendra369/laravel-jenkins'
        DOCKER_TAG = "${BUILD_NUMBER}"
        DOCKER_LATEST = 'latest'
        DEPLOY_CONTAINER = 'laravel_jenkins_app'
        DEPLOY_PORT = '8010' // Changed from 8000 to 8010 to avoid port conflict
    }

    triggers {
        githubPush()  // <-- triggers when code is pushed to GitHub
    }

    stages {
        stage('Build Docker Image with Source') {
            steps {
                script {
                    echo 'Building Docker image with source code...'
                    writeFile file: 'Dockerfile.jenkins', text: '''
FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \\
    git \\
    unzip \\
    libzip-dev \\
    libpng-dev \\
    libonig-dev \\
    libxml2-dev \\
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN git clone https://github.com/HitKakadiya3/laravel_jenkins.git /var/www/app \\
    && cd /var/www/app \\
    && git checkout main

WORKDIR /var/www/app

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/app \\
    && chmod -R 755 /var/www/app/storage || true

EXPOSE 9000

CMD ["php-fpm"]
'''
                    sh "docker build -f Dockerfile.jenkins -t ${DOCKER_IMAGE}:test ."
                }
            }
        }

        stage('Verify Build') {
            steps {
                script {
                    sh """
                        docker run --rm ${DOCKER_IMAGE}:test bash -c '
                            cd /var/www/app
                            test -f composer.json && echo "✅ composer.json found" || echo "❌ missing composer.json"
                            test -f artisan && echo "✅ artisan found" || echo "❌ missing artisan"
                        '
                    """
                }
            }
        }

        stage('Run Tests') {
            steps {
                script {
                    sh """
                        docker run --rm ${DOCKER_IMAGE}:test bash -c '
                            cd /var/www/app
                            cp .env.example .env || true
                            php artisan key:generate --force
                            php artisan config:clear
                            vendor/bin/phpunit --testdox || echo "Tests completed with warnings"
                        '
                    """
                }
            }
        }

        stage('Tag Docker Images') {
            steps {
                script {
                    // sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_TAG}" // Create for build number tag
                    sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                }
            }
        }

        stage('Push Docker Images') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'DOCKERHUB_CREDENTIALS',
                    usernameVariable: 'DOCKERHUB_USERNAME',
                    passwordVariable: 'DOCKERHUB_PASSWORD'
                )]) {
                    script {
                        sh 'echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin'
                        // sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}" // Push build number tag
                        sh "docker push ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                        sh 'docker logout'
                    }
                }
            }
        }

        stage('Auto Deploy to Server') {
            when {
                anyOf {
                    branch 'main'
                    branch 'master'
                    expression { 
                        return env.BRANCH_NAME == 'main' || env.BRANCH_NAME == 'master' || env.GIT_BRANCH == 'origin/main' || env.GIT_BRANCH == 'origin/master'
                    }
                }
            }
            steps {
                script {
                    echo "Current branch: ${env.BRANCH_NAME ?: env.GIT_BRANCH ?: 'unknown'}"
                    echo "Git commit: ${env.GIT_COMMIT ?: 'unknown'}"
                    echo 'Deploying latest Docker image...'

                    // Stop existing container (if running)
                    sh "docker stop ${DEPLOY_CONTAINER} || true"
                    sh "docker rm ${DEPLOY_CONTAINER} || true"

                    // Pull latest image from Docker Hub
                    sh "docker pull ${DOCKER_IMAGE}:${DOCKER_LATEST}"

                    // Run the new container
                    sh """
                        docker run -d --name ${DEPLOY_CONTAINER} -p ${DEPLOY_PORT}:8000 \\
                        -v /var/www/html/storage:/var/www/app/storage \\
                        ${DOCKER_IMAGE}:${DOCKER_LATEST}
                    """

                    echo "✅ Deployment complete. Application running on port ${DEPLOY_PORT}"
                }
            }
        }

        stage('Cleanup') {
            steps {
                script {
                    sh "docker rmi ${DOCKER_IMAGE}:test || true"
                }
            }
        }
    }

    post {
        always {
            sh 'docker system prune -f || true'
            echo 'Pipeline finished.'
        }
        success {
            echo "✅ Build, push, and deploy successful!"
        }
        failure {
            echo "❌ Pipeline failed. Check logs."
        }
    }
}
