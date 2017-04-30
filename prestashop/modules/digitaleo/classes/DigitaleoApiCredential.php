<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author	    Digitaleo
 *  @copyright 2016 Digitaleo
 *  @license   All Rights Reserved
 */

/**
 * Class Credential
 */
class Credential
{
    /**
     * URL du serveur d'autorisation
     *
     * @var string
     */
    public $url;

    /**
     * Grant Type
     *
     * @var string
     */
    public $grantType;

    /**
     * Client Id
     *
     * @var string
     */
    public $clientId;

    /**
     * Client Secret
     *
     * @var string
     */
    public $clientSecret;

    /**
     * Login
     *
     * @var string
     */
    public $username;

    /**
     * Password
     *
     * @var string
     */
    public $password;

    /**
     * Token
     *
     * @var string
     */
    public $token;

    /**
     * Refresh token
     *
     * @var string
     */
    public $refreshToken;

    /**
     * Facebook token
     *
     * @var string
     */
    public $facebookToken;
}
