import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/comments';
    }

    update (comment) {
        return this.axios.put(this.path(comment.uuid), {text: comment.text});
    }
}
