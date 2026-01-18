<?php
// MongoDB connection using PHP extension (NO composer)

// Get MongoDB URI from Railway environment
$mongoUri = getenv('MONGO_URI');

if (!$mongoUri) {
    die("MONGO_URI environment variable not set");
}

try {
    // Create MongoDB Manager
    $manager = new MongoDB\Driver\Manager($mongoUri);
} catch (Exception $e) {
    die("MongoDB connection failed: " . $e->getMessage());
}

// Database & collection
$dbName = "profile_db";
$collectionName = "profiles";

/**
 * Get profile by user ID
 */
function getProfileByUserId($userId)
{
    global $manager, $dbName, $collectionName;

    $query = new MongoDB\Driver\Query(
        ['user_id' => (int)$userId],
        ['limit' => 1]
    );

    $cursor = $manager->executeQuery("$dbName.$collectionName", $query);

    foreach ($cursor as $doc) {
        return $doc;
    }

    return null;
}

/**
 * Create or update profile
 */
function updateProfile($userId, $data)
{
    global $manager, $dbName, $collectionName;

    // Always enforce user_id
    $data['user_id'] = (int)$userId;

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update(
        ['user_id' => (int)$userId],
        ['$set' => $data],
        ['upsert' => true]
    );

    $manager->executeBulkWrite("$dbName.$collectionName", $bulk);
}
