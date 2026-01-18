<?php
// ===============================
// MongoDB connection (NO composer)
// ===============================

// Get MongoDB URI from environment
$mongoUri = getenv('MONGO_URI');

if (!$mongoUri) {
    http_response_code(500);
    die("MONGO_URI environment variable not set");
}

try {
    // Create MongoDB Manager (NO SSL, NO SRV)
    $manager = new MongoDB\Driver\Manager($mongoUri);
} catch (Exception $e) {
    http_response_code(500);
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

    try {
        $query = new MongoDB\Driver\Query(
            ['user_id' => (int)$userId],
            ['limit' => 1]
        );

        $cursor = $manager->executeQuery("$dbName.$collectionName", $query);

        foreach ($cursor as $doc) {
            return $doc;
        }
    } catch (Exception $e) {
        return null;
    }

    return null;
}

/**
 * Create or update profile
 */
function updateProfile($userId, $data)
{
    global $manager, $dbName, $collectionName;

    $data['user_id'] = (int)$userId;

    try {
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['user_id' => (int)$userId],
            ['$set' => $data],
            ['upsert' => true]
        );

        $manager->executeBulkWrite(
            "$dbName.$collectionName",
            $bulk,
            new MongoDB\Driver\WriteConcern(
                MongoDB\Driver\WriteConcern::MAJORITY
            )
        );
    } catch (Exception $e) {
        // optional: error_log($e->getMessage());
    }
}
