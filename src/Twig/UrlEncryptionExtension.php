<?php
namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UrlEncryptionExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('encrypt_email', [$this, 'encryptEmail']),
            new TwigFilter('decrypt_email', [$this, 'decryptEmail']),
        ];
    }

    public function encryptEmail(string $email): string
    {
        $encryptedEmail = urlencode($email);
        return $encryptedEmail;
    }

    public function decryptEmail(string $encryptedEmail): string
    {
        $decryptedEmail = urldecode($encryptedEmail);
        return $decryptedEmail;
    }
}