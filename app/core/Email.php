<?php

namespace Helge\Framework;


// TODO(30 aug 2015) ~ Helge: put ssl/tls option in config file

class Email
{

    /**
     * @var \PHPMailer $phpMailer phpmailer instance
     */
    protected $mailer;

    /**
     * @var \Noodlehaus\Config $config config instance
     */
    protected $config;


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

        $from_name      = $this->config->get("email.from_name");
        $from_email     = $this->config->get("email.from_email");
        $mail_method    = $this->config->get("email.method");

        if ($mail_method == "mail") {

            $headers  = "From: $from_name <$from_email>\r\n";
            $headers .= "Reply-To: $from_name <$from_email>\r\n";
            $headers .= 'X-Mailer: PHP/' . phpversion();

            return mail($to_email, $subject, $message, $headers);

        } else if ($mailMethod = "smtp") {
            try {
                // Bool Flags
                $this->mailer->isHTML($this->config->get("email.html"));
                $this->mailer->isSMTP();

                // SMTP Settings
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = $this->config->get("email.secure");

                // Authentication
                $this->mailer->Host = $this->config->get("email.host");
                $this->mailer->Username = $this->config->get("email.user");
                $this->mailer->Password = $this->config->get("email.pass");

                // Set Sender and Receiver
                $this->mailer->setFrom($from_email, $from_name);
                $this->mailer->addReplyTo($from_email, $from_name);
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