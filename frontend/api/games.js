import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/games';
    }

    getList (q, page) {
        return this.axios.get(this.path(), {params: {q, page, perPage: 5}});
    }

    import () {
        return this.axios.get('/api/execute/import_games');
    }
}
