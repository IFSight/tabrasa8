// The google translate init needs to be outside of the behavior.
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'ar,zh-CN,zh-TW,fr,ht,pt,ru,es,vi,en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, 'google_translate_element');
}