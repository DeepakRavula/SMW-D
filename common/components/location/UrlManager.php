<?php
namespace common\components\location;

defined('YII2_LOCALEURLS_TEST') || define('YII2_LOCALEURLS_TEST', false);

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Cookie;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\components\location\LocationChangedEvent;
use common\components\location\LocationChangedEventFrontend;
use yii\web\UrlNormalizerRedirectException;

/**
 * This class extends Yii's UrlManager and adds features to detect the location
 * from the URL or from browser settings transparently. It also can persist the
 * location in the user session and optionally in a cookie. It also adds the
 * location parameter to any created URL.
 */
class UrlManager extends \codemix\localeurls\UrlManager
{
    const EVENT_LOCATION_CHANGED = 'locationChanged';
    const EVENT_LOCATION_CHANGED_FRONTEND = 'frontendLocationChanged';

    /**
     * @var array list of available location codes. More specific patterns
     * should come first, e.g. 'en_us' before 'en'. This can also contain
     * mapping of <url_value> => <location>, e.g. 'english' => 'en'.
     */
    public $locations = [];

    /**
     * @var bool whether to enable locale URL specific features
     */
    public $enableLocaleUrls = true;

    /**
     * @var bool whether the default location should use an URL code like any
     * other configured location.
     *
     * By default this is `false`, so for URLs without a location code the
     * default location is assumed.  In addition any request to an URL that
     * contains the default location code will be redirected to the same URL
     * without a location code. So if the default location is `fr` and a user
     * requests `/fr/some/page` he gets redirected to `/some/page`. This way
     * the persistent location can be reset to the default location.
     *
     * If this is `true`, then an URL that does not contain any location code
     * will be redirected to the same URL with default location code. So if for
     * example the default location is `fr`, then any request to `/some/page`
     * will be redirected to `/fr/some/page`.
     *
     */
    public $enableDefaultLocationUrlCode = false;

    /**
     * @var bool whether to detect the app location from the HTTP headers (i.e.
     * browser settings).  Default is `true`.
     */
    public $enableLocationDetection = true;

    /**
     * @var bool whether to store the detected location in session and
     * (optionally) a cookie. If this is `true` (default) and a returning user
     * tries to access any URL without a location prefix, he'll be redirected
     * to the respective stored location URL (e.g. /some/page ->
     * /fr/some/page).
     */
    public $enableLocationPersistence = true;

    /**
     * @var bool whether to keep upper case location codes in URL. Default is
     * `false` wich will e.g.  redirect `de-AT` to `de-at`.
     */
    public $keepUppercaseLocationCode = false;

    /**
     * @var string the name of the session key that is used to store the
     * location. Default is '_location'.
     */
    public $locationSessionKey = '_location';

    /**
     * @var string the name of the location cookie. Default is '_location'.
     */
    public $locationCookieName = '_location';

    /**
     * @var int number of seconds how long the location information should be
     * stored in cookie, if `$enableLocationPersistence` is true. Set to
     * `false` to disable the location cookie completely.  Default is 30 days.
     */
    public $locationCookieDuration = 2592000;

    /**
     * @var array configuration options for the location cookie. Note that
     * `$locationCookieName` and `$locationCookeDuration` will override any
     * `name` and `expire` settings provided here.
     */
    public $locationCookieOptions = [];

    /**
     * @var array list of route and URL regex patterns to ignore during
     * location processing. The keys of the array are patterns for routes, the
     * values are patterns for URLs. Route patterns are checked during URL
     * creation. If a pattern matches, no location parameter will be added to
     * the created URL.  URL patterns are checked during processing incoming
     * requests. If a pattern matches, the location processing will be skipped
     * for that URL. Examples:
     *
     * ~~~php
     * [
     *     '#^site/(login|register)#' => '#^(login|register)#'
     *     '#^api/#' => '#^api/#',
     * ]
     * ~~~
     */
    public $ignoreLocationUrlPatterns = [];

    /**
     * @var string the location that was initially set in the application
     * configuration
     */
    protected $_defaultLocation;

    /**
     * @inheritdoc
     */
    public $enablePrettyUrl = true;

    /**
     * @var string if a parameter with this name is passed to any `createUrl()`
     * method, the created URL will use the location specified there. URLs
     * created this way can be used to switch to a different location. If no
     * such parameter is used, the currently detected application location is
     * used.
     */
    public $locationParam = 'location';

    /**
     * @var \common\components\location\Request
     */
    protected $_request;

