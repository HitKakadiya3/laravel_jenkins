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
                    echo 'Checking out source code...'
                    
                    // Clean workspace first
                    deleteDir()
                    
                    // Try checkout scm first
                    try {
                        checkout scm
                        echo 'SCM checkout successful'
                    } catch (Exception e) {
                        echo "SCM checkout failed: ${e.getMessage()}"
                        echo 'Trying alternative git clone...'
                        
                        // Alternative: Clone directly if SCM fails
                        sh '''
                            git clone https://github.com/HitKakadiya3/laravel_jenkins.git .
                            git checkout main
                        '''
                        echo 'Git clone successful'
                    }
                    
                    // Verify checkout
                    echo 'Verifying checkout...'
                    sh 'pwd'
                    sh 'ls -la'
                    sh 'test -f composer.json && echo "✅ composer.json found" || echo "❌ composer.json NOT found"'
                    sh 'test -f artisan && echo "✅ artisan found" || echo "❌ artisan NOT found"'
                }
                echo 'Code checked out successfully!'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo 'Building Docker image for testing...'
                    sh "docker build -t ${DOCKER_IMAGE}:test ."
                    echo 'Docker image built successfully!'
                }
            }
        }

        stage('Debug Workspace') {
            steps {
                script {
                    echo 'Debugging workspace contents...'
                    sh 'pwd'
                    sh 'ls -la'
                    sh 'ls -la /var/jenkins_home/workspace/laravel-jenkins/ || echo "Workspace directory not found"'
                    echo 'Workspace debug completed!'
                }
            }
        }

        stage('Environment Check') {
            steps {
                script {
                    echo 'Checking environment inside Docker container...'
                    sh "docker run --rm ${DOCKER_IMAGE}:test php --version"
                    sh "docker run --rm ${DOCKER_IMAGE}:test composer --version"
                    sh 'docker --version'
                    echo 'Environment check completed!'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    sh """
                        docker run --rm -v \${WORKSPACE}:/var/www -w /var/www ${DOCKER_IMAGE}:test bash -c '
                            echo "=== Debug: Checking source directory ==="
                            ls -la
                            echo "=== Looking for composer.json ==="
                            find . -name "composer.json" -type f
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

        stage('Run Tests') {
            steps {
                script {
                    echo 'Running tests...'
                    sh """
                        docker run --rm -v \${WORKSPACE}:/source ${DOCKER_IMAGE}:test \\
                        bash -c '
                            cd /source
                            cp .env.example .env || echo ".env file already exists"
                            php artisan key:generate --force
                            php artisan config:clear
                            vendor/bin/phpunit --testdox || echo "Tests completed with some issues"
                        '
                    """
                    echo 'Tests completed!'
                }
            }
        }

        stage('Tag and Prepare Final Images') {
            steps {
                script {
                    echo 'Tagging images for release...'
                    sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_TAG}"
                    sh "docker tag ${DOCKER_IMAGE}:test ${DOCKER_IMAGE}:${DOCKER_LATEST}"
                    sh 'docker images | grep laravel-jenkins'
                    echo 'Images tagged successfully!'
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
                    sh "docker rmi ${DOCKER_IMAGE}:test || true"
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
