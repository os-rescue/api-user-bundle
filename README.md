# APIUserBundle

This bundle is based on *FOSUserBundle* services but instead of using twig it provides services to build REST APIs to handle common tasks as user registration, forgot password and resetting password.

Included features:
- Easy integration with API Platform and Lexik JWT Authentication bundle.
- Unit tests.

Installation
------------

Installation is a quick 7 step process:

1. Download APIUserBundle using composer
2. Create your User class
3. Configure your application's security.yml
4. Configure the APIUserBundle
5. Import APIUserBundle routing
6. Update your database schema

Step 1: Download APIUserBundle using composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Require the bundle with composer:

.. code-block:: bash

    $ composer require os-rescue/api-user-bundle "1.0"
    
    
Step 3: Create your User class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




