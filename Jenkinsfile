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
        
                sh '''
                    rm -f test_database.db
        
                    cat > config.php <<'PHP'
        <?php
        $config = [
            "db" => [
                "path" => __DIR__ . "/test_database.db"
            ]
        ];
        PHP
        
                    cat > init_db.php <<'PHP'
        <?php
        
        $pdo = new PDO("sqlite:" . __DIR__ . "/test_database.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $schema = file_get_contents(__DIR__ . "/sql/schema.sql");
        
        if ($schema === false) {
            die("Error: sql/schema.sql not found!");
        }
        
        $pdo->exec($schema);
        
        echo "Schema imported successfully.\\n";
        PHP
        
                    php init_db.php
        
                    echo "Database created:"
                    ls -l test_database.db
                '''
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
