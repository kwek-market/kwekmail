<?php
include('..//helpers/helpers.php');
include('../helpers/cors.php');
include('../variables.php');
$data = cors("POST");

if (!isset($data->send_kwek_email)) {
    echo json_encode(array(
        "status" => false,
        "message" => "Access Denied"
    ));
    exit;
}

$encodedtemplate = returnIfNotExist($data, "email_template");
$emails = returnIfNotExist($data, "emails");
$cc = isset($data->cc) ? $data->cc : [];
$bcc = isset($data->bcc) ? $data->bcc : [];
$api_key = test_input(returnIfNotExist($data, "api_key"));
$from_email = test_input(returnIfNotExist($data, "from_email"));
$subject = test_input(returnIfNotExist($data, "subject"));
$product_name = test_input(returnIfNotExist($data, "product_name"));
$decodedTemplate = base64_decode($encodedtemplate);

if ($api_key != $main_api_key) {
    $message = array(
        "status" => false,
        "message" => "Access Denied"
    );

    echo json_encode($message);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = array(
        "status" => false,
        "message" => "Invalid Email Address"
    );

    echo json_encode($message);
    exit;
}

if (!is_array($emails)) {
    echo json_encode(array(
        "status" => false,
        "message" => "emails is a list of emails"
    ));
    exit;
}

checkMails($emails);

if (is_array($cc) && count($cc) > 0) {
    checkMails($cc);
}

if (is_array($bcc) && count($bcc) > 0) {
    checkMails($bcc);
}


sendEmail($from_email, $subject, $decodedTemplate, $product_name, $emails, $cc, $bcc);
