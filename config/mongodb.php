<?php
// =======================================
// MongoDB connection (Railway compatible)
// TLS REQUIRED for SCRAM-SHA-256
// =======================================

$mongoUrl = getenv('MONGO_URL');

if (!$mongoUrl) {
    http_response_code(500);
    die("MONGO_URL environment variable not set");
}

try {
    $manager = new MongoDB\Driver\Manager(
        $mongoUrl,
        [
            'tls' => true,
            'tlsAllowInvalidCertificates' => true
        ]
    );
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

    try {
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
    } catch (Throwable $e) {
        error_log("MongoDB READ error: " . $e->getMessage());
    }

    return null;
}

/* =========================
   CREATE / UPDATE PROFILE
========================= */
function updateProfile($userId, $data)
{
    global $manager, $dbName, $collectionName;

    $data['user_id'] = (int)$userId;

    $bulk = new MongoDB\Driver\BulkWrite();
    $bulk->update(
        ['user_id' => (int)$userId],
        ['$set' => $data],
        ['upsert' => true]
    );

    $result = $manager->executeBulkWrite(
        "$dbName.$collectionName",
        $bulk,
        [
            'writeConcern' => new MongoDB\Driver\WriteConcern(
                MongoDB\Driver\WriteConcern::MAJORITY,
                1000
            )
        ]
    );

    if (
        $result->getUpsertedCount() === 0 &&
        $result->getModifiedCount() === 0 &&
        $result->getMatchedCount() === 0
    ) {
        throw new Exception("MongoDB write did not persist");
    }

    return true;
}
