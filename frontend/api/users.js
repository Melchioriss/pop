import BaseApi from './baseApi'

export default new class extends BaseApi {
    constructor (props) {
        super(props)
        this.url = '/api/users';
    }

    getList(query = null) {
        return this.axios.get(this.path(), {
            params: {query}
        });
    }

    setBlaeoName(user, blaeoName) {
        return this.axios.put(this.path(user.steamId, 'blaeoName'), {blaeoName});
    }

    setExtraRules(user, extraRules) {
        return this.axios.put(this.path(user.steamId, 'extraRules'), {extraRules});
    }

    activateUser(user) {
        return this.axios.put(this.path(user.steamId, 'activity'));
    }

    deactivateUser(user) {
        return this.axios.delete(this.path(user.steamId, 'activity'));
    }

    grantAdminRole(user) {
        return this.axios.put(this.path(user.steamId, 'admin'));
    }

    revokeAdminRole(user) {
        return this.axios.delete(this.path(user.steamId, 'admin'));
    }
}
