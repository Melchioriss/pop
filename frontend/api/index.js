import axios from 'axios';
import profile from './profile';
import users from './users';
import events from './events';
import participants from './participants';
import pickers from './pickers';
import picks from './picks';
import groups from './groups';
import games from './games';


Object.assign(axios.defaults, {
    withCredentials: true,
    baseURL: '/'
});

export default {
    profile,
    users,
    events,
    participants,
    pickers,
    picks,
    groups,
    games,
}
