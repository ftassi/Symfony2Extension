Symfony2 Extension
==================

`Symfony2 <http://symfony.com>`_ is a PHP Web Development Framework. This
extension provides integration with it.

Symfony2Extension provides:

* Complete integration into Symfony2 bundle structure - you can define an
  isolated bundle suite by bundle name
* KernelAwareContext, which provides an initialized and booted kernel
  instance for your contexts
* Additional ``symfony2`` session for Mink (if ``MinkExtension`` is used)

Installation
------------

This extension requires:

* Behat 3.0+

Through PHAR
~~~~~~~~~~~~

First, download phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>`_ - Behat itself
* `symfony2_extension.phar <http://behat.org/downloads/symfony2_extension.phar>`_
  - integration extension

After downloading and placing ``*.phar`` into project directory, you need to
activate ``Symfony2Extension`` in your ``behat.yml``:

    .. code-block:: yaml

        default:
          # ...
          extensions:
            symfony2_extension.phar: ~


Through Composer
~~~~~~~~~~~~~~~~

The easiest way to keep your suite updated is to use `Composer <http://getcomposer.org>`_:

1. Define dependencies in your `composer.json`:

    .. code-block:: js

        {
            "require": {
                ...

                "behat/symfony2-extension": "~2.0@dev"
            }
        }

2. Install/update your vendors:

    .. code-block:: bash

        $ composer update behat/symfony2-extension

3. Activate extension in your ``behat.yml``:

    .. code-block:: yaml

        default:
            # ...
            extensions:
                Behat\Symfony2Extension\Extension: ~

4. Register a suite for your bundle:

    .. code-block:: yaml

        default:
            suites:
                my_suite:
                    type: symfony_bundle
                    bundle: AcmeDemoBundle

.. note::

    If you're using Symfony2.1 with Composer, there could be a conflict of
    Symfony2 with Behat, that will prevent Symfony2 from loading Doctrine
    or Validation annotations. This is not a problem with the latest version
    of Composer, but if you are running an older version and see errors,
    just update your ``app/autoload.php``:

    .. code-block:: php

        <?php

        use Doctrine\Common\Annotations\AnnotationRegistry;

        if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
            $loader = require __DIR__.'/../vendor/autoload.php';
        } else {
            $loader = new Composer\Autoload\ClassLoader();
            $loader->register();
        }

        // intl
        if (!function_exists('intl_get_error_code')) {
            require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

            $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
        }

        AnnotationRegistry::registerLoader('class_exists');

        return $loader;

.. note::

    Most of the examples in this document show behat being run via ``php behat.phar``.
    However, if you install via Composer, you have the option of running via ``bin/behat``
    instead.  To make this possible, add the following into your `composer.json` before
    installing or updating vendors:

    .. code-block:: js

        "config": {
            "bin-dir": "bin/"
        },

    This will make the ``behat`` command available from the ``bin`` directory.  If you run
    behat this way, you do not need to download ``behat.phar``.

Usage
-----

After installing the extension, there are 2 usage options available:

1. If you're using PHP 5.4+, you can simply use the
   ``Behat\Symfony2Extension\Context\KernelDictionary`` trait inside your
   ``FeatureContext`` or any of its subcontexts. This trait will provide the
   ``getKernel()`` and ``getContainer()`` methods for you.

2. Implementing ``Behat\Symfony2Extension\Context\KernelAwareContext`` with
   your context or its subcontexts. This will give you more customization options.
   Also, you can use this mechanism on multiple contexts avoiding the need to call
   parent contexts from subcontexts when the only thing you need is a kernel instance.

There's a common thing between those 2 methods. In each of those, target context
will implement the ``setKernel(KernelInterface $kernel)`` method. This method would be
automatically called **immediately after** each context creation before each scenario.
After context constructor, but before any instance hook or definition call.

.. note::

    The application kernel will be automatically rebooted between scenarios, so your
    scenarios would have almost absolutely isolated state.

Initialize Bundle Suite
~~~~~~~~~~~~~~~~~~~~~~~

In order to start with your feature suite for specific bundle, execute:

.. code-block:: bash

    $ php behat.phar --init --suite=my_suite

Run Bundle Suite
~~~~~~~~~~~~~~~~

In order to run the feature suite for a specific bundle, execute:

.. code-block:: bash

    $ php behat.phar -s my_suite

You can also use the bundle name to limit the features being run when using the default
convention for features files (putting them in the ``Features`` folder of the bundle):

.. code-block:: bash

    $ php behat.phar "@AcmeDemoBundle"

This can also be used to run specific features in the bundle:

.. code-block:: bash

    $ php behat.phar "@AcmeDemoBundle/registration.feature"
    $ php behat.phar src/Acme/DemoBundle/Features/registration.feature

``symfony2`` Mink Session
~~~~~~~~~~~~~~~~~~~~~~~~~

Symfony2Extension comes bundled with a custom ``symfony2`` session (driver) for Mink,
which is enabled by default when the MinkExtension and the MinkBrowserKitDriver are
available. In order to use it you should download/install/activate MinkExtension and
BrowserKit driver for Mink:

.. code-block:: js

    {
        "require": {
            ...

            "behat/symfony2-extension":      "~2.0@dev",
            "behat/mink-extension":          "~2.0@dev",
            "behat/mink-browserkit-driver":  "~1.1@dev"
        }
    }

The new Mink driver will be enabled automatically.

.. code-block:: yaml

    default:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension: ~
            Behat\MinkExtension\Extension: ~

Also, you can make the ``symfony2`` session the default one by setting ``default_session``
option in MinkExtension:

.. code-block:: yaml

    default:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension: ~
            Behat\MinkExtension\Extension:
                default_session: 'symfony2'

.. caution::

    The KernelDriver of the symfony2 session requires using a Symfony environment where
    the test mode of the FrameworkBundle is enabled. It uses the ``test`` environment by
    default, for which it is the case in the Symfony2 Standard Edition.

.. note::

    If you use the MinkExtension but don't want to enable the symfony2 session,
    you can disable it explicitly:

    .. code-block:: yaml

        default:
            # ...
            extensions:
                Behat\Symfony2Extension\Extension:
                    mink_driver: false
                Behat\MinkExtension\Extension: ~

Configuration
-------------

Symfony2Extension comes with a flexible configuration system, that gives you the ability to
configure Symfony2 kernel inside Behat to fulfil all your needs.

* ``kernel`` - specifies options to instantiate the kernel:

  - ``bootstrap`` - defines an autoloading/bootstraping file to autoload
    all the required classes to instantiate the kernel. It can be an absolute path
    or a path relative to the Behat configuration file. Defaults to ``app/autoload.php``.
  - ``path`` - defines the path to the kernel class file in order to instantiate it. It
    can be an absolute path or a path relative to the Behat configuration file. Defaults
    to ``app/AppKernel.php``.
  - ``class`` - defines the name of the kernel class. Defaults to ``AppKernel``.
  - ``env`` - defines the environment in which kernel should be instantiated and used
    inside suite. Defaults to ``test``.
  - ``debug`` - defines whether kernel should be instantiated with ``debug`` option
    set to true. Defaults to ``true``

* ``context`` - specifies options, used to guess the context class:

  - ``path_suffix`` - suffix from bundle directory for features. Defaults to
    ``Features\Context\FeatureContext``.
  - ``class_suffix`` - suffix from bundle classname for context class. Defaults to
    ``Features``.

* ``mink_driver`` - if set to true - extension will load the ``symfony2`` session
  for Mink.
