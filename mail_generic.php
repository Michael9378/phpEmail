<?php 
  // complete all TODO's before using

  $domain = $_SERVER['HTTP_HOST']; // change this manually if need be.

  // TODO: Update dev email and emails to recieve notifications
  $notification_emails = array();
  $dev_email = "dev@example.com";
  array_push($notification_emails, $dev_email, "additional_email@example.com");

  // functional variables for control flow
  $contains_data = false;
  $emails_sent = true;
  $file_logged = false;
  $log_name = "email-".time();

  // used to store POST variables from form
  $vars = "";
  $values = array();

  // grab all the fields and put them in an array and a string
  foreach ($_POST as $key => $value) {
    // TODO: Update variables you don"t want emailed out here
    if ( $key != "redirect" && $key != "form" && $value != "" ) {
      // set contains data to true if a valid form field was found with data
      $contains_data = true;
      // add new form data to variables
      $vars .= $key . ": " . $value . "\r\n";
      array_push($values, $value);
    }
  }

  // if we dont have any data, do not log it or send a notification, just die
  if( !$contains_data ) {
    die("No data was in the form. Emails not sent.");
  }

  // log email
  date_default_timezone_set("America/Phoenix");
  $file_logged = file_put_contents("logs/".$log_name.".txt", $vars."\r\nSent: ".date("l jS \of F Y h:i:s A"));

  // send out notifications
  foreach ($notification_emails as $email) {
    // TODO: Update message and subject and header
    $sent_to .= $email;
    $to      = $email;
    $subject = "Subject";
    $message = "Here is a greeting message" . "\r\n\n" . $vars;
    $headers = "From: admin@". $domain . "\r\n" . "X-Mailer: PHP/" . phpversion();
    // if any of the email addresses fail to send, mark emails sent to false and try to continue on
    if( !mail($to, $subject, $message, $headers) ) { 
      $emails_sent = false;
    }
  }

  // Confirmation email
  // TODO: Update $to and $message with correct $_POST variable names from form
  $to      = $_POST["Email"];
  $subject = "Subject";
  $message = "Hey ".$_POST["First"]."! Here is what we recieved: " . "\r\n\n" . $vars;
  $headers = "From: admin@". $domain . "\r\n" . "X-Mailer: PHP/" . phpversion();

  mail($to, $subject, $message, $headers);

  // send an email to the dev if there was an error.
  if( !$file_logged ) {
    alertDev("An email failed to write to the log.");
  }
  if( !$emails_sent ) {
    alertDev("An email failed to send, but was logged as ".$log_name.".txt");
  }

  function alertDev($message) {
    // tell a dev an error occured
    $to      = $dev_email;
    $subject = "Form Failure at: " + $domain;
    $headers = "From: admin@". $domain . "\r\n" . "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message, $headers);
    die( $message );
  }
?>