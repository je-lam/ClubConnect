<?php

// call this anywhere you need to talk to the database
function get_db()
{
    // __DIR__ is the folder this file lives in
    $path = __DIR__ . '/../database.db';
    $db = new PDO('sqlite:' . $path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // show errors instead of silently failing
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // return rows as named arrays
    return $db;
}
