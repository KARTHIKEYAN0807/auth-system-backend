<?php
// =======================================
// MongoDB connection (Railway compatible)
// NO composer | NO SSL | NO SRV
// =======================================

// Read MongoDB URI from environment
$mongoUri = getenv('MONGO_URI');

if (!$mongoUri) {
    http_response_code(500);
    die("MONGO_URI environment variable not set");
}

try {
    // Create MongoDB Manager
    $manager = new MongoDB\Driver\Manager($mongoUri);
} catch (Throwable $e) {
    http_response_code(500);
    die("MongoDB connection failed: " . $e->getMessage());
}

// Database & collection
$dbName = "profile_db";
$collectionName = "profiles";

/* =========================
   GET PROFILE BY USER ID
========================= */
function getProfileByUserId($userId)
{
    global $manager, $dbName, $collectionName;

    $query = new MongoDB\Driver\Query(
        ['user_id' => (int)$userId],
        ['limit' => 1]
    );

    $cursor = $manager->executeQuery(
        "$dbName.$collectionName",
        $query
    );

    foreach ($cursor as $doc) {
        return $doc;
    }

    return null;
}

/* =========================
   CREATE / UPDATE PROFILE
========================= */
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

    $result = $manager->executeBulkWrite(
        "$dbName.$collectionName",
        $bulk
    );

    // 🔴 CRITICAL: verify write actually happened
    if (
        $result->getUpsertedCount() === 0 &&
        $result->getModifiedCount() === 0
    ) {
        throw new Exception("MongoDB write failed");
    }
}
