import isEmpty from 'lodash/isEmpty'
import upperCase from 'lodash/upperCase'
import upperFirst from 'lodash/upperFirst'

class LaravelTranslationFormatter {
  constructor (options = {}) {
    this._locale = options.locale || 'de'
    this._caches = Object.create(null)
  }

  interpolate (message, values) {
    let fn = this._caches[message]
    if (!fn) {
      fn = this.compile(message)
      this._caches[message] = fn
    }
    return fn(values)
  }

  /**
   * Returns an equivalent of the Illuminate\Translation\Translator#choice method from Laravel.
   *
   * @param line
   * @returns {function(...[*]=)}
   */
  compile(line) {
    return (replace=null) => {
      if (!replace) replace = {}
      return this.makeReplacements(this.choose(line, replace['count']), replace)
    }
  }

  /**
   * JS port of the Illuminate\Translation\Translator#makeReplacements method from Laravel.
   *
   * Make the place-holder replacements on a line.
   *
   * @param line
   * @param replace
   * @returns {string}
   */
  makeReplacements(line, replace={}) {
    if(isEmpty(replace)) return line

    replace = this.sortReplacements(replace)

    let result = line
    replace.forEach(([key, value]) => {
      result = result
        .replace(':' + key, value)
        .replace(':' + upperCase(key), upperCase(value))
        .replace(':' + upperFirst(key), upperFirst(value))
    })

    return result
  }

  /**
   * JS port of the Illuminate\Translation\Translator#sortReplacements method from Laravel.
   *
   * Sort the replacements array.
   *
   * @param replace
   * @returns {[string, unknown][]}
   */
  sortReplacements(replace) {
    return Object.entries(replace)
      .sort(([keyA, valueA], [keyB, valueB]) => keyB.length - keyA.length )
  }

  /**
   * JS port of the Illuminate\Translation\MessageSelector#choose method from Laravel.
   *
   * Select a proper translation string based on the given number.
   *
   * @param line
   * @param number
   * @return string
   */
  choose(line, number) {
    if (number === null || number === undefined) return line

    const segments = line.split('|')

    const value = this.extract(segments, number)
    if (typeof value === 'string') {
      return value.trim();
    }

    const strippedSegments = this.stripConditions(segments);

    const pluralIndex = this.getPluralIndex(this._locale, number);

    if (strippedSegments.length === 1 || pluralIndex >= strippedSegments.length) {
      return strippedSegments[0];
    }

    return strippedSegments[pluralIndex];
  }

  /**
   * JS port of the Illuminate\Translation\MessageSelector#extract method from Laravel.
   *
   * Extract a translation string using inline conditions.
   *
   * @param segments
   * @param number
   * @returns {*}
   */
  extract(segments, number) {
    let result = undefined
    segments.forEach(part =>  {
      if (result) return
      result = this.extractFromString(part, number)
    })
    return result
  }

  /**
   * JS port of the Illuminate\Translation\MessageSelector#extractFromString method from Laravel.
   *
   * Get the translation string if the condition matches.
   *
   * @param part
   * @param number
   * @returns {string|null|any}
   */
  extractFromString(part, number) {
    const matches = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)

    if (matches === null || matches.length !== 3) {
      return null
    }

    const condition = matches[1]
    const value = matches[2]

    if (condition.includes(',')) {
      const [from, to] = condition.split(',', 2)

      if (to === '*' && number >= parseInt(from)) {
        return value;
      } else if (from === '*' && number <= parseInt(to)) {
        return value;
      } else if (number >= parseInt(from) && number <= parseInt(to)) {
        return value;
      }
    }

