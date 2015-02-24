Installation
============

Step 1: Download the Bundle
---------------------------

This bundle is part of the graviton library.

Step 2: Enable the Bundle
-------------------------

Graviton has it's own way how to register a new bundle in the symfony kernel.
In order to register this bundle it has to be instantiated in the method »\Graviton\CoreBundle\GravitonCoreBundle::getBundles()«.

```php
[...]
   public function getBundles()
    {
        return array(
           [...]
            new \Graviton\SecurityBundle\GravitonSecurityBundle\GravitonSecurityBundle(),
            [...]
        );
    }    

[...]
```

Step 3: Configuration
---------------------

Authentication 
==============

The authentication part of the bundle does provide the ability by changing the way authentication information are
provided by Airlock by configuration. 
The configuration is done by setting the parameter »graviton.security.authentication.strategy« to the service to be used.
 
```yml
parameters:
    graviton.security.authentication.strategy: <SERVICE_ID_OF_MY_AUTHENTICATION_STRATEGY>
```

It is further possible to define the class to be used to load the user object for the authentication process. 
To be explicit what service to be used for this change the »graviton.authentication.user_provider.model« parameter in
the parameters.yml file. E.g to:
 
```yml
parameters:
    graviton.authentication.user_provider.model: gravitondyn.contract.model.contract  # DEFAULT: null
```

In addition there is a command (»graviton:security:authenication:keyfinder:strategies« short: »g:s:a:k:s») gathering a 
list of authentication key finder strategies (aka services tagged as defined above). 

**NOTE**:
The service referenced in the parameter must implement the »\Graviton\RestBundle\Model\ModelInterface«.

Authorization
=============

tbd

Dependencies
------------
- \Graviton\CoreBundle\Repository\AppRepository
- \Graviton\RestBundle\Model\ModelInterface ( this should be resolved asap)
- \Symfony\Component\Security\Core\User\UserInterface

Future things
-------------
- add command to find out what strategies are available.