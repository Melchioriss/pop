import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/blocks';
    }

    getBlock (code) {
        return this.axios.get(this.path(code));
    }

    setBlock (code, content) {
        return this.axios.put(this.path(code), {content});
    }
}
