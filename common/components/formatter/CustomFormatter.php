<?php
namespace common\components\formatter;

use Yii;
use yii\i18n\Formatter;
use NumberFormatter;
use common\models\User;
use common\models\Location;
use common\models\UserLocation;
use yii\web\ForbiddenHttpException;

class CustomFormatter extends Formatter
{
    private $_intlLoaded = false;

    public function asBalance($value, $currency = null, $options = [], $textOptions = [])
    {

        if ($value === null) {
            return $this->nullDisplay;
        }
        $value = $this->normalizeNumericValue($value);
        if($value <= 0.09 && $value >= -0.09  ) {
            $value = 0.00;
        }
        $this->_intlLoaded = extension_loaded('intl');
        if ($this->_intlLoaded) {
            $currency = $currency ?: $this->currencyCode;
            if ($currency && !isset($textOptions[NumberFormatter::CURRENCY_CODE])) {
                $textOptions[NumberFormatter::CURRENCY_CODE] = $currency;
            }
            $formatter = $this->createNumberFormatter(NumberFormatter::CURRENCY, null, $options, $textOptions);
            if ($currency === null) {
                $result = $formatter->format($value);
            } else {
                $result = $formatter->formatCurrency($value, $currency);
            }
            if ($result === false) {
                throw new InvalidArgumentException('Formatting currency value failed: ' . $formatter->getErrorCode() . ' ' . $formatter->getErrorMessage());
            }

            return $result;
        }

        if ($currency === null) {
            if ($this->currencyCode === null) {
                throw new InvalidConfigException('The default currency code for the formatter is not defined and the php intl extension is not installed which could take the default currency from the locale.');
            }
            $currency = $this->currencyCode;
        }

        return $currency . ' ' . $this->asDecimal($value, 2, $options, $textOptions);
    }
    
}
