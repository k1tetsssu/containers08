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
                    // Создаем config.php
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
                    
                    echo 'Initializing database schema...'
                    // Создаем и запускаем init_db.php без использования cat << EOF
                    sh """
                    echo '<?php
                    \$pdo = new PDO("sqlite:test_database.db");
                    \$schema = file_get_contents("sql/schema.sql");
                    if (\$schema !== false) {
                        \$pdo->exec(\$schema);
                        echo "Schema imported successfully.\\n";
                    } else {
                        echo "Error: site/sql/schema.sql not found!\\n";
                    }
                    ' > init_db.php
                    
                    php init_db.php
                    """
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
