import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/picks';
    }

    changeStatus (pick, status) {
        return this.axios.put(this.path(pick.uuid, 'status'), {status});
    }
}
