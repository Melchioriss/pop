import Vue from 'vue';
import Vuex from 'vuex';

import api from '../api/index';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        users: {},
        loggedUser: {},
        groups: {},
        events: {},
        participants: {},
        pickers: {},
        picks: {},
        games: {},
        comments: {},
        blocks: {},
        activity: {},
        lastVisit: '',
        notifications: {
            comments: [],
            repicks: []
        },

        rewardsMap: {},

        BLAEO_USER_BASE_LINK: 'https://www.backlog-assassins.net/users/',
        MAJOR: 20,
        MINOR: 10,

        SHORT: 10,
        MEDIUM: 20,
        LONG: 30,
        VERY_LONG: 40,

        NOT_PLAYED: 5,
        UNFINISHED: 10,
        BEATEN: 20,
        COMPLETED: 30,
        ABANDONED: 40,

        POINTS_THRESHOLD: 5,

        MAIN_PAGE_CONTENT_CODE: 'main_page',

        ACTIVITY_TYPES: {
            STATUS_CHANGE: 'PickPlayedStatusChanged',
            REVIEW_ADDED: 'ReviewAdded',
            MEMBER_ADDED: 'MemberAdded',
            MEMBER_REMOVED: 'MemberRemoved'
        },

        GAME_REFERENCE_TYPE: {
            REVIEW: 10,
            REPICK: 20
        }
    },
    getters: {
        statusTexts: (state) => ({
            [state.NOT_PLAYED]: 'Not Played',
            [state.UNFINISHED]: 'Unfinished',
            [state.BEATEN]: 'Beaten',
            [state.COMPLETED]: 'Completed',
            [state.ABANDONED]: 'Abandoned'
        }),

        rewardReasons: (state) => ({
            GAME_BEATEN: {
                [state.SHORT]: 100,
                [state.MEDIUM]: 200,
                [state.LONG]: 300,
                [state.VERY_LONG]: 400
            },
            GAME_COMPLETED: 500,
            BLAEO_POINTS: 600,
            ALL_PICKS_BEATEN: 700,
            GAME_BEATEN_UNI: 'beaten'
        }),

        rewardHints: (state, getters) => ({
            [ getters.rewardReasons.GAME_BEATEN[ state.SHORT ] ]: 'For finishing a short game',
            [ getters.rewardReasons.GAME_BEATEN[ state.MEDIUM ] ]: 'For finishing a medium game',
            [ getters.rewardReasons.GAME_BEATEN[ state.LONG ] ]: 'For finishing a long game',
            [ getters.rewardReasons.GAME_BEATEN[ state.VERY_LONG ] ]: 'For finishing a very long game',
            [ getters.rewardReasons.GAME_COMPLETED ]: 'For completing a game',
            [ getters.rewardReasons.BLAEO_POINTS ]: 'For playing games that fit BLAEO theme',
            [ getters.rewardReasons.ALL_PICKS_BEATEN ]: 'For finishing all picked games'
        }),

        pickTypes: (state) => ({
            [state.MAJOR]: [state.SHORT, state.MEDIUM, state.LONG, state.VERY_LONG],
            [state.MINOR]: [state.SHORT, state.MEDIUM, state.LONG]
        }),

        getMainGroup: (state) => state.groups[0],

        getSortedUsers: (state) => {
            let sortedUsers = Object.values(state.users);

            sortedUsers.sort((a, b) => {
                return a.profileName.toLowerCase().localeCompare(b.profileName.toLowerCase());
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

        getParticipantsSortedByName: (state) => {
            let sortedParticipants = Object.values(state.participants);
            sortedParticipants.sort((a, b) => {
                let userA = state.users[ a.user ];
                let userB = state.users[ b.user ];

                return userA.profileName.toLowerCase().localeCompare(userB.profileName.toLowerCase());
            });
            return sortedParticipants;
        },

        getParticipantsSortedByPoints: (state) => {

            let sortedParticipants = Object.values(state.participants);
            sortedParticipants.sort((a, b) => {
                // if points are equal we sort by name
                if (a.totalRewardValue === b.totalRewardValue)
                {
                    let userA = state.users[ a.user ];
                    let userB = state.users[ b.user ];

                    return userA.profileName.toLowerCase().localeCompare(userB.profileName.toLowerCase());
                }
                return a.totalRewardValue < b.totalRewardValue ? 1 : -1;
            });
            return sortedParticipants;
        },

        getPicker: (state) => (pickerUuid) => state.pickers[pickerUuid],

        getPick: (state) => (pickUuid) => state.picks[pickUuid],

        getGame: (state) => (gameId) => state.games[gameId],

        getComment: (state) => (commentUuid) => state.comments[commentUuid],

        getMainPageContent: (state) => state.blocks[ state.MAIN_PAGE_CONTENT_CODE ],

        getActivityByDate: (state) => {
            let activityByDate = {};

            Object.values(state.activity).forEach(activityItem => {
                let date = Vue.prototype.$getExactDate(activityItem.createdAt);
                if (!activityByDate[date])
                    activityByDate[date] = [];
                activityByDate[date].push(activityItem);
            });

            return activityByDate;
        },

        getCommentNotification: (state) => state.notifications.comments,

        getRepickNotification: (state) => state.notifications.repicks
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
                });
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
                });
        },

        revokeUserAdminRole: function ({commit}, user) {
            return api.users.revokeAdminRole(user)
                .then(() => {
                    user.admin = false;
                    commit('setUser', user);
                });
        },

        createEvent: function ({commit}, event) {
            return new Promise((resolve, reject) => {
                return api.events.create(event)
                    .then(() => resolve())
                    .catch(e => reject(e.response.data.errors.detail));
            });
        },

        updateEvent: function ({commit}, event) {
            return new Promise((resolve, reject) => {
                return api.events.update(event)
                    .then(() => resolve())
                    .catch(e => reject(e.response.data.errors.detail));
            });
        },

        deleteEvent: function ({commit}, event) {
            return new Promise((resolve, reject) => {
                return api.events.delete(event)
                    .then(() => resolve())
                    .catch(e => reject(e.response.data.errors.detail));
            });
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
                        let comments = {};
                        Object.keys(participants).forEach(participantUuid => {
                            let participant = participants[participantUuid];
                            participant.picks = {};
                            let pickersNormalized = {};
                            participant.pickers.forEach(picker => {
                                pickers[picker.uuid] = picker;
                                pickersNormalized[picker.type] = picker.uuid;

                                participant.picks[picker.type] = {};

                                picker.picks.forEach(pick => {
                                    picks[pick.uuid] = pick;
                                    participant.picks[picker.type][pick.type] = pick.uuid;
                                });
                                delete picker.picks;

                                let pickerCommentUuids = [];
                                picker.comments.forEach(comment => {
                                    comments[comment.uuid] = comment;
                                    pickerCommentUuids.push(comment.uuid);
                                });
                                picker.comments = pickerCommentUuids;

                            });
                            participant.pickers = pickersNormalized;

                            let rewards = {};
                            participant.rewards.forEach(reward => {
                                let key = reward.pick ? reward.pick : 'global';
                                if (!rewards[key])
                                    rewards[key] = {};
                                rewards[key][reward.reason] = reward;
                            });

                            participant.rewards = rewards;
                        });

                        let rewardsMap = {};
                        event.rewards.forEach(rewardItem => {
                            rewardsMap[ rewardItem.reason ] = rewardItem;
                        });
                        delete event.rewards;

                        let gamesMap = {};
                        event.games.forEach(game => {
                            gamesMap[game.id] = game;
                        });
                        delete event.games;

                        commit('setComments', comments);
                        commit('setGames', gamesMap);
                        commit('setPicks', picks);
                        commit('setPickers', pickers);
                        commit('setUsers', users);
                        commit('setEvents', {[event.uuid]: event});
                        commit('setParticipants', participants);
                        commit('setRewardsMap', rewardsMap);

                        resolve();
                    })
                    .catch(e => reject(e.response.data.errors.detail));
            });
        },

        updateParticipantGroupWins: function ({commit}, {participant, groupWins}) {
            return api.participants.updateGroupWins(participant, groupWins)
                .then(() => {
                    participant.groupWins = groupWins;
                    commit('setParticipant', participant);
                });
        },

        updateParticipantBlaeoGames: function ({commit}, {participant, blaeoGames}) {
            return api.participants.updateBlaeoGames(participant, blaeoGames)
                .then(() => {
                    participant.blaeoGames = blaeoGames;
                    commit('setParticipant', participant)
                });
        },

        updateParticipantBlaeoPoints: function ({commit, state, getters}, {participant, blaeoPoints}) {
            return api.participants.updateBlaeoPoints(participant, blaeoPoints)
                .then(() => {
                    let updatedParticipant = {...participant};
                    if (!updatedParticipant.rewards)
                        updatedParticipant.rewards = {};

                    if (!updatedParticipant.rewards.global)
                        updatedParticipant.rewards.global = {};

                    updatedParticipant.rewards.global[getters.rewardReasons.BLAEO_POINTS] = {
                        pick: null,
                        reason: getters.rewardReasons.BLAEO_POINTS,
                        value: blaeoPoints
                    };

                    commit('setParticipant', updatedParticipant)
                });
        },

        updateParticipantExtraRules: function ({commit}, {participant, extraRules}) {
            return api.participants.updateExtraRules(participant, extraRules)
                .then(() => {
                    participant.extraRules = extraRules;
                    commit('setParticipant', participant)
                });
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
                });
        },

        importGroup: function ({commit}, code) {
            return new Promise((resolve, reject) => {
                return api.groups.import(code)
                    .then(() => resolve())
                    .catch(e => reject(e.response.data.errors.detail));
            });
        },

        importGames: function () {
            return new Promise((resolve, reject) => {
                return api.games.import()
                    .then(() => resolve())
                    .catch(e => reject(e.response.data.errors.detail));
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
                    .catch(e => reject(e.response.data.errors.status));
            })
        },

        makePick: function ({commit, state}, {picker, pick, participantUuid}) {
            return new Promise((resolve, reject) => {
                return api.pickers.makePick(picker, pick)
                    .then(() => {

                        let participant = {...state.participants[participantUuid]};
                        let game = pick.game;
                        pick.game = game.id;

                        participant.picks[picker.type][pick.type] = pick.uuid;

                        commit('setGame', game);
                        commit('setParticipant', participant);
                        commit('setPick', pick);
                        resolve();
                    })
                    .catch(e => reject(e.response.data.errors.detail));
            })
        },

        changePickGame: function ({commit, state}, {picker, pick, participantUuid}) {
            return new Promise((resolve, reject) => {
                return api.picks.changeGame(pick)
                    .then(() => {
                        let participant = {...state.participants[participantUuid]};
                        let game = pick.game;
                        pick.game = game.id;
                        pick.rejected = false;

                        participant.picks[picker.type][pick.type] = pick.uuid;

                        commit('setGame', game);
                        commit('setParticipant', participant);
                        commit('setPick', pick);
                    })
                    .catch(e => reject(e.response.data.errors.detail));
            })
        },

        changePickStatus: function ({commit}, {pick, status}) {
            return api.picks.changeStatus(pick, status)
                .then(() => {
                    pick.playedStatus = status;
                    commit('setPick', pick);
                });
        },

        loadEventPotentialParticipants: function ({commit}, event) {
            return new Promise((resolve) => {
                return api.events.getPotentialParticipants(event)
                    .then(({data: participantsResult}) => {
                        resolve(participantsResult.data);
                    });
            });
        },

        addEventParticipant: function ({commit}, {event, participantUuid, steamId}) {
            return api.events.addParticipant(event, participantUuid, steamId);
        },

        removeEventParticipant: function ({commit}, {participantUuid}) {
            return api.participants.remove(participantUuid)
                .then(() => {
                    commit('removeParticipant', participantUuid);
                });
        },

        importEventPlaystats: function ({commit}, {event}) {
            return api.events.importPlaystats(event);
        },

        addPickerComment: function ({commit, state, getters}, {picker, comment}) {
            return new Promise((resolve, reject) => {
                return api.pickers.addComment(picker, comment)
                    .then(() => {
                        commit('setComment', comment);

                        picker.comments.push(comment.uuid);
                        commit('setPicker', {...picker});

                        if (comment.referencedPick && comment.gameReferenceType === state.GAME_REFERENCE_TYPE.REPICK)
                        {
                            let pick = {...getters.getPick(comment.referencedPick), rejected: true};
                            commit('setPick', pick);
                        }

                        resolve();
                    })
                    .catch(e => reject(e.response.data.errors.detail));
            })
        },

        updateComment: function({commit, state}, comment) {
            return api.comments.update(comment)
                .then(() => {
                    comment.updatedAt = Vue.prototype.$getDateNow();
                    commit('setComment', comment);
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
                });
        },

        loadActivity: function ({commit, state}, page) {
            return new Promise((resolve) => {
                return api.activity.getList(page)
                    .then(({data: activityResult}) => {

                        if (activityResult.meta.refs.user)
                        {
                            let users = {};
                            activityResult.meta.refs.user.forEach(user => {
                                users[user.steamId] = user;
                            });
                            commit('setUsers', users);
                        }

                        if (activityResult.meta.refs.game)
                        {
                            let games = {};
                            activityResult.meta.refs.game.forEach(game => {
                                games[game.id] = game;
                            });
                            commit('setGames', games);
                        }

                        if (activityResult.meta.refs.eventPick)
                        {
                            let picks = {};
                            activityResult.meta.refs.eventPick.forEach(pick => {
                                picks[pick.uuid] = pick;
                            });
                            commit('setPicks', picks);
                        }

                        if (activityResult.meta.refs.eventPickerComment)
                        {
                            let comments = {};
                            activityResult.meta.refs.eventPickerComment.forEach(comment => {
                                comments[comment.uuid] = comment;
                            });
                            commit('setComments', comments);
                        }

                        let pagin = {
                            size: activityResult.meta.size,
                            page: activityResult.meta.page,
                            pages: activityResult.meta.pages,
                            total: activityResult.meta.total
                        };

                        commit('setActivity', activityResult.data);
                        resolve(pagin)
                    });
            });
        },

        setLastVisit: function ({commit}, lastVisit) {
            commit('setLastVisit', lastVisit);
        },

        setCommentNotification: function ({commit}, commentUuid) {
            commit('addCommentNotification', commentUuid);
        },

        setRepickNotification: function ({commit}, pickUuid) {
            commit('addRepickNotification', pickUuid);
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

        removeParticipant: (state, participantUuid) => Vue.delete(state.participants, participantUuid),

        setPickers: (state, pickers) => state.pickers = pickers,

        setPicker: (state, picker) => Vue.set(state.pickers, picker.uuid, picker),

        setPicks: (state, picks) => state.picks = picks,

        setGames: (state, games) => state.games = games,

        setGame: (state, game) => Vue.set(state.games, game.id, game),

        setComments: (state, comments) => state.comments = comments,

        setComment: (state, comment) => Vue.set(state.comments, comment.uuid, comment),

        setPick: (state, pick) => Vue.set(state.picks, pick.uuid, pick),

        setBlock: (state, block) => Vue.set(state.blocks, block.code, block.content),

        setActivity: (state, activity) => state.activity = activity,

        setRewardsMap: (state, rewardsMap) => state.rewardsMap = rewardsMap,

        setLastVisit: (state, lastVisit) => state.lastVisit = lastVisit,

        addCommentNotification: (state, commentUuid) => {
            if (!state.notifications.comments.includes(commentUuid))
                state.notifications.comments.push(commentUuid);
        },

        addRepickNotification: (state, pickUuid) => {
            if (!state.notifications.repicks.includes(pickUuid))
                state.notifications.repicks.push(pickUuid);
        }
    }
});
