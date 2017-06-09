#!/bin/bash
<?php

include('fileStream.php');

// Create new file to write too.
$handle = new fileStream('test.bin', 'c');

// Tell if we failed to open it due to permissions.
if(!$handle->isOpen()) { echo "Unable to open file for creation.\n"; exit(); }

// Goto position zero.
$handle->setPosition(0);

// Test write some stuff.
$handle->writeAsciiString('HELLO');
$handle->writeInt16(20);
$handle->writeInt32(20);
$handle->writeInt64(9223372036854775807);
$handle->writeUInt16(20);
$handle->writeUInt32(20);
$handle->writeUInt64(9223372036854775807);
// Closed the newly created filestream.
$handle->close();

// Reopen said file, with read permissions.
$handle = new fileStream('test.bin', 'r');

// Goto position zero.
$handle->setPosition(0);

// Dump to the console each value we wrote above...
var_dump($handle->readAsciiString(5));
var_dump($handle->readInt16());
var_dump($handle->readInt32());
var_dump($handle->readInt64());
var_dump($handle->readUInt16());
var_dump($handle->readUInt32());
var_dump($handle->readUInt64());

// Close to save memory leaks.
$handle->close();
// Optionable delete said file after.
//unlink('test.bin');
// Cleanup pointers.
unset($handle);

?>
