<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modeles/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();

function testDbConnection() {
    global $config;
    $db = new Database($config['db']["path"]);
    return assertExpression($db !== null);
}

function testDbCount() {
    global $config;
    $db = new Database($config['db']["path"]);
    return assertExpression($db->$count("page") >= 3);
}

function testDbCreate() {
    global $config;
    $db = new Database($config['db']["path"]);
    $id = $db->create("page", [
        "title" => "Test Page",
        "content" => "This is a test page.",
    ]);
    return assertExpression($id > 0);
}

function testDbRead() {
    global $config;
    $db = new Database($config['db']["path"]);
    $data = $db->read("page", 1);
    return assertExpression(isset($data['title']));
}

function testDbUpdate() {
    global $config;
    $db = new Database($config['db']["path"]);

    $db->Update("page", 1, ["title" => "Updated Test Page",]);
    
    $data = $db->read("page", 1);
    return assertExpression($data['title'] === "Updated Test Page");
}

function testDbDelete() {
    global $config;
    $db = new Database($config['db']["path"]);

    $id = $db->create("page", [
        "title" => "Test Page to Delete",
        "content" => "This page will be deleted.",
    ]);

    $db->delete("page", $id);
    
    $data = $db->read("page", $id);
    return assertExpression($data === null);
}

function testPageRender() {
    $page = new Page(__DIR__ . '/../templates/page.php');

    $html = $page->render([
        "title" => "Test title",
        "content" => "This is a test page.",
    ]);

        return assertExpression(strpos($html, "Test title") !== false);
    }

$testFramework->addTest("Database Connection", "testDbConnection");
$testFramework->addTest("Database Count", "testDbCount");
$testFramework->addTest("Database Create", "testDbCreate");
$testFramework->addTest("Database Read", "testDbRead");
$testFramework->addTest("Database Update", "testDbUpdate");
$testFramework->addTest("Database Delete", "testDbDelete");
$testFramework->addTest("Page Render", "testPageRender");

$testFramework->runTests();

echo $testFramework->getResults();