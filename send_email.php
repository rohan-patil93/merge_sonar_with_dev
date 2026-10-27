<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    print_r($_POST);
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    // Email recipient
    $to = "naresh@infisuite.com";

    $from = "admin@vendorstaq.com";

    // Email subject
    $subject = "Message Form Vendor Registration Portal Login Page";

    // Email message
    $email_message = "Dear Sir/Madam,\n";
    $email_message .= "\n";
    $email_message .= "You have received a new message through the Vendor Registration Portal's contact form. Please find the details of the submission below:\n";
    $email_message .= "\n";
    $email_message .= "Contact Form Submission Details:\n";
    $email_message .= "\n";
    $email_message .= "Name :  $name\n";
    $email_message .= "\n";
    $email_message .= "Email :  $email\n";
    $email_message .= "\n";
    $email_message .= "Subject :  $subject\n";
    $email_message .= "\n";
    $email_message .= "Message : $message\n";
    $email_message .= "\n";
    $email_message .= "\n";
    $email_message .= "Please review the message and respond as soon as possible. If the inquiry is urgent, you may want to prioritize it and contact the sender directly.\n";
    $email_message .= "\n";
    $email_message .= "For any further assistance or if you need more information, please do not hesitate to reach out to the support team.\n";
    $email_message .= "\n";

    $email_message .= "Thank you for your attention to this matter.\n";
    $email_message .= "\n";
    $email_message .= "\n";
    $email_message .= "Best regards,\n";
    $email_message .= "\n";
    $email_message .= "Vendor Registration Portal Team.\n";
    $email_message .= "\n";

    // Email headers
    $headers = "From: $from\r\nReply-To: $from\r\n";

    // Attempt to send the email
    if (mail($to, $subject, $email_message, $headers)) {
        // Email sent successfully
        echo "Thank you! Your message has been sent.";
    } else {
        // Error sending email
        echo "Sorry, there was an error sending your message.";
    }
}
?>