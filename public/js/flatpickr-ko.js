/**
 * Filament DateTimePicker 한국어 패치
 *
 * Filament v4의 DateTimePicker는 Day.js를 사용하며 번들에 ko locale이 없어
 * window.dayjs 할당 시점을 가로채 months() / weekdaysShort() 를 한국어로 교체한다.
 */
(function () {
    var KO_MONTHS = ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'];
    var KO_WEEKDAYS_SHORT = ['일','월','화','수','목','금','토'];

    function patch(dayjs) {
        dayjs.months      = function () { return KO_MONTHS.slice(); };
        dayjs.monthsShort = function () { return KO_MONTHS.slice(); };
        dayjs.weekdaysShort = function () { return KO_WEEKDAYS_SHORT.slice(); };
    }

    var _dayjs;
    Object.defineProperty(window, 'dayjs', {
        configurable: true,
        enumerable:   true,
        get: function () { return _dayjs; },
        set: function (val) {
            _dayjs = val;
            if (val) { patch(val); }
        }
    });
})();
