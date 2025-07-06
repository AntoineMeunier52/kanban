<?php

namespace App\Service;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class EmailSender
{
    private MailerInterface $mailer;
    private string $projectDir;

    public function __construct(MailerInterface $mailer, KernelInterface $kernel)
    {
        $this->mailer = $mailer;
        $this->projectDir = $kernel->getProjectDir();
    }

    public function send(
        string $template,
        array $replacer,
        string $sendTo,
        string $subject,
        string $from = 'kanban@meunco.space'
    ): void {
        $templatePath = "{$this->projectDir}/src/HtmlTemplateMail/{$template}";

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Email template '$template' not found at $templatePath");
        }

        $html = file_get_contents($templatePath);

        foreach ($replacer as $key => $value) {
            $html = str_replace("{{ $key }}", $value, $html);
        }

        $email = (new Email())
            ->from($from)
            ->to($sendTo)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);

    }
}
