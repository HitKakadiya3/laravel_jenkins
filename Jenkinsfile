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
                sh 'docker build -t hitendra369/laravel-jenkins:latest .'
                sh 'docker images'
            }
        }

        stage('Push Docker Image') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'DOCKERHUB_CREDENTIALS',
                        usernameVariable: 'DOCKERHUB_USERNAME',
                        passwordVariable: 'DOCKERHUB_PASSWORD'
                    )
                ]) {
                    sh '''
                        echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin
                        docker push hitendra369/laravel-jenkins:latest
                        docker logout
                    '''
                }
            }
        }

        stage('Test Build') {
            steps {
                sh 'echo "Laravel project build successful!"'
            }
        }
    }
}