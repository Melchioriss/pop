import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/activity';
    }

    getList (page) {
        return this.axios.get(this.path(), {params: {page, perPage: 100}});
    }
}
