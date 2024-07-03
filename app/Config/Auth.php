<?php

namespace Config;

use Myth\Auth\Config\Auth as MythAuthConfig;

class Auth extends MythAuthConfig
{
    /**
     * --------------------------------------------------------------------
     * Require Confirmation Registration via Email
     * --------------------------------------------------------------------
     *
     * When enabled, every registered user will receive an email message
     * with an activation link to confirm the account.
     *
     * @var string|null Name of the ActivatorInterface class
     */
    public $requireActivation = null;
    // public $requireActivation = 'Myth\Auth\Authentication\Activators\EmailActivator';

    /**
     * --------------------------------------------------------------------
     * Allow Password Reset via Email
     * --------------------------------------------------------------------
     *
     * When enabled, users will have the option to reset their password
     * via the specified Resetter. Default setting is email.
     *
     * @var string|null Name of the ResetterInterface class
     */
    public $activeResetter = null;
    // public $activeResetter = 'Myth\Auth\Authentication\Resetters\EmailResetter';

    /**
     * --------------------------------------------------------------------
     * Views used by Auth Controllers
     * --------------------------------------------------------------------
     *
     * @var array
     */
    public $views = [
        'login'           => '\App\Views\admin\login',
        // 'login'           => 'Myth\Auth\Views\login',
        'register'        => 'Myth\Auth\Views\register',
        'forgot'          => 'Myth\Auth\Views\forgot',
        'reset'           => 'Myth\Auth\Views\reset',
        'emailForgot'     => 'Myth\Auth\Views\emails\forgot',
        'emailActivation' => 'Myth\Auth\Views\emails\activation',
    ];
}
