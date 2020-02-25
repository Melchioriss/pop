import moment from 'moment';

const momentPlugin = {
    install(Vue, options) {
        Vue.prototype.$getExactDate = function(datetime) {
            if (!datetime)
                return 'never';

            return moment(datetime).format('LL');
        };
        Vue.prototype.$getExactTime = function(datetime) {
            if (!datetime)
                return 'never';

            return moment(datetime).format('LT');
        };
        Vue.prototype.$getExactDatetime = function(datetime) {
            if (!datetime)
                return 'never';

            return moment(datetime).format('LLL');
        };
        Vue.prototype.$getRelativeDate = function (datetime) {
            if (!datetime)
                return 'never';

            return moment(datetime).fromNow();
        };
        Vue.prototype.$getHtmlFormat = function (datetime) {
            if (!datetime)
                return '';

            return moment(datetime).format('YYYY-MM-DD');
        };
        Vue.prototype.$getDateNow = function () {
            return moment().format();
        }
    }
};

export default momentPlugin;