import BaseApi from './baseApi';

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/participants';
    }

    updateGroupWins (participant, groupWins) {
        return this.axios.put(this.path(participant.uuid, 'groupWins'), {groupWins})
    }

    updateBlaeoGames (participant, blaeoGames) {
        return this.axios.put(this.path(participant.uuid, 'blaeoGames'), {blaeoGames})
    }

    updateBlaeoPoints (participant, blaeoPoints) {
        return this.axios.put(this.path(participant.uuid, 'blaeoPoints'), {blaeoPoints})
    }

    updateExtraRules (participant, extraRules) {
        return this.axios.put(this.path(participant.uuid, 'extraRules'), {extraRules})
    }

    addPicker(participant, picker) {
        return this.axios.post(this.path(participant.uuid, 'pickers', picker.uuid), {userId: picker.user, type: picker.type});
    }

    remove(participantUuid) {
        return this.axios.delete(this.path(participantUuid));
    }
}
