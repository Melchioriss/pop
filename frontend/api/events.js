import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/events';
    }

    getList () {
        return this.axios.get(this.path());
    }

    get(uuid) {
        return this.axios.get(this.path(uuid));
    }

    create (event) {
        return this.axios.post(this.path(), event);
    }

    update (event) {
        return this.axios.put(this.path(event.uuid), event);
    }

    delete (event) {
        return this.axios.delete(this.path(event.uuid));
    }

    generatePickers (event) {
        return this.axios.post('/api/execute/generate_pickers', {
            eventUuid: event.uuid,
        });
    }

    getPotentialParticipants (event) {
        return this.axios.get(this.path(event.uuid, 'potential_participants'));
    }

    addParticipant (event, participantUuid, steamId) {
        return this.axios.post(this.path(event.uuid, 'participants'), {
            steamId,
            participantUuid,
        });
    }

    importPlaystats (event) {
        return this.axios.get('/api/execute/import_playing_states', {params: {eventUuid: event.uuid}});
    }
}
