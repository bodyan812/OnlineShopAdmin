<?php
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;

class JWTDecoder implements JWTEncoderInterface
{
    private $jwsProvider;

    public function __construct(JWSProviderInterface $jwsProvider)
    {
        $this->jwsProvider = $jwsProvider;
    }

    public function encode(array $data): string
    {
        throw new \LogicException('Not implemented for decoding');
    }

    public function decode($token)
    {
        try {
            $jws = $this->jwsProvider->load($token);

            if (!$jws->isVerified()) {
                return false;
            }

            $payload = $jws->getPayload();

            if (null === $payload) {
                return false;
            }

            // Проверка времени жизни токена
            $currentTime = time();
            if (isset($payload['exp']) && $payload['exp'] < $currentTime) {
                return false;
            }

            return $payload;
        } catch (JWTDecodeFailureException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
