<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdcb8af2cc989eb38b08d98627b00f7cf
{
    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'Hybrid' => 
            array (
                0 => __DIR__ . '/../..' . '/hybridauth',
            ),
        ),
    );

    public static $classMap = array (
        'BaseFacebook' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/Facebook/base_facebook.php',
        'Facebook' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/Facebook/facebook.php',
        'FacebookApiException' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/Facebook/base_facebook.php',
        'Hybrid_Auth' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Auth.php',
        'Hybrid_Endpoint' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Endpoint.php',
        'Hybrid_Error' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Error.php',
        'Hybrid_Exception' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Exception.php',
        'Hybrid_Logger' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Logger.php',
        'Hybrid_Provider_Adapter' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Provider_Adapter.php',
        'Hybrid_Provider_Model' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Provider_Model.php',
        'Hybrid_Provider_Model_OAuth1' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Provider_Model_OAuth1.php',
        'Hybrid_Provider_Model_OAuth2' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Provider_Model_OAuth2.php',
        'Hybrid_Provider_Model_OpenID' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Provider_Model_OpenID.php',
        'Hybrid_Providers_AOL' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/AOL.php',
        'Hybrid_Providers_Facebook' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Facebook.php',
        'Hybrid_Providers_Foursquare' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Foursquare.php',
        'Hybrid_Providers_Google' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Google.php',
        'Hybrid_Providers_LinkedIn' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/LinkedIn.php',
        'Hybrid_Providers_Live' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Live.php',
        'Hybrid_Providers_OpenID' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/OpenID.php',
        'Hybrid_Providers_Twitter' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Twitter.php',
        'Hybrid_Providers_Yahoo' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Providers/Yahoo.php',
        'Hybrid_Storage' => __DIR__ . '/../..' . '/hybridauth/Hybrid/Storage.php',
        'Hybrid_Storage_Interface' => __DIR__ . '/../..' . '/hybridauth/Hybrid/StorageInterface.php',
        'Hybrid_User' => __DIR__ . '/../..' . '/hybridauth/Hybrid/User.php',
        'Hybrid_User_Activity' => __DIR__ . '/../..' . '/hybridauth/Hybrid/User_Activity.php',
        'Hybrid_User_Contact' => __DIR__ . '/../..' . '/hybridauth/Hybrid/User_Contact.php',
        'Hybrid_User_Profile' => __DIR__ . '/../..' . '/hybridauth/Hybrid/User_Profile.php',
        'LightOpenID' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OpenID/LightOpenID.php',
        'LinkedIn' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/LinkedIn/LinkedIn.php',
        'LinkedInException' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/LinkedIn/LinkedIn.php',
        'OAuth1Client' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth1Client.php',
        'OAuth2Client' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth2Client.php',
        'OAuthConsumer' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthDataStore' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthException' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthRequest' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthServer' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_HMAC_SHA1' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_PLAINTEXT' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_RSA_SHA1' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthToken' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthUtil' => __DIR__ . '/../..' . '/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitdcb8af2cc989eb38b08d98627b00f7cf::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitdcb8af2cc989eb38b08d98627b00f7cf::$classMap;

        }, null, ClassLoader::class);
    }
}
