pipeline {
    agent any

    stages {
        stage('Clone Repository') {
            steps {
                git branch: 'main', url: 'https://github.com/ayesha-ghaffar/lamp-guestbook-app.git'
            }
        }

        stage('Build and Deploy Containers') {
            steps {
                script {
                    sh 'docker-compose -p lamp_guestbook_app -f docker-compose.yml down || true'
                    sh 'docker-compose -p lamp_guestbook_app -f docker-compose.yml build --no-cache'
                    sh 'docker-compose -p lamp_guestbook_app -f docker-compose.yml up -d --remove-orphans'
                }
            }
        }
    }
}
