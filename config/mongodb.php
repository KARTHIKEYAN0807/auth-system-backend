<?php

// MongoDB connection using PHP extension (NO composer)

$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Database & collection names
$dbName = "auth_system";
$collectionName = "profiles";

// Helper function to get profile
function getProfileByUserId($userId) {
    global $manager, $dbName, $collectionName;

    $query = new MongoDB\Driver\Query(['user_id' => (int)$userId]);
    $cursor = $manager->executeQuery("$dbName.$collectionName", $query);

    foreach ($cursor as $doc) {
        return $doc;
    }
    return null;
}

// Helper function to update profile
function updateProfile($userId, $data) {
    global $manager, $dbName, $collectionName;

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update(
        ['user_id' => (int)$userId],
        ['$set' => $data],
        ['upsert' => true]
    );

    $manager->executeBulkWrite("$dbName.$collectionName", $bulk);
}
