
<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));
if (isset($data->send_kwek_email)) {
    include_once("./helpers/helpers.php");
    include('variables.php');
    $email = test_input($data->email);
    $product_name = test_input($data->product_name);
    $api_key = test_input($data->api_key);
    $from_email = test_input($data->from_email);
    $subject = test_input($data->subject);
    $message_event = $data->event;

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

    if ($message_event == "firstCorReset") {
        $title = $data->title;
        $small_text_detail = $data->small_text_detail;
        $link = $data->link;
        $button_name = $data->link_keyword;
        include('email.php');
    } else if ($message_event == "email_verification") {
        $link = $data->link;
        $name = $data->name;
        include('email_verification.php');
    } else if ($message_event == "forgot_password") {
        $link = $data->link;
        include('forgot_password.php');
    } else if ($message_event == "notification") {
        $notification_title = $data->notification_title;
        $name = $data->name;
        $no_html_content = $data->no_html_content;
        $html_content = $data->html_content;
        include('notification.php');
    } else {
        $message = array(
            "status" => false,
            "message" => "Event Does not exist"
        );
        echo json_encode($message);
        exit;
    }


    $to = $email;
    $subject = $subject;
    $txt = $message_body;
    $headers = "";
    $headers .= "CC: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . $product_name . ' <' . $from_email . '>' . "\r\n";
    $success = mail($to, $subject, $txt, $headers);
    if ($success) {
        $message = array(
            "status" => true,
            "message" => "Successfully Sent"
        );

        echo json_encode($message);
    } else {
        $message = array(
            "status" => $success,
            "message" => "Error Sending E-mail: " . error_get_last()["message"]
        );

        echo json_encode($message);
    }
} else {
    $message = array(
        "status" => false,
        "message" => "Access Denied"
    );

    echo json_encode($message);
    exit;
}
?>