    return parseInt(condition) === number ? value : null;
  }

  /**
   * JS port of the Illuminate\Translation\MessageSelector#stripConditions method from Laravel.
   *
   * Strip the inline conditions from each segment, just leaving the text.
   *
   * @param segments
   * @returns {*}
   */
  stripConditions(segments) {
    return segments.map(part => part.replace(/^[{[]([^[]{}]*)[}]]/, ''))
  }

  /**
   * JS port of the Illuminate\Translation\MessageSelector#getPluralIndex method from Laravel.
   *
   * Get the index to use for pluralization.
   *
   * The plural rules are derived from code of the Zend Framework (2010-09-25), which
   * is subject to the new BSD license (https://framework.zend.com/license)
   * Copyright (c) 2005-2010 - Zend Technologies USA Inc. (http://www.zend.com)
   *
   * @param locale
   * @param number
   * @returns {number}
   */
  getPluralIndex(locale, number) {
    switch (locale) {
      case 'az':
      case 'az_AZ':
      case 'bo':
      case 'bo_CN':
      case 'bo_IN':
      case 'dz':
      case 'dz_BT':
      case 'id':
      case 'id_ID':
      case 'ja':
      case 'ja_JP':
      case 'jv':
      case 'ka':
      case 'ka_GE':
      case 'km':
      case 'km_KH':
      case 'kn':
      case 'kn_IN':
      case 'ko':
      case 'ko_KR':
      case 'ms':
      case 'ms_MY':
      case 'th':
      case 'th_TH':
      case 'tr':
      case 'tr_CY':
      case 'tr_TR':
      case 'vi':
      case 'vi_VN':
      case 'zh':
      case 'zh_CN':
      case 'zh_HK':
      case 'zh_SG':
      case 'zh_TW':
        return 0;
      case 'af':
      case 'af_ZA':
      case 'bn':
      case 'bn_BD':
      case 'bn_IN':
      case 'bg':
      case 'bg_BG':
      case 'ca':
      case 'ca_AD':
      case 'ca_ES':
      case 'ca_FR':
      case 'ca_IT':
      case 'da':
      case 'da_DK':
      case 'de':
      case 'de_AT':
      case 'de_BE':
      case 'de_CH':
      case 'de_DE':
      case 'de_LI':
      case 'de_LU':
      case 'el':
      case 'el_CY':
      case 'el_GR':
      case 'en':
      case 'en_AG':
      case 'en_AU':
      case 'en_BW':
      case 'en_CA':
      case 'en_DK':
      case 'en_GB':
      case 'en_HK':
      case 'en_IE':
      case 'en_IN':
      case 'en_NG':
      case 'en_NZ':
      case 'en_PH':
      case 'en_SG':
      case 'en_US':
      case 'en_ZA':
      case 'en_ZM':
      case 'en_ZW':
      case 'eo':
      case 'eo_US':
      case 'es':
      case 'es_AR':
      case 'es_BO':
      case 'es_CL':
      case 'es_CO':
      case 'es_CR':
      case 'es_CU':
      case 'es_DO':
      case 'es_EC':
      case 'es_ES':
      case 'es_GT':
      case 'es_HN':
      case 'es_MX':
      case 'es_NI':
      case 'es_PA':
      case 'es_PE':
      case 'es_PR':
      case 'es_PY':
      case 'es_SV':
      case 'es_US':
      case 'es_UY':
      case 'es_VE':
      case 'et':
      case 'et_EE':
      case 'eu':
      case 'eu_ES':
      case 'eu_FR':
      case 'fa':
      case 'fa_IR':
      case 'fi':
      case 'fi_FI':
      case 'fo':
      case 'fo_FO':
      case 'fur':
      case 'fur_IT':
      case 'fy':
      case 'fy_DE':
      case 'fy_NL':
      case 'gl':
      case 'gl_ES':
      case 'gu':
      case 'gu_IN':
      case 'ha':
      case 'ha_NG':
      case 'he':
      case 'he_IL':
      case 'hu':
      case 'hu_HU':
      case 'is':
      case 'is_IS':
      case 'it':
      case 'it_CH':
      case 'it_IT':
      case 'ku':
      case 'ku_TR':
      case 'lb':
      case 'lb_LU':
      case 'ml':
      case 'ml_IN':
      case 'mn':
      case 'mn_MN':
      case 'mr':
      case 'mr_IN':
      case 'nah':
      case 'nb':
      case 'nb_NO':
      case 'ne':
      case 'ne_NP':
      case 'nl':
      case 'nl_AW':
      case 'nl_BE':
      case 'nl_NL':
      case 'nn':
      case 'nn_NO':
      case 'no':
      case 'om':
      case 'om_ET':
      case 'om_KE':
      case 'or':
      case 'or_IN':
      case 'pa':
      case 'pa_IN':
      case 'pa_PK':
      case 'pap':
      case 'pap_AN':
      case 'pap_AW':
      case 'pap_CW':
      case 'ps':
      case 'ps_AF':
      case 'pt':
      case 'pt_BR':
      case 'pt_PT':
      case 'so':
      case 'so_DJ':
      case 'so_ET':
      case 'so_KE':
      case 'so_SO':
      case 'sq':
      case 'sq_AL':
      case 'sq_MK':
      case 'sv':
      case 'sv_FI':
      case 'sv_SE':
      case 'sw':
      case 'sw_KE':
      case 'sw_TZ':
      case 'ta':
      case 'ta_IN':
      case 'ta_LK':
      case 'te':
      case 'te_IN':
      case 'tk':
      case 'tk_TM':
      case 'ur':
      case 'ur_IN':
      case 'ur_PK':
      case 'zu':
      case 'zu_ZA':
        return (number === 1) ? 0 : 1;
      case 'am':
      case 'am_ET':
      case 'bh':
      case 'fil':
      case 'fil_PH':
      case 'fr':
      case 'fr_BE':
      case 'fr_CA':
      case 'fr_CH':
      case 'fr_FR':
      case 'fr_LU':
      case 'gun':
      case 'hi':
      case 'hi_IN':
      case 'hy':
      case 'hy_AM':
      case 'ln':
      case 'ln_CD':
      case 'mg':
      case 'mg_MG':
      case 'nso':
      case 'nso_ZA':
      case 'ti':
      case 'ti_ER':
      case 'ti_ET':
      case 'wa':
      case 'wa_BE':
      case 'xbr':
        return ((number === 0) || (number === 1)) ? 0 : 1;
      case 'be':
      case 'be_BY':
      case 'bs':
      case 'bs_BA':
      case 'hr':
      case 'hr_HR':
      case 'ru':
      case 'ru_RU':
      case 'ru_UA':
      case 'sr':
      case 'sr_ME':
      case 'sr_RS':
      case 'uk':
      case 'uk_UA':
        return ((number % 10 === 1) && (number % 100 !== 11)) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 10) || (number % 100 >= 20))) ? 1 : 2);
      case 'cs':
      case 'cs_CZ':
      case 'sk':
      case 'sk_SK':
        return (number === 1) ? 0 : (((number >= 2) && (number <= 4)) ? 1 : 2);
      case 'ga':
      case 'ga_IE':
        return (number === 1) ? 0 : ((number === 2) ? 1 : 2);
      case 'lt':
      case 'lt_LT':
        return ((number % 10 === 1) && (number % 100 !== 11)) ? 0 : (((number % 10 >= 2) && ((number % 100 < 10) || (number % 100 >= 20))) ? 1 : 2);
      case 'sl':
      case 'sl_SI':
        return (number % 100 === 1) ? 0 : ((number % 100 === 2) ? 1 : (((number % 100 === 3) || (number % 100 === 4)) ? 2 : 3));
      case 'mk':
      case 'mk_MK':
        return (number % 10 === 1) ? 0 : 1;
      case 'mt':
      case 'mt_MT':
        return (number === 1) ? 0 : (((number === 0) || ((number % 100 > 1) && (number % 100 < 11))) ? 1 : (((number % 100 > 10) && (number % 100 < 20)) ? 2 : 3));
      case 'lv':
      case 'lv_LV':
        return (number === 0) ? 0 : (((number % 10 === 1) && (number % 100 !== 11)) ? 1 : 2);
      case 'pl':
      case 'pl_PL':
        return (number === 1) ? 0 : (((number % 10 >= 2) && (number % 10 <= 4) && ((number % 100 < 12) || (number % 100 > 14))) ? 1 : 2);
      case 'cy':
      case 'cy_GB':
        return (number === 1) ? 0 : ((number === 2) ? 1 : (((number === 8) || (number === 11)) ? 2 : 3));
      case 'ro':
      case 'ro_RO':
        return (number === 1) ? 0 : (((number === 0) || ((number % 100 > 0) && (number % 100 < 20))) ? 1 : 2);
      case 'ar':
      case 'ar_AE':
      case 'ar_BH':
      case 'ar_DZ':
      case 'ar_EG':
      case 'ar_IN':
      case 'ar_IQ':
      case 'ar_JO':
      case 'ar_KW':
      case 'ar_LB':
      case 'ar_LY':
      case 'ar_MA':
      case 'ar_OM':
      case 'ar_QA':
      case 'ar_SA':
      case 'ar_SD':
      case 'ar_SS':
      case 'ar_SY':
      case 'ar_TN':
      case 'ar_YE':
        return (number === 0) ? 0 : ((number === 1) ? 1 : ((number === 2) ? 2 : (((number % 100 >= 3) && (number % 100 <= 10)) ? 3 : (((number % 100 >= 11) && (number % 100 <= 99)) ? 4 : 5))));
      default:
        return 0;
    }
  }

}

const formatters = {}

const messageCompiler = (
  message,
  { locale, key, onError }
) => {
  if (typeof message === 'string') {
    /**
     * You can tune your message compiler performance more with your cache strategy or also memoization at here
     */
    const formatter = locale in formatters ? formatters[locale] : new LaravelTranslationFormatter({ locale });
    return (ctx) => {
      return formatter.interpolate(message, ctx.values)
    }
  } else {
    /**
     * for AST.
     * If you would like to support it,
     * You need to transform locale messages such as `json`, `yaml`, etc. with the bundle plugin.
     */
    onError && onError(new Error('not support for AST'))
    return () => key
  }
}

export default messageCompiler;
