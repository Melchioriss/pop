<template>
    <div class="event">

        <loading-indicator v-if="isLoading"></loading-indicator>

        <error-box v-else-if="globalError">{{globalError}}</error-box>

        <template v-else>

            <h1 class="title">{{event.name}}</h1>

            <div class="event__params">
                <div class="event__params-item event__dates">
                    <i class="fa-icon fa-fw fas fa-calendar-alt event__icon"></i>{{$getExactDate(event.activePeriod.startDate)}} &mdash; {{$getExactDate(event.activePeriod.endDate)}}
                </div>
                <div
                    v-if="isAdmin"
                    class="event__params-item"
                >
                    <span
                        @click="deleteEvent"
                        class="edit-link edit-link--delete"
                    >Delete this event</span>
                </div>
            </div>

            <div
                v-if="event.description"
                v-html="$getMarkedContent(event.description)"
                class="event__description text"
            ></div>

            <div class="event__buttons">
                <router-link
                    :to="{name: 'event_leaderboard', params: {eventUuid: event.uuid}}"
                    class="button button--light button--space-right"
                >Leaderboard</router-link>
                <button
                    v-show="!isShowingOnlyMine"
                    @click="showOnlyMineParticipations"
                    type="button"
                    class="button button--space-right"
                >Show only Mine</button>
                <button
                    v-show="isShowingOnlyMine"
                    @click="showAllParticipations"
                    type="button"
                    class="button button--space-right"
                >Show All</button>
                <button
                    @click="hideAllComments"
                    type="button"
                    class="button button--space-right"
                >Hide all comments</button>
                <button
                    @click="togglePPTable"
                    type="button"
                    class="button button--space-right"
                >{{isShowingPPTable ? 'Hide' : 'Show'}} Pickers table</button>
                <router-link
                    v-if="isAdmin"
                    :to="{name: 'edit_event', params: {eventUuid: event.uuid}}"
                    class="button button--cobalt button--space-right"
                >Edit</router-link>
                <button
                    v-if="!isShowingGeneratePickersButton"
                    @click="generatePickers"
                    type="button"
                    class="button button--cobalt button--space-right"
                >Generate pickers</button>
                <button
                    v-if="isAdmin && !isShowingPotentialParticipants && potentialParticipants.length > 0"
                    @click="showPotentialParticipants"
                    type="button"
                    class="button button--cobalt button--space-right"
                >Add new participant</button>
                <button
                    v-if="isAdmin"
                    @click="importPlaystats"
                    type="button"
                    class="button button--cobalt button--space-right"
                >Update playing stats</button>
                <div
                    v-if="newCommentsCount || repickCount"
                    class="event__new-indicator"
                >
                    <div v-if="newCommentsCount">
                        <i class="fa-icon fa-fw far fa-comments"></i>{{newCommentsCount}} new comment(s) since last visit
                    </div>
                    <div v-if="repickCount">
                        <i class="fa-icon fa-fw fas fa-recycle"></i>{{repickCount}} active repick request(s)
                    </div>
                </div>
            </div>

            <loading-indicator
                v-if="isUpdatingPlaystats"
            >updating playing stats, it may take a while...</loading-indicator>

            <error-box
                v-if="error"
            >{{error}}</error-box>

            <div
                v-if="isShowingPotentialParticipants"
                class="event__potential"
            >
                <div class="event__potential-heading">Add new participant:</div>
                <div
                    class="event__potential-item"
                    v-for="user in potentialParticipants"
                >
                    <a
                        :href="user.profileUrl"
                        target="_blank"
                    >{{user.profileName}}</a>
                    <button
                        @click="addNewParticipant(user.steamId)"
                        type="button"
                        class="button button--cobalt button--space-left"
                    >Add</button>
                </div>
            </div>

            <div
                v-if="isShowingPPTable"
                class="text"
            >
                <table class="event__pickers-table">
                    <tr>
                        <th>Participant</th>
                        <th>Major Picker</th>
                        <th>Minor Picker</th>
                    </tr>
                    <tr
                        v-for="ppTableItem in participantPickerTable"
                    >
                        <td>{{ppTableItem['participant']}}</td>
                        <td>{{ppTableItem[MAJOR]}}</td>
                        <td>{{ppTableItem[MINOR]}}</td>
                    </tr>
                </table>
            </div>

            <div class="event__participants">

                <participation-item
                    v-for="participant in participants"
                    v-show="!hiddenParticipants[participant.uuid]"
                    :key="'p_'+participant.uuid"
                    :participant="participant"
                    :is-hiding-all-comments="isHidingAllComments"
                />

            </div>

        </template>

    </div>
