pipeline {
    agent any

    environment {
        DOCKERHUB_CREDENTIALS = 'dockerhub_credentials_id'
        IMAGE_NAME = 'your-dockerhub-username/laravel_jenkins'
        IMAGE_TAG = 'latest'
        GIT_REPO = 'https://github.com/yourusername/laravel_jenkins.git'
        GIT_BRANCH = 'main'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: "${GIT_BRANCH}", url: "${GIT_REPO}"
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo "Building Docker image: ${IMAGE_NAME}:${IMAGE_TAG}"
                    docker.build("${IMAGE_NAME}:${IMAGE_TAG}")
                }
            }
        }

        stage('Push Docker Image') {
            steps {
                script {
                    echo "Pushing Docker image to Docker Hub..."
                    withCredentials([usernamePassword(credentialsId: DOCKERHUB_CREDENTIALS, usernameVariable: 'USER', passwordVariable: 'PASS')]) {
                        sh "echo $PASS | docker login -u $USER --password-stdin"
                        sh "docker push ${IMAGE_NAME}:${IMAGE_TAG}"
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                echo "Cleaning up Docker login session..."
                sh "docker logout"
            }
        }
    }
}
