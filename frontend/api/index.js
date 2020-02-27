import axios from 'axios';
import profile from './profile';
import content from './content'
import users from './users';
import events from './events';
import participants from './participants';
import pickers from './pickers';
import picks from './picks';
import groups from './groups';
import games from './games';
import comments from './comments';
import activity from './activity';


Object.assign(axios.defaults, {
    withCredentials: true,
    baseURL: '/'
});

export default {
    profile,
    content,
    users,
    events,
    participants,
    pickers,
    picks,
    groups,
    games,
    comments,
    activity
}
