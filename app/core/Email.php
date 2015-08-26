<?php

namespace Helge\Framework;

class Email
{

    /**
     * @param \PHPMailer $phpMailer phpmailer instance
     * @param \Noodlehaus\Config $config config instance
     */
    public function __construct(\PHPMailer $phpMailer, $config)
    {
        $this->mailer = $phpMailer;
        $this->config = $config;
    }

    /**
     * Sends an email via SMTP
     * @param string $to_email the email to send this message to
     * @param string $subject subject of the email
     * @param string $message the body of the email
     * @return bool if the message was sent or not
     */
    public function send($to_email, $subject, $message)
    {

        $mailMethod = $this->config->get("email.method");

        if ($mailMethod == "mail") {

            $headers = 'From: ABAX Worker <no-reply@pro-ks.no>' . "\r\n" .
                'Reply-To: ABAX Worker <no-reply@pro-ks.no>' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            return mail($to_email, $subject, $message, $headers);

        } else if ($mailMethod = "smtp") {
            try {
                // Bool Flags
                $this->mailer->isHTML(false);
                $this->mailer->isSMTP();

                // SMTP Settings
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = 'tls';

                // Authentication
                $this->mailer->Host = $this->config->get("email.host");
                $this->mailer->Username = $this->config->get("email.user");
                $this->mailer->Password = $this->config->get("email.pass");

                // Set Sender and Receiver
                $this->mailer->setFrom("no-reply@pro-ks.no", "ABAX Worker");
                $this->mailer->addReplyTo("no-reply@pro-ks.no", "ABAX Worker");
                $this->mailer->addAddress($to_email);

                // Subject and Body
                $this->mailer->Subject = $subject;
                $this->mailer->Body = $message;

                // Return whether or not the message was sent
                return $this->mailer->send();

            } catch (\phpmailerException $e) {
                // Something failed, Let's puke out the error message
                die($e->errorMessage());
            }
        } else {
            // Mail is disabled, let's just pretend we sent the mail
            // so we don't have to deal with scenario checking all over the place
            return true;
        }

    }


}