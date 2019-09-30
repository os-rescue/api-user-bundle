<?php

namespace API\UserBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "post"={
 *              "messenger"=true,
 *              "output"=false,
 *              "_api_persist"=false,
 *              "_api_respond"=false,
 *              "summary"="Send email with token to reset password.",
 *              "path"="/reset-password-request",
 *              "status"=202,
 *              "swagger_context"={
 *                  "parameters"={
 *                      {
 *                          "name"="email",
 *                          "in"="path",
 *                          "required"="true",
 *                          "type"="string"
 *                      }
 *                  },
 *                  "responses"={
 *                      "202"={
 *                          "description"="the request has been received and will be treated later.",
 *                      },
 *                      "400"={
 *                          "description"="Validation failed.",
 *                      },
 *                      "404"={
 *                          "description"="User not found.",
 *                      },
 *                      "401"={
 *                          "description"="User account locked.",
 *                      },
 *                      "400"={
 *                          "description"="A reset password request already available in the user email.",
 *                      }
 *                  }
 *              }
 *          }
 *     },
 *     itemOperations={"get"},
 *     output=false,
 * )
 */
final class ResetPasswordRequest
{
    /**
     * @ApiProperty(identifier=true)
     */
    public $id;

    /**
     *
     * @Assert\Email(message="invalid")
     * @Assert\Length(
     *     min = 2,
     *     max = 180,
     *     minMessage = "length.min,{{ limit }}",
     *     maxMessage = "length.max,{{ limit }}",
     * )
     * @Assert\NotNull(message="not_null")
     * @Assert\NotBlank(message="not_blank")
     */
    public $email;

    public function getId(): int
    {
        return mt_rand();
    }
}
