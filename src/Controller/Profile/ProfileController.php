<?php

declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\DefaultController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ProfileController extends DefaultController
{
    #[Route('/profile/2fa/enable', name: 'profile_2fa_enable', methods: ['GET'])]
    public function enable2fa(TotpAuthenticatorInterface $totpAuthenticator, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->isTotpAuthenticationEnabled()) {
            $user->setTotpSecret($totpAuthenticator->generateSecret());

            $entityManager->flush();
        }

        return $this->render('profile/enable2fa.html.twig');
    }

    #[Route('/profile/2fa/enable', name: 'profile_complete_2fa_enable', methods: ['POST'])]
    public function confirm2fa(Request $request, TotpAuthenticatorInterface $totpAuthenticator, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($totpAuthenticator->checkCode($user, $request->get('auth_code', ''))) {
            $user->setTotpEnabled(true);

            $entityManager->flush();

            return $this->render('profile/finish2fa.html.twig');
        }

        return $this->render(
            'profile/enable2fa.html.twig', [
                'validationError' => 'Invalid code, please re-scan QR code and try again',
            ]
        );
    }

    #[Route('/profile/2fa/qr-code', name: 'profile_2fa_qr_code')]
    public function displayGoogleAuthenticatorQrCode(TotpAuthenticatorInterface $totpAuthenticator)
    {
        $qrCodeContent = $totpAuthenticator->getQRContent($this->getUser());
        $result        = Builder::create()
            ->data($qrCodeContent)
            ->build();

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}
