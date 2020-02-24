import Vue from 'vue';
import Vuex from 'vuex';

import api from '../api/index';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        BLAEO_USER_BASE_LINK: 'https://www.backlog-assassins.net/users/',
        MAJOR: 20,
        MINOR: 10,

        SHORT: 10,
        MEDIUM: 20,
        LONG: 30,
        VERY_LONG: 40,

        NOT_PLAYED: 0,
        UNFINISHED: 10,
        BEATEN: 20,
        COMPLETED: 30,
        ABANDONED: 40,

        MAIN_PAGE_CONTENT_CODE: 'main_page',

        users: {},
        loggedUser: {},
        groups: {},
        events: {},
        participants: {},
        pickers: {},
        picks: {},
        games: {},
        blocks: {},
    },
    getters: {
        statusTexts: (state) => ({
            [state.NOT_PLAYED]: 'Not Played',
            [state.UNFINISHED]: 'Unfinished',
            [state.BEATEN]: 'Beaten',
            [state.COMPLETED]: 'Completed',
            [state.ABANDONED]: 'Abandoned'
        }),

        getMainGroup: (state) => state.groups[0],

        getSortedUsers: (state) => {
            let sortedUsers = Object.values(state.users);

            sortedUsers.sort((a, b) => {
                if (a.profileName.toLowerCase() === b.profileName.toLowerCase()) return 0;
                return a.profileName.toLowerCase() > b.profileName.toLowerCase();
            });

            return sortedUsers;
        },

        getUser: (state) => (userId) => state.users[userId] ? state.users[userId] : null,

        loggedUserIsAdmin: (state) => {
            return state.loggedUser? state.loggedUser.admin : false;
        },

        loggedUserSteamId: (state) => {
            return state.loggedUser? state.loggedUser.steamId : -1;
        },

        getSortedParticipants: (state) => {
            let sortedParticipants = Object.values(state.participants);
            sortedParticipants.sort((a, b) => {
                let userA = state.users[ a.user ];
                let userB = state.users[ b.user ];

                if (userA.profileName.toLowerCase() === userB.profileName.toLowerCase()) return 0;
                return userA.profileName.toLowerCase() > userB.profileName.toLowerCase();
            });
            return sortedParticipants;
        },

        getPicker: (state) => (pickerUuid) => state.pickers[pickerUuid],

        getPick: (state) => (pickUuid) => state.picks[pickUuid],

        getGame: (state) => (gameId) => state.games[gameId],

        getMainPageContent: (state) => state.blocks[ state.MAIN_PAGE_CONTENT_CODE ],
    },
    actions: {
        loadGroups: function({commit}) {

            return api.groups.getList()
                .then(({data: groupsData}) => {
                    commit('setGroups', groupsData.data);
                });
        },

        loadProfile: function({commit}) {
            return api.profile.logged()
                .then(({data: profileResult}) => {

                    commit('setLoggedUser', profileResult.data);
                });
        },

        loadUsers: function ({commit}) {
            return api.users.getList()
                .then(({data: usersResult}) => {
                    let users = {};

                    usersResult.data.forEach((user) => {
                        users[ user.steamId ] = user;
                    });

                    commit('setUsers', users);
                });
        },

        updateUserBlaeoLink: function ({commit, state}, {user, blaeoLink}) {
            return api.users.setBlaeoName(user, blaeoLink)
                .then(() => {
                    user.blaeoName = blaeoLink.replace(state.BLAEO_USER_BASE_LINK, '');
                    commit('setUser', user);
                })
        },

        updateUserExtraRules: function ({commit}, {user, extraRules}) {
            return api.users.setExtraRules(user, extraRules)
                .then(() => {
                    user.extraRules = extraRules;
                    commit('setUser', user);
                });
        },

        activateUser: function ({commit}, user) {
            return api.users.activateUser(user)
                .then(() => {
                    user.active = true;
                    commit('setUser', user);
                });
        },

        deactivateUser: function ({commit}, user) {
            return api.users.deactivateUser(user)
                .then(() => {
                    user.active = false;
                    commit('setUser', user);
                });
        },

        grantUserAdminRole: function ({commit}, user) {
            return api.users.grantAdminRole(user)
                .then(() => {
                    user.admin = true;
                    commit('setUser', user);
                })
        },

        revokeUserAdminRole: function ({commit}, user) {
            return api.users.revokeAdminRole(user)
                .then(() => {
                    user.admin = false;
                    commit('setUser', user);
                })
        },

        createEvent: function ({commit}, event) {

            return new Promise((resolve, reject) => {
                return api.events.create(event)
                    .then(() => resolve())
                    .catch(e => reject(e));
            })
        },

        updateEvent: function ({commit}, event) {

            return new Promise((resolve, reject) => {
                return api.events.update(event)
                    .then(() => resolve())
                    .catch(e => reject(e));
            })
        },

        loadEvents: function ({commit}) {

            return api.events.getList()
                .then(({data: eventsResult}) => {
                    let events = {};

                    eventsResult.data.forEach((event) => {
                        events[ event.uuid ] = event;
                    });

                    commit('setEvents', events);
                });
        },

        loadEvent: function ({commit}, eventUuid) {

            return new Promise((resolve, reject) => {
                return api.events.get(eventUuid)
                    .then(({data: eventResult}) => {
                        let event = eventResult.data;

                        let participants = {};
                        event.participants.forEach(participant => {
                            participants[ participant.uuid ] = participant;
                        });
                        delete event.participants;

                        let users = {};
                        event.users.forEach(user => {
                            users[user.steamId] = user;
                        });
                        delete event.users;

                        let pickers = {};
                        let picks = {};
                        let games = {};
                        Object.keys(participants).forEach(participantUuid => {
                            let participant = participants[participantUuid];
                            participant.picks = {};
                            let pickersNormalized = {};
                            participant.pickers.forEach(picker => {
                                pickers[picker.uuid] = picker;
                                pickersNormalized[picker.type] = picker.uuid;

                                participant.picks[picker.type] = {};

                                picker.picks.forEach(pick => {
                                    games[pick.game.id] = pick.game;
                                    pick.game = pick.game.id;

                                    picks[pick.uuid] = pick;
                                    participant.picks[picker.type][pick.type] = pick.uuid;
                                });
                                delete picker.picks;

                            });
                            participant.pickers = pickersNormalized;
                        });


                        commit('setGames', games);
                        commit('setPicks', picks);
                        commit('setPickers', pickers);
                        commit('setUsers', users);
                        commit('setEvents', {[event.uuid]: event});
                        commit('setParticipants', participants);

                        resolve();
                    })
                    .catch(e => reject(e));
            });
        },

        updateParticipantGroupWins: function ({commit}, {participant, groupWins}) {
            return api.participants.updateGroupWins(participant, groupWins)
                .then(() => {
                    participant.groupWins = groupWins;
                    commit('setParticipant', participant);
                })
        },

        updateParticipantBlaeoGames: function ({commit}, {participant, blaeoGames}) {
            return api.participants.updateBlaeoGames(participant, blaeoGames)
                .then(() => {
                    participant.blaeoGames = blaeoGames;
                    commit('setParticipant', participant)
                })
        },

        updateParticipantExtraRules: function ({commit}, {participant, extraRules}) {
            return api.participants.updateExtraRules(participant, extraRules)
                .then(() => {
                    participant.extraRules = extraRules;
                    commit('setParticipant', participant)
                })
        },

        generateEventPickers: function ({commit}, event) {
            return api.events.generatePickers(event);
        },

        replacePickerUser: function ({commit}, {picker, userId}) {
            return api.pickers.replaceUser(picker, userId)
                .then(() => {
                    picker.user = userId;
                    commit('setPicker', picker);
                });
        },

        addPicker: function ({commit}, {picker, participant}) {

            return api.participants.addPicker(participant, picker)
                .then(() => {
                    participant.pickers[picker.type] = picker.uuid;
                    participant.picks[picker.type] = [];

                    commit('setPicker', picker);
                    commit('setParticipant', participant);
                })
        },

        importGroup: function ({commit}, code) {
            return new Promise((resolve, reject) => {
                return api.groups.import(code)
                    .then(() => resolve())
                    .catch(e => reject(e));
            });
        },

        importGames: function () {
            return new Promise((resolve, reject) => {
                return api.games.import()
                    .then(() => resolve())
                    .catch(e => reject());
            })
        },

        findGames: function ({commit}, {query, page}) {
            return new Promise((resolve, reject) => {
                return api.games.getList(query, page)
                    .then(({data: gamesResult}) => {
                        let games = gamesResult.data;
                        let pagin = gamesResult.meta;
                        resolve({games, pagin});
                    })
                    .catch(e => reject(e));
            })
        },

        makePick: function ({commit, state}, {picker, pick, participantUuid}) {
            return api.pickers.makePick(picker, pick)
                .then(() => {

                    let participant = {...state.participants[participantUuid]};
                    let game = pick.game;
                    pick.game = game.id;

                    participant.picks[picker.type][pick.type] = pick.uuid;

                    commit('setGame', game);
                    commit('setParticipant', participant);
                    commit('setPick', pick);
                });
        },

        changePickGame: function ({commit, state}, {picker, pick, participantUuid}) {
            return api.picks.changeGame(pick)
                .then(() => {

                    if (pick.game.id.toString() !== state.picks[pick.uuid].game.toString())
                    {
                        pick.playedStatus = state.NOT_PLAYED;
                        pick.playingState = {
                            achievements: null,
                            playtime: null
                        };
                    }

                    let participant = {...state.participants[participantUuid]};
                    let game = pick.game;
                    pick.game = game.id;

                    participant.picks[picker.type][pick.type] = pick.uuid;

                    commit('setGame', game);
                    commit('setParticipant', participant);
                    commit('setPick', pick);
                });
        },

        changePickStatus: function ({commit}, {pick, status}) {

            return api.picks.changeStatus(pick, status)
                .then(() => {
                    pick.playedStatus = status;
                    commit('setPick', pick);
                });
        },

        loadEventPotentialParticipants: function ({commit}, event) {
            return new Promise((resolve, reject) => {
                return api.events.getPotentialParticipants(event)
                    .then(({data: participantsResult}) => {
                        resolve(participantsResult.data);
                    });
            });
        },

        addEventParticipant: function ({commit}, {event, participantUuid, steamId}) {
            return api.events.addParticipant(event, participantUuid, steamId);
        },

        importEventPlaystats: function ({commit}, {event}) {
            return api.events.importPlaystats(event);
        },

        addPickerComment: function ({commit}, {picker, comment}) {
            return api.pickers.addComment(picker, comment)
                .then(() => {
                    picker.comments.push(comment);
                    commit('setPicker', picker);
                });
        },

        loadMainPageContent: function({commit, state}) {
            return api.content.getBlock(state.MAIN_PAGE_CONTENT_CODE)
                .then(({data: contentResult}) => {
                    commit('setBlock', {code: state.MAIN_PAGE_CONTENT_CODE, content: contentResult.data.content});
                })
                .catch(e => console.log(e.response.data.errors.detail));
        },

        setMainPageContent: function ({commit, state}, content) {
            return api.content.setBlock(state.MAIN_PAGE_CONTENT_CODE, content)
                .then(() => {
                    commit('setBlock', {code: state.MAIN_PAGE_CONTENT_CODE, content})
                })
        }
    },
    mutations: {
        setUsers: (state, users) => state.users = users,

        setLoggedUser: (state, loggedUser) => state.loggedUser = loggedUser,

        setUser: (state, user) => Vue.set(state.users, user.steamId, user),

        setGroups: (state, groups) => state.groups = groups,

        setEvents: (state, events) => state.events = events,

        setParticipants: (state, participants) => state.participants = participants,

        setParticipant: (state, participant) => Vue.set(state.participants, participant.uuid, participant),

        setPickers: (state, pickers) => state.pickers = pickers,

        setPicker: (state, picker) => Vue.set(state.pickers, picker.uuid, picker),

        setPicks: (state, picks) => state.picks = picks,

        setGames: (state, games) => state.games = games,

        setGame: (state, game) => Vue.set(state.games, game.id, game),

        setPick: (state, pick) => Vue.set(state.picks, pick.uuid, pick),

        setBlock: (state, block) => Vue.set(state.blocks, block.code, block.content)
    }
});
