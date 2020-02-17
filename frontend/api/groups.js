import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/groups';
    }

    getList () {
        return this.axios.get(this.path());
    }

    import (code) {
        return this.axios.get(`/api/execute/import_group/${code}`);
    }
}
