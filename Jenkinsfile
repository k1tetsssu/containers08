pipeline {
    // Выполняем пайплайн только на нашем настроенном SSH-агенте
    agent {
        label 'php-agent'
    }
    
    stages {        
        stage('Environment Check') {
            steps {
                echo 'Checking PHP version...'
                // Убеждаемся, что PHP работает на агенте
                sh 'php -v'
            }
        }

        stage('Install Dependencies & Config') {
            steps {
                echo 'Preparing project and config...'
                
                sh """
                echo '<?php
                \$config = [
                    "db" => [
                        "path" => "test_database.db"
                    ]
                ];
                ' > site/config.php
                """
                echo 'Mock config.php created successfully.'
            }
        }
        
        stage('Install Dependencies') {
            steps {
                echo 'Preparing project...'
                // Если в корне появится composer.json, эту строку нужно будет раскомментировать:
                // sh 'composer install --no-interaction'
                echo 'No dependencies to install right now.'
            }
        }
        
        stage('Test') {
            steps {
                echo 'Running tests from /tests directory...'
                
                sh 'for test_file in tests/*.php; do php "$test_file"; done'
                
                echo 'Tests passed successfully!'
            }
        }
    }
    
    post {
        always {
            echo 'Pipeline finished.'
        }
        success {
            echo '✅ All tests passed! Pipeline completed successfully.'
        }
        failure {
            echo '❌ Errors detected in the pipeline or tests failed.'
        }
    }
}
