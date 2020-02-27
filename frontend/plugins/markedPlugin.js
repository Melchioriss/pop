import marked from 'marked';
import DomPurify from 'dompurify';

const markedPlugin = {
    install(Vue, options) {
        Vue.prototype.$getMarkedContent = function (source) {
            return DomPurify.sanitize(marked(source, {breaks: true}));
        }
    }
};

export default  markedPlugin;