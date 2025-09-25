<?php
class S3Dummy {
    public function __construct($registry) {
        // Do nothing
    }

    // Journal3 seems to rely on these AWS S3 SDK methods:
    public function getObject($args = []) {
        // Return fake object with expected properties
        return (object)[
            'Body' => '',
            'ContentType' => 'text/plain'
        ];
    }

    public function putObject($args = []) {
        // Pretend upload success
        return true;
    }

    public function deleteObject($args = []) {
        // Pretend delete success
        return true;
    }

    public function headObject($args = []) {
        // Pretend file exists
        return (object)[
            'ContentLength' => 0,
            'ContentType'   => 'text/plain',
        ];
    }

    // You might also need these stubs later
    public function doesObjectExist($bucket, $key, $args = []) {
        return false; // always pretend it doesn't exist
    }

    public function listObjects($args = []) {
        return (object)[
            'Contents' => []
        ];
    }
}