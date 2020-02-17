const debouncePlugin = {
    install(Vue, options) {
        Vue.prototype.$debounce = function (f, ms) {
            let _this = this;

            return function (...args) {
                const onComplete = () => {
                    f.apply(this, args);
                    _this.timer = null;
                };

                if (_this.timer) {
                    clearTimeout(_this.timer);
                }

                _this.timer = setTimeout(onComplete, ms);
            };
        }
    },
    timer: null
};

export default debouncePlugin;