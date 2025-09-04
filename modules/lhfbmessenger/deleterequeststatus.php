<?php

// Just dummy confirmation for data removal, because we are removing data on initial request

// Extract user ID from the URL (e.g. /deletion-status/123456)
$user_id = $Params['user_parameters']['user_id'];

// TODO: Lookup in your database whether this user’s data was deleted
// For demo purposes, assume it was successfully deleted
$status = [
    "user_id" => $user_id,
    "status" => "deleted",  // could be "pending", "deleted", or "not_found"
    "timestamp" => date("c") // ISO 8601 format
];

// Respond with JSON
header('Content-Type: application/json');
echo json_encode($status);
exit;