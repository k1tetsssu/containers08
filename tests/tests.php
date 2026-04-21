<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
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
    return assertExpression($db->Count("page") >= 3);
}

function testDbCreate() {
    global $config;
    $db = new Database($config['db']["path"]);

    $id = $db->Create("page", [
        "title" => "Test Page",
        "content" => "This is a test page.",
    ]);

    return assertExpression($id > 0);
}

function testDbRead() {
    global $config;
    $db = new Database($config['db']["path"]);

    $data = $db->Read("page", 1);

    return assertExpression(isset($data['title']));
}

function testDbUpdate() {
    global $config;
    $db = new Database($config['db']["path"]);

    $db->Update("page", 1, [
        "title" => "Updated Test Page",
    ]);

    $data = $db->Read("page", 1);

    return assertExpression($data['title'] === "Updated Test Page");
}

function testDbDelete() {
    global $config;
    $db = new Database($config['db']["path"]);

    $id = $db->Create("page", [
        "title" => "Test Page to Delete",
        "content" => "This page will be deleted.",
    ]);

    $db->Delete("page", $id);

    $data = $db->Read("page", $id);

    return assertExpression($data === null);
}

function testPageRender() {
    $page = new Page(__DIR__ . '/../templates/index.tpl');

    $html = $page->Render([
        "title" => "Test title",
        "content" => "This is a test page.",
    ]);

    return assertExpression(
        strpos($html, "Test title") || strpos($html, "Test title") !== false
    );
}

$testFramework->add("Database Connection", "testDbConnection");
$testFramework->add("Database Count", "testDbCount");
$testFramework->add("Database Create", "testDbCreate");
$testFramework->add("Database Read", "testDbRead");
$testFramework->add("Database Update", "testDbUpdate");
$testFramework->add("Database Delete", "testDbDelete");
$testFramework->add("Page Render", "testPageRender");

$testFramework->run();

echo $testFramework->getResult();