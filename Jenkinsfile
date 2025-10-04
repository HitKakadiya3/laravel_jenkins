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
                script {
                    echo 'Cleaning and checking out source code...'
                    deleteDir() // Clean workspace
                    
                    // Clone the repository
                    sh '''
                        git clone https://github.com/HitKakadiya3/laravel_jenkins.git .
                        git checkout main
                        echo "=== Git clone completed ==="
                    '''
                    
                    // Verify code
                    echo 'Verifying checkout...'
                    sh 'pwd'
                    sh 'whoami'
                    sh 'ls -la'
                    sh 'test -f composer.json && echo "✅ composer.json found" || echo "❌ composer.json missing"'
                    sh 'test -f artisan && echo "✅ artisan found" || echo "❌ artisan missing"'
                    
                    // Show workspace variable
                    echo "Workspace: ${WORKSPACE}"
                    sh 'echo "Current directory: $(pwd)"'
                }
            }
        }

        stage('Debug Workspace') {
            steps {
                script {
                    echo 'Debugging workspace state...'
                    sh '''
                        echo "=== Workspace Debug Info ==="
                        echo "Current directory: $(pwd)"
                        echo "Workspace variable: $WORKSPACE"
                        echo "Directory contents:"
                        ls -la
                        echo "Checking for Laravel files:"
                        ls -la | grep -E "(composer\.json|artisan|\.env\.example)" || echo "No Laravel files found"
                        echo "=== End Debug ==="
                    '''
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo 'Building Docker image...'
                    sh "docker build -t ${DOCKER_IMAGE}:test ."
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    echo 'Installing PHP dependencies inside Docker...'
                    sh """
                        docker run --rm -v \${WORKSPACE}:/var/www -w /var/www ${DOCKER_IMAGE}:test bash -c '
                            if [ -f composer.json ]; then
                                echo "Found composer.json, running composer install..."
                                composer install --no-interaction --prefer-dist --optimize-autoloader
                            else
                                echo "ERROR: composer.json not found"
                                exit 1
                            fi
                        '
                    """
                }
            }
        }

        stage('Run Laravel Commands & Tests') {
            steps {
                script {
                    echo 'Running Laravel setup and PHPUnit tests...'
                    sh """
                        docker run --rm -v \${WORKSPACE}:/var/www -w /var/www ${DOCKER_IMAGE}:test bash -c '
                            cp .env.example .env || echo ".env already exists"
                            php artisan key:generate --force
                            php artisan config:clear
                            if [ -f vendor/bin/phpunit ]; then
                                vendor/bin/phpunit --testdox
                            else
                                echo "PHPUnit not found, skipping tests"
                            fi
                        '
                    """
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
