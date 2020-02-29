<template>
    <div>
        <loading-indicator v-if="isLoading" />

        <template v-else>
            <h1 class="title">{{event.name}}</h1>

            <router-link
                :to="{name: 'event', params: {eventUuid: uuid}}"
                class="button button--space-right"
            >Back to event</router-link>

            <button
                v-if="sortField !== 'name'"
                @click="setSortByName"
                type="button"
                class="button button--space-right"
            >Sort Alphabetically</button>
            <button
                v-if="sortField !== 'points'"
                @click="setSortByPoints"
                type="button"
                class="button button--space-right"
            >Sort By total points</button>
            <button type="button" class="button button--space-right">Show TOP15</button>


            <div class="leaderboard">
                <div class="leaderboard-item leaderboard-item--head">
                    <div class="leaderboard-item__user">Member</div>
                    <div class="leaderboard-item__games">
                        <div class="leaderboard-item__games-head">Games</div>
                        <div class="leaderboard-item__game">Short</div>
                        <div class="leaderboard-item__game">Medium</div>
                        <div class="leaderboard-item__game">Long</div>
                        <div class="leaderboard-item__game">Very long</div>
                    </div>
                    <div class="leaderboard-item__bonus">All 7</div>
                    <div class="leaderboard-item__blaeo">BLAEO points</div>
                    <div class="leaderboard-item__total">Total</div>
                </div>

                <leaderboard-item
                    v-for="(participant, i) in participants"
                    :key="participant.uuid"
                    :participant="participant"
                    :number="i"
                />

                <div class="leaderboard-item">
                    <div class="leaderboard-item__user">
                        Total per categories:
                    </div>
                    <div class="leaderboard-item__games">

                        <div
                            v-for="pickType in [SHORT, MEDIUM, LONG, VERY_LONG]"
                            class="leaderboard-item__game leaderboard-game"
                        >
                            <div v-if="pickType === SHORT" class="leaderboard-game__name">Short games</div>
                            <div v-if="pickType === MEDIUM" class="leaderboard-game__name">Medium games</div>
                            <div v-if="pickType === LONG" class="leaderboard-game__name">Long games</div>
                            <div v-if="pickType === VERY_LONG" class="leaderboard-game__name">Very long games</div>

                            <div class="leaderboard-game__stats">
                                <div class="leaderboard-game__stat">
                                    <i class="fa-icon fas fa-fw fa-trophy"></i>{{totalStats[pickType].achievements}}
                                </div>
                                <div class="leaderboard-game__stat">
                                    <i class="fa-icon far fa-fw fa-clock"></i>{{totalStats[pickType].playtimeHours}} hrs
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="leaderboard-item__bonus"></div>
                    <div class="leaderboard-item__blaeo"></div>
                    <div class="leaderboard-item__total"></div>
                </div>

                <div class="leaderboard-item">
                    <div class="leaderboard-item__user">Total stats</div>
                    <div class="leaderboard-item__games leaderboard-item__games--total">
                        <div class="leaderboard-item__game leaderboard-game">
                            <div class="leaderboard-game__name">Total hours played</div>
                            <div class="leaderboard-game__stats">
                                <div class="leaderboard-game__stat">
                                    <i class="fa-icon far fa-fw fa-clock"></i>{{totalStats.all.playtimeHours}} hrs
                                </div>
                            </div>
                        </div>
                        <div class="leaderboard-item__game leaderboard-game">
                            <div class="leaderboard-game__name">Total achievements unlocked</div>
                            <div class="leaderboard-game__stats">
                                <div class="lleaderboard-game__stat">
                                    <i class="fa-icon fas fa-fw fa-trophy"></i>{{totalStats.all.achievements}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="leaderboard-item__bonus"></div>
                    <div class="leaderboard-item__blaeo"></div>
                    <div class="leaderboard-item__total"></div>
                </div>

            </div>

        </template>
    </div>
</template>

<script>
    import {mapState, mapGetters} from 'vuex';
    import LoadingIndicator from "../components/LoadingIndicator";
    import LeaderboardItem from "../components/LeaderboardItem";
    export default {
        name: "EventLeaderboard",
        components: {LeaderboardItem, LoadingIndicator},
        props: {},
        data() {
            return {
                isLoading: false,
                sortField: 'name'
            };
        },
        computed: {
            ...mapState([
                'SHORT', 'MEDIUM', 'LONG', 'VERY_LONG'
            ]),

            ...mapGetters([
                'getPick',
                'getParticipantsSortedByName',
                'getParticipantsSortedByPoints'
            ]),

            participants: function () {
                return this.sortField === 'points' ? this.getParticipantsSortedByPoints : this.getParticipantsSortedByName;
            },

            uuid: function () {
                return this.$route.params.eventUuid;
            },
            event: function () {
                return this.$store.state.events[this.uuid];
            },
            totalStats: function () {
                let totals = {
                    [this.SHORT]: {playtime: 0, achievements: 0},
                    [this.MEDIUM]: {playtime: 0, achievements: 0},
                    [this.LONG]: {playtime: 0, achievements: 0},
                    [this.VERY_LONG]: {playtime: 0, achievements: 0},
                    all: {playtime: 0, achievements: 0}
                };
                Object.values(this.participants).forEach(participant => {
                    Object.values(participant.picks).forEach(pickerPicks => {
                        Object.values(pickerPicks).forEach(pickUuid => {
                            let pick = this.getPick(pickUuid);
                            totals.all.achievements += +pick.playingState.achievements;
                            totals.all.playtime += +pick.playingState.playtime;
                            totals[pick.type].achievements += +pick.playingState.achievements;
                            totals[pick.type].playtime += +pick.playingState.playtime;
                        });
                    })
                });

                Object.keys(totals).forEach(totalKey => {
                    let totalItem = totals[totalKey];
                    totalItem.playtimeHours = (totalItem.playtime / 60).toFixed(1);
                });

                return totals;
            }
        },
        methods: {
            setSortByName: function () {
                this.sortField = 'name';
            },

            setSortByPoints: function () {
                this.sortField = 'points';
            }
        },
        created() {
            this.isLoading = true;
            this.$store.dispatch('loadEvent', this.uuid)
                .then(() => {})
                .catch(e => console.log(e))
                .finally(() => this.isLoading = false);
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/medal";
    @import "../assets/leaderboard-item";
    @import "../assets/leaderboard-game";

    .leaderboard{
        font-size: 14px;
    }

</style>