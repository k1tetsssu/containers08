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
                    echo "Removing old test database..."
                    rm -f test_database.db
        
                    echo "Creating test configuration..."
        
                    cat > site/config.php <<'EOF'
        <?php
        $config = [
            "db" => [
                "path" => __DIR__ . "/../test_database.db"
            ]
        ];
        EOF
        
                    echo "Checking PHP configuration..."
                    php -l site/config.php
        
                    echo "Initializing database..."
        
                    php -r '
                        $path = __DIR__ . "/test_database.db";
                        $pdo = new PDO("sqlite:" . $path);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
                        $schema = file_get_contents("sql/schema.sql");
        
                        if ($schema === false) {
                            die("Schema not found!");
                        }
        
                        $pdo->exec($schema);
                        echo "Schema imported successfully!\\n";
                    '
        
                    echo "Checking database..."
                    ls -lh test_database.db
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
                
                sh 'php tests/tests.php'
                
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