    /**
     * @var bool whether locale URL was processed
     */
    protected $_processed = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->locations = ArrayHelper::map(Location::find()->notDeleted()->all(), 'slug', 'slug');
        if ($this->enableLocaleUrls && $this->locations) {
            if (!$this->enablePrettyUrl) {
                throw new InvalidConfigException('Locale URL support requires enablePrettyUrl to be set to true.');
            }
        }
        $this->_defaultLocation = Yii::$app->location ?? null;
        parent::init();
    }

    /**
     * @return string the `location` option that was initially set in the
     * application config file, before it was modified by this component.
     */
    public function getDefaultLocation()
    {
        return $this->_defaultLocation;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        if ($this->enableLocaleUrls && $this->locations) {
            $this->_request = $request;
            $process = true;
            if ($this->ignoreLocationUrlPatterns) {
                $pathInfo = $request->getPathInfo();
                foreach ($this->ignoreLocationUrlPatterns as $k => $pattern) {
                    if (preg_match($pattern, $pathInfo)) {
                        $message = "Ignore pattern '$pattern' matches '$pathInfo.' Skipping location processing.";
                        Yii::trace($message, __METHOD__);
                        $process = false;
                    }
                }
            }
            if ($process && !$this->_processed) {
                // Check if a normalizer wants to redirect
                $normalized = false;
                if (property_exists($this, 'normalizer') && $this->normalizer!==false) {
                    try {
                        parent::parseRequest($request);
                    } catch (UrlNormalizerRedirectException $e) {
                        $normalized = true;
                    }
                }
                $this->_processed = true;
                $this->processLocaleUrl($normalized);
            }
        }
        return parent::parseRequest($request);
    }

    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        if ($this->ignoreLocationUrlPatterns) {
            $params = (array) $params;
            $route = trim($params[0], '/');
            foreach ($this->ignoreLocationUrlPatterns as $pattern => $v) {
                if (preg_match($pattern, $route)) {
                    return parent::createUrl($params);
                }
            }
        }

        if ($this->enableLocaleUrls && $this->locations) {
            $params = (array) $params;

            $isLocationGiven = isset($params[$this->locationParam]);
            $location = $isLocationGiven ? $params[$this->locationParam] : Yii::$app->location;
            $isDefaultLocation = $location===$this->getDefaultLocation();

            if ($isLocationGiven) {
                unset($params[$this->locationParam]);
            }

            $url = parent::createUrl($params);

            if (
                // Only add location if it's not empty and ...
                $location!=='' && (

                    // ... it's not the default location or default location uses URL code ...
                    !$isDefaultLocation || $this->enableDefaultLocationUrlCode ||

                    // ... or if a location is explicitely given, but only if
                    // either persistence or detection is enabled.  This way a
                    // "reset URL" can be created for the default location.
                    $isLocationGiven && ($this->enableLocationPersistence || $this->enableLocationDetection)
                )
            ) {
                $key = array_search($location, $this->locations);
                if (is_string($key)) {
                    $location = $key;
                }
                if (!$this->keepUppercaseLocationCode) {
                    $location = strtolower($location);
                }

                // Calculate the position where the location code has to be inserted
                // depending on the showScriptName and baseUrl configuration:
                //
                //  - /foo/bar -> /de/foo/bar
                //  - /base/foo/bar -> /base/de/foo/bar
                //  - /index.php/foo/bar -> /index.php/de/foo/bar
                //  - /base/index.php/foo/bar -> /base/index.php/de/foo/bar
                //
                $prefix = $this->showScriptName ? $this->getScriptUrl() : $this->getBaseUrl();
                $insertPos = strlen($prefix);

                // Remove any trailing slashes for root URLs
                if ($this->suffix !== '/') {
                    if (count($params) === 1) {
                        // / -> ''
                        // /base/ -> /base
                        // /index.php/ -> /index.php
                        // /base/index.php/ -> /base/index.php
                        if ($url === $prefix . '/') {
                            $url = rtrim($url, '/');
                        }
                    } elseif (strncmp($url, $prefix . '/?', $insertPos + 2) === 0) {
                        // /?x=y -> ?x=y
                        // /base/?x=y -> /base?x=y
                        // /index.php/?x=y -> /index.php?x=y
                        // /base/index.php/?x=y -> /base/index.php?x=y
                        $url = substr_replace($url, '', $insertPos, 1);
                    }
                }

                // If we have an absolute URL the length of the host URL has to
                // be added:
                //
                //  - http://www.example.com
                //  - http://www.example.com?x=y
                //  - http://www.example.com/foo/bar
                //
                if (strpos($url, '://')!==false) {
                    // Host URL ends at first '/' or '?' after the schema
                    if (($pos = strpos($url, '/', 8))!==false || ($pos = strpos($url, '?', 8))!==false) {
                        $insertPos += $pos;
                    } else {
                        $insertPos += strlen($url);
                    }
                }
                if ($insertPos > 0) {
                    return substr_replace($url, '/' . $location, $insertPos, 0);
                } else {
                    return '/' . $location . $url;
                }
            } else {
                return $url;
            }
        } else {
            return parent::createUrl($params);
        }
    }

    /**
     * Checks for a location or locale parameter in the URL and rewrites the
     * pathInfo if found.  If no parameter is found it will try to detect the
     * location from persistent storage (session / cookie) or from browser
     * settings.
     *
     * @param bool $normalized whether a UrlNormalizer tried to redirect
     */
    protected function processLocaleUrl($normalized)
    {
        $pathInfo = $this->_request->getPathInfo();
        $parts = [];
        foreach ($this->locations as $k => $v) {
            $value = is_string($k) ? $k : $v;
            if (substr($value, -2)==='-*') {
                $lng = substr($value, 0, -2);
                $parts[] = "$lng\-[a-z]{2,3}";
                $parts[] = $lng;
            } else {
                $parts[] = $value;
            }
        }
        $pattern = implode('|', $parts);
        if (preg_match("#^($pattern)\b(/?)#i", $pathInfo, $m)) {
            $this->_request->setPathInfo(mb_substr($pathInfo, mb_strlen($m[1].$m[2])));
            $code = $m[1];
            if (isset($this->locations[$code])) {
                // Replace alias with location code
                $location = $this->locations[$code];
            } else {
                // lowercase location, uppercase country
                list($location, $country) = $this->matchCode($code);
                if ($country!==null) {
                    if ($code==="$location-$country" && !$this->keepUppercaseLocationCode) {
                        $this->redirectToLocation(strtolower($code));   // Redirect ll-CC to ll-cc
                    } else {
                        $location = "$location-$country";
                    }
                }
                if ($location===null) {
                    $location = $code;
                }
            }
            Yii::$app->location = $location;
            Yii::trace("Location code found in URL. Setting application location to '$location'.", __METHOD__);
            if ($this->enableLocationPersistence) {
                $this->persistLocation($location);
            }

            // "Reset" case: We called e.g. /fr/demo/page so the persisted location was set back to "fr".
            // Now we can redirect to the URL without location prefix, if default prefixes are disabled.
            $reset = !$this->enableDefaultLocationUrlCode && $location===$this->_defaultLocation;

            if ($reset || $normalized) {
                $this->redirectToLocation('');
            }
        } else {
            $location = null;
            if ($this->enableLocationPersistence) {
                $location = $this->loadPersistedLocation();
            }
            if ($location===null && $this->enableLocationDetection) {
                foreach ($this->_request->getAcceptableLocations() as $acceptable) {
                    list($location, $country) = $this->matchCode($acceptable);
                    if ($location!==null) {
                        $location = $country===null ? $location : "$location-$country";
                        Yii::trace("Detected browser location '$location'.", __METHOD__);
                        break;
                    }
                }
            }
            if ($location===null || $location===$this->_defaultLocation) {
                if (!$this->enableDefaultLocationUrlCode) {
                    return;
                } else {
                    $location = $this->_defaultLocation;
                }
            }
            // #35: Only redirect if a valid location was found
            if ($this->matchCode($location)===[null, null]) {
                return;
            }

            $key = array_search($location, $this->locations);
            if ($key && is_string($key)) {
                $location = $key;
            }
            if (!$this->keepUppercaseLocationCode) {
                $location = strtolower($location);
            }
            $this->redirectToLocation($location);
        }
    }

    /**
     * @param string $location the location code to persist in session and cookie
     */
    protected function persistLocation($location)
    {
        if ($this->hasEventHandlers(self::EVENT_LOCATION_CHANGED)) {
            $oldLocation = $this->loadPersistedLocation();
            if ($oldLocation !== $location) {
                Yii::trace("Triggering locationChanged event: $oldLocation -> $location", __METHOD__);
                $this->trigger(self::EVENT_LOCATION_CHANGED, new LocationChangedEvent([
                    'oldLocation' => $oldLocation,
                    'location' => $location,
                ]));
            }
        }
        if ($this->hasEventHandlers(self::EVENT_LOCATION_CHANGED_FRONTEND)) {
            $oldLocation = $this->loadPersistedLocation();
            if ($oldLocation !== $location) {
                Yii::trace("Triggering locationChanged event: $oldLocation -> $location", __METHOD__);
                $this->trigger(self::EVENT_LOCATION_CHANGED_FRONTEND, new LocationChangedEventFrontend([
                    'oldLocation' => $oldLocation,
                    'location' => $location,
                ]));
            }
        }
        Yii::$app->session[$this->locationSessionKey] = $location;
        Yii::trace("Persisting location '$location' in session.", __METHOD__);
        if ($this->locationCookieDuration) {
            $cookie = new Cookie(array_merge(
                ['httpOnly' => true],
                $this->locationCookieOptions,
                [
                    'name' => $this->locationCookieName,
                    'value' => $location,
                    'expire' => time() + (int) $this->locationCookieDuration,
                ]
            ));
            Yii::$app->getResponse()->getCookies()->add($cookie);
            Yii::trace("Persisting location '$location' in cookie.", __METHOD__);
        }
    }

    /**
     * @return string|null the persisted location code or null if none found
     */
    protected function loadPersistedLocation()
    {
        $location = Yii::$app->session->get($this->locationSessionKey);
        $location!==null && Yii::trace("Found persisted location '$location' in session.", __METHOD__);
        if ($location===null) {
            $location = $this->_request->getCookies()->getValue($this->locationCookieName);
            $location!==null && Yii::trace("Found persisted location '$location' in cookie.", __METHOD__);
        }
        return $location;
    }

    /**
     * Tests whether the given code matches any of the configured locations.
     *
     * The return value is an array of the form `[$location, $country]`, where
     * `$country` or both can be `null`.
     *
     * If the code is a single location code, and matches either
     *
     *  - an exact location as configured (ll)
     *  - a location with a country wildcard (ll-*)
     *
     * the code value will be returned as `$location`.
     *
     * If the code is of the form `ll-CC`, and matches either
     *
     *  - an exact location/country code as configured (ll-CC)
     *  - a location with a country wildcard (ll-*)
     *
     * `$country` well be set to the `CC` part of the configured location.
     * If only the location part matches a configured location, only `$location`
     * will be set to that location.
     *
     * @param string $code the code to match
     * @return array of `[$location, $country]` where `$country` or both can be
     * `null`
     */
    protected function matchCode($code)
    {
        $hasDash = strpos($code, '-') !== false;
        $lcCode = strtolower($code);
        $lcLocations = array_map('strtolower', $this->locations);

        if (($key = array_search($lcCode, $lcLocations)) === false) {
            if ($hasDash) {
                list($location, $country) = explode('-', $code, 2);
            } else {
                $location = $code;
                $country = null;
            }
            if (in_array($location . '-*', $this->locations)) {
                if ($hasDash) {
                    // TODO: Make wildcards work with script codes
                    // like `sr-Latn`
                    return [$location, strtoupper($country)];
                } else {
                    return [$location, null];
                }
            } elseif ($hasDash && in_array($location, $this->locations)) {
                return [$location, null];
            } else {
                return [null, null];
            }
        } else {
            $result = $this->locations[$key];
            return $hasDash ? explode('-', $result, 2) : [$result, null];
        }

        $location = $code;
        $country = null;
        $parts = explode('-', $code);
        if (count($parts)===2) {
            $location = $parts[0];
            $country = strtoupper($parts[1]);
        }

        if (in_array($code, $this->locations)) {
            return [$location, $country];
        } elseif (
            $country && in_array("$location-$country", $this->locations) ||
            in_array("$location-*", $this->locations)
        ) {
            return [$location, $country];
        } elseif (in_array($location, $this->locations)) {
            return [$location, null];
        } else {
            return [null, null];
        }
    }

    /**
     * Redirect to the current URL with given location code applied
     *
     * @param string $location the location code to add. Can also be empty to
     * not add any location code.
     * @throws \yii\base\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    protected function redirectToLocation($location)
    {
        try {
            $result = parent::parseRequest($this->_request);
        } catch (UrlNormalizerRedirectException $e) {
            if (is_array($e->url)) {
                $params = $e->url;
                $route = array_shift($params);
                $result = [$route, $params];
            } else {
                $result = [$e->url, []];
            }
        }
        if ($result === false) {
            throw new \yii\web\NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        list($route, $params) = $result;
        if ($location) {
            $params[$this->locationParam] = $location;
        }
        // See Yii Issues #8291 and #9161:
        $params = $params + $this->_request->getQueryParams();
        array_unshift($params, $route);
        $url = $this->createUrl($params);
        // Required to prevent double slashes on generated URLs
        if ($this->suffix==='/' && $route==='' && count($params)===1) {
            $url = rtrim($url, '/').'/';
        }
        // Prevent redirects to same URL which could happen in certain
        // UrlNormalizer / custom rule combinations
        if ($url === $this->_request->url) {
            return;
        }
        Yii::trace("Redirecting to $url.", __METHOD__);
        Yii::$app->getResponse()->redirect($url);
        if (YII2_LOCALEURLS_TEST) {
            // Response::redirect($url) above will call `Url::to()` internally.
            // So to really test for the same final redirect URL here, we need
            // to call Url::to(), too.
            throw new \yii\base\Exception(\yii\helpers\Url::to($url));
        } else {
            Yii::$app->end();
        }
    }
}
