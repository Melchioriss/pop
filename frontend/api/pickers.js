import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/pickers';
    }

    replaceUser (picker, userId) {
        return this.axios.put(this.path(picker.uuid, 'user'), {userId});
    }

    makePick (picker, pick) {
        return this.axios.post(this.path(picker.uuid, 'picks'), {pickUuid: pick.uuid, type: pick.type, gameId: pick.game.id});
    }

    addComment (picker, comment) {
        return this.axios.post(this.path(picker.uuid, 'comments'), {
            text: comment.text,
            referencedPickUuid: comment.referencedPick,
            gameReferenceType: comment.gameReferenceType,
            commentUuid: comment.uuid
        });
    }
}
