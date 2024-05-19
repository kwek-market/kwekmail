<?php

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function returnIfNotExist($data, $fieldName)
{
    // Check if the field name exists in the data (handles both objects and associative arrays)
    if (is_array($data)) {
        $exists = array_key_exists($fieldName, $data);
    } elseif (is_object($data)) {
        $exists = property_exists($data, $fieldName);
    } else {
        // If $data is neither an array nor an object, it is considered as not set
        $exists = false;
    }

    // If the field does not exist, return a JSON response and exit
    if (!$exists) {
        echo json_encode(array(
            "status" => false,
            "message" => "$fieldName is required"
        ));
        exit;
    } else {
        return is_array($data) ? $data[$fieldName] : $data->$fieldName;
    }
}


function sendEmail($from_email, $subject, $body, $product_name, array $recipients, array $cc, array $bcc)
{
    $to = implode(', ', $recipients);
    $message = $body;
    $headers = "";

    if (count($cc) > 0) {
        $headers .= "CC: " . implode(', ', $cc) . "\r\n";
    }

    if (count($bcc) > 0) {
        $headers .= "BCC: " . implode(', ', $bcc) . "\r\n";
    }

    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . $product_name . ' <' . $from_email . '>' . "\r\n";
    $success = mail($to, $subject, $message, $headers);
    if ($success) {
        $message = array(
            "status" => true,
            "message" => "Successfully Sent"
        );

        echo json_encode($message);
        exit;
    } else {
        $message = array(
            "status" => $success,
            "message" => "Error Sending E-mail: " . error_get_last()["message"]
        );

        echo json_encode($message);
        exit;
    }
}


function checkMails(array $emails)
{
    foreach ($emails as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array(
                "status" => false,
                "message" => "email " . $email . "is invalid"
            ));
            exit;
        }
    }
}
