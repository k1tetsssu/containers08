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
        
                    cat > site/config.php <<'PHP'
        <?php
        $config = [
            "db" => [
                "path" => __DIR__ . "/../test_database.db"
            ]
        ];
        PHP
        
                    echo "Initializing database..."
        
                    cat > init_db.php <<'PHP'
        <?php
        
        $dbPath = __DIR__ . "/test_database.db";
        
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $schema = file_get_contents(__DIR__ . "/sql/schema.sql");
        
        if ($schema === false) {
            die("Error: schema.sql not found!");
        }
        
        $pdo->exec($schema);
        
        echo "Schema imported successfully!\\n";
        PHP
        
                    php init_db.php
        
                    echo "Checking database..."
                    ls -lh test_database.db
        
                    echo "Checking configuration..."
                    php -r '
                        require "site/config.php";
                        echo $config["db"]["path"] . PHP_EOL;
                        echo file_exists($config["db"]["path"]) ? "Database exists" : "Database missing";
                    '
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