</template>

<script>
    import uuid from 'uuid';
    import {mapState, mapGetters} from 'vuex';
    import ParticipationItem from "../components/ParticipationItem";
    import LoadingIndicator from "../components/LoadingIndicator";
    import ErrorBox from "../components/ErrorBox";
    export default {
        name: "Event",
        components: {ErrorBox, LoadingIndicator, ParticipationItem},
        props: {},
        data() {
            return {
                isLoading: false,
                error: '',
                globalError: false,
                isHidingAllComments: false,
                isShowingOnlyMine: false,
                isShowingPPTable: false,
                potentialParticipants: [],
                isShowingPotentialParticipants: false,
                isUpdatingPlaystats: false
            };
        },
        computed: {
            ...mapState([
                'MAJOR',
                'MINOR'
            ]),

            ...mapGetters({
                participants: 'getParticipantsSortedByName',
                isAdmin: 'loggedUserIsAdmin'
            }),

            ...mapGetters([
                'loggedUserSteamId',
                'getPicker',
                'getCommentNotification',
                'getRepickNotification'
            ]),

            uuid: function () {
                return this.$route.params.eventUuid;
            },
            event: function () {
                return this.$store.state.events[this.uuid];
            },
            isShowingGeneratePickersButton: function () {
                if (!this.isAdmin)
                    return true;

                if (!this.participants.length)
                    return true;

                let generated = false;

                for (let i = 0; i < this.participants.length; i++ )
                {
                    if (Object.values(this.participants[i].pickers).length > 0)
                    {
                        generated = true;
                        break;
                    }
                }

                return generated;
            },
            hiddenParticipants: function () {
                if (!this.isShowingOnlyMine)
                    return {};

                let hiddenParticipants = {};

                Object.values(this.participants).forEach(participant => {
                    let participantUuid = participant.uuid;

                    if (participant.user === this.loggedUserSteamId)
                        return;

                    let majorPicker = this.getPicker(participant.pickers[this.MAJOR]);
                    if (majorPicker && this.$store.getters.getUser(majorPicker.user).steamId === this.loggedUserSteamId)
                        return;

                    let minorPicker = this.getPicker(participant.pickers[this.MINOR]);
                    if (minorPicker && this.$store.getters.getUser(minorPicker.user).steamId === this.loggedUserSteamId)
                        return;

                    hiddenParticipants[participantUuid] = true;
                });

                return hiddenParticipants;
            },
            participantPickerTable: function () {
                let ppTable = [];
                Object.values(this.participants).forEach(participant => {
                    let ppTableItem = {};
                    ppTableItem['participant'] = this.$store.getters.getUser(participant.user).profileName;

                    Object.keys(participant.pickers).forEach(pickerType => {
                        let pickerUuid = participant.pickers[pickerType];
                        let userId = this.$store.getters.getPicker(pickerUuid).user;
                        ppTableItem[pickerType] = this.$store.getters.getUser(userId).profileName;
                    });
                    ppTable.push(ppTableItem);
                });
                return ppTable;
            },
            newCommentsCount: function () {
                return this.getCommentNotification.length;
            },
            repickCount: function () {
                return this.getRepickNotification.length;
            }
        },
        methods: {
            hideAllComments () {
                this.isHidingAllComments = !this.isHidingAllComments;
            },
            showOnlyMineParticipations () {
                this.isShowingOnlyMine = true;
            },
            showAllParticipations () {
                this.isShowingOnlyMine = false;
            },
            generatePickers () {
                this.$store.dispatch('generateEventPickers', this.event)
                    .then(() => {
                        // reloading page, cause generatePickers leads to too many changes
                        location.reload();
                    })
                    .catch(e => {
                        console.log(e.response.data.errors.detail);
                        this.error = 'There was an error generating pickers.';
                    });
            },
            togglePPTable () {
                this.isShowingPPTable = !this.isShowingPPTable;
            },
            showPotentialParticipants () {
                this.isShowingPotentialParticipants = true;
            },
            addNewParticipant (userId) {
                this.$store.dispatch('addEventParticipant', {
                        event: this.event,
                        participantUuid: uuid.v4(),
                        steamId: userId
                    })
                    .then(() => {
                        location.reload();
                    })
            },
            importPlaystats () {
                this.isUpdatingPlaystats = true;
                this.$store.dispatch('importEventPlaystats', {event: this.event})
                    .then(() => {
                        location.reload();
                    })
                    .catch(e => {
                        console.log(e.response.data.errors.detail);
                        this.error = 'There was an error updating playstats, try again later.';
                        this.isUpdatingPlaystats = false;
                    })
            },
            deleteEvent () {
                let confirmed = confirm('Are you sure you want to delete this event?\n\n' + this.event.name);

                if (!confirmed)
                    return false;

                this.$store.dispatch('deleteEvent', this.event)
                    .then(() => {
                        this.$router.push({name: 'events'});
                    })
            }
        },
        created() {
            this.isLoading = true;
            this.$store.dispatch('loadEvent', this.uuid)
                .then(() => {

                    if (this.isAdmin)
                    {
                        setTimeout(() => {
                            this.$store.dispatch('loadEventPotentialParticipants', this.event)
                                .then((potentialParticipants) => {
                                    this.potentialParticipants = potentialParticipants;
                                });
                        }, 250);
                    }

                    this.$store.dispatch('setLastVisit', this.$cookie.get('event_'+this.uuid))
                        .then(() => {
                            this.$cookie.set('event_'+this.uuid, this.$getDateNow(), { expires: '3M', sameSite: 'Lax' })
                        });
                })
                .catch(e => {
                    this.globalError = e;
                })
                .finally(() => {
                    this.isLoading = false;
                    if (this.$route.hash)
                    {
                        setTimeout(() => {
                            let element = document.getElementById(this.$route.hash.replace('#', ''));

                            if (element)
                                element.scrollIntoView({behavior: 'auto'});

                        }, 25);
                    }
                });
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .event{

        &__params{
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        &__params-item{
            margin-right: 20px;
        }

        &__icon{
            color: @color-dark-orange;

            .dark-mode &{
                color: @color-light-orange;
            }
        }

        &__description{
            padding: 4px 0 4px 10px;
            border-left: 2px solid @color-cobalt;
            margin-bottom: 20px;

            & > p:last-child{
                margin-bottom: 0;
            }

            .dark-mode &{
                border-left-color: @color-cobalt-light;
            }
        }

        &__buttons{
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }

        &__potential{
            margin-bottom: 20px;
            padding: 20px 20px 10px;
            background: @color-gray;

            .dark-mode &{
                background: fade(@color-gray-darkest, 45%);
            }
        }

        &__potential-heading{
            font-size: 16px;
            font-weight: bold;
            color: @color-cobalt;
            margin-bottom: 16px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__potential-item{
            margin-bottom: 10px;
        }

        &__new-indicator{
            color: @color-red;
            font-weight: bold;
            padding: 6px 10px;
            line-height: 1.2;
            border-left: 3px solid @color-red;
            margin: 10px 0 10px auto;

            .dark-mode &{
                color: @color-red-light;
                border-left-color: @color-red-light;
            }
        }
    }

</style>