var md = new MobileDetect(window.navigator.userAgent);
mobile = 'width=device-width,initial-scale=1.0';
if (md.mobile()) {
 $('head').prepend('<meta name="viewport" content="' + mobile + '" id="viewport">');
}
