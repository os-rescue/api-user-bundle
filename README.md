# APIUserBundle

This bundle is based on *FOSUserBundle* services but instead of using twig it provides services to build REST APIs to handle common tasks as user registration, forgot password and resetting password.

Included features:
- Easy integration with API Platform and Lexik JWT Authentication bundle.
- Unit tests.

## Installation

Installation is a quick 7 step process:

1. Download APIUserBundle using composer
2. Create User class
3. Configure your application's security.yml
4. Configure the APIUserBundle
5. Update database schema

#### Step 1: Download APIUserBundle using composer
Require the bundle with composer:
```
    $ composer require os-rescue/api-user-bundle "1.0"
```
    
#### Step 2: Create User class

##### a) Doctrine ORM User class

```
<?php

namespace App\Entity;

use API\UserBundle\Model\User as BaseUser;
use API\UserBundle\Model\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={
 *              "groups"={
 *                  "item_user_normalized",
 *                  "collection_users_normalized",
 *              },
 *              "enable_max_depth"=true
 *          },
 *          "denormalization_context"={
 *              "groups"={"user_model"},
 *              "allow_extra_attributes"=false,
 *          },
 *     },
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"collection_users_normalized"}}
 *          },
 *          "post",
 *      },
 *     itemOperations={
 *         "get"={"normalization_context"={"groups"={"item_user_normalized"}}},
 *         "put",
 *         "delete"
 *     }
 * @ORM\Table(name="api_user")
 *
 * @final
 */
class User extends BaseUser implements CompanySettingsInterface, LicenseSettingsInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    ...
```
    
#### Step 3: Configure your application's security.yml

```
    # app/config/security.yml
    security:
        encoders:
            App\UserBundle\Model\UserInterface:
                algorithm: 'auto'

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: ROLE_ADMIN

        providers:
            app_db_provider:
                entity:
                    class: App\Entity\User
                    property: email

        firewalls:
            login:
                pattern:  ^/api/login
                stateless: true
                anonymous: true
                provider: app_db_provider
                user_checker: App\UserBundle\Security\UserChecker
                json_login:
                    check_path: /api/login_check
                    username_path: email
                    password_path: password
                    success_handler: lexik_jwt_authentication.handler.authentication_success
                    failure_handler: lexik_jwt_authentication.handler.authentication_failure
    
            reset_password_request:
                pattern:  /api/reset-password-request
                stateless: true
                anonymous: true
    
            reset_password:
                pattern:  ^/api/reset-password
                stateless: true
                anonymous: true
    
            confirm_email:
                pattern:  ^/api/confirm
                stateless: true
                anonymous: true
    
            api:
                pattern: ^/api
                provider: app_db_provider
                stateless: true
                anonymous: true
                guard:
                    authenticators:
                        - lexik_jwt_authentication.jwt_token_authenticator
                logout:
                    path: /api/logout
                    success_handler: API\UserBundle\EventListener\LogoutListener

        access_control:
            - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api/token/refresh, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api/confirm, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api/reset-password, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api/reset-password-request, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
            
```
            
#### Step 4: Configure the APIUserBundle

```
    # app/config/config.yml
    api_user:
        user_class: App\Entity\User
        login_credential: email
       
```
            
#### Step 5: Update your database schema

```
    $ php bin/console doctrine:schema:update --force
```
