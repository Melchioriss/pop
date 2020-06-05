<template>
    <div class="participation">

        <div
            :class="['participation__line', 'participation__line--base', {'participation__line--mine': isParticipant}]"
        >
            <div class="participation__participant">
                <div class="participation__user-title">Participant:</div>
                <div
                    :class="['user-tile', {'user-tile--mine': isParticipant}]"
                >
                    <div class="user-tile__pic-block">
                        <img
                            :src="participantUser.avatar"
                            class="user-tile__pic"
                            :alt="participantUser.profileName"
                        />
                    </div>
                    <div class="user-tile__info">
                        <div class="user-tile__name">{{participantUser.profileName}}</div>
                        <div class="user-tile__links">
                            <a
                                :href="participantUser.profileUrl"
                                target="_blank"
                            >Steam</a>
                            <a
                                v-if="participantBlaeoLink"
                                :href="participantBlaeoLink"
                                target="_blank"
                            >BLAEO</a>
                        </div>
                    </div>
                </div>
                <div
                    v-if="all7BeatenReward"
                    class="participation__reward-info"
                >
                    <span class="participation__reward-title">All 7 games finished!</span>
                    <div
                        :title="rewardHints[ rewardReasons.ALL_PICKS_BEATEN ]"
                        class="medal"
                    >{{all7BeatenReward.value}}</div>
                </div>

                <div
                    v-if="potentialAll7Reward"
                    class="participation__reward-info"
                >
                    <span class="participation__reward-title participation__reward-title--disabled">All 7:</span>
                    <div
                        :title="rewardHints[ potentialAll7Reward.reason ]"
                        class="medal medal--absent"
                    >{{potentialAll7Reward.value}}</div>
                </div>

                <div v-if="isAdmin">
                    <span
                        @click="removeParticipant"
                        class="edit-link edit-link--delete"
                    >Remove participant</span>
                </div>

            </div>
            <div class="participation__main-area">

                <div class="participation__additional">
                    <label
                        :for="'wins_'+participant.uuid"
                        class="participation__sub-title"
                    >Group Win(s):</label>
                    <template v-if="isEditingGroupWins">
                        <input
                            v-model="newGroupWins"
                            :id="'wins_'+participant.uuid"
                            type="text"
                            class="input"
                        />
                        <button
                            @click="saveGroupWins"
                            type="button"
                            class="button button--space-left"
                        >save</button>
                        <button
                            @click="endEditingGroupWins"
                            type="button"
                            class="button button--space-left"
                        >cancel</button>
                    </template>

                    <span
                        v-else
                        class="text"
                        v-html="$getMarkedContent(participant.groupWins)"
                    ></span>
                    <span
                        v-if="canEditFields && !isEditingGroupWins"
                        @click="startEditingGroupWins"
                        class="edit-link"
                    >edit</span>
                </div>

                <div class="participation__additional">
                    <label
                        :for="'bg_'+participant.uuid"
                        class="participation__sub-title"
                    >BLAEO games:</label>
                    <template v-if="isEditingBlaeoGames">
                        <input
                            v-model="newBlaeoGames"
                            :id="'bg_'+participant.uuid"
                            type="text"
                            class="input"
                        />
                        <button
                            @click="saveBlaeoGames"
                            type="button"
                            class="button button--space-left"
                        >save</button>
                        <button
                            @click="endEditingBlaeoGames"
                            type="button"
                            class="button button--space-left"
                        >cancel</button>
                    </template>
                    <span
                        v-else
                        class="text"
                        v-html="$getMarkedContent(participant.blaeoGames)"
                    ></span>
                    <span
                        v-if="canEditFields && !isEditingBlaeoGames"
                        @click="startEditingBlaeoGames"
                        class="edit-link"
                    >edit</span>
                </div>

                <div class="participation__additional">
                    <label
                        :for="'bp_'+participant.uuid"
                        class="participation__sub-title"
                    >BLAEO points:</label>
                    <template v-if="isEditingBlaeoPoints">
                        <input
                            v-model="newBlaeoPoints"
                            :id="'bp_'+participant.uuid"
                            type="number"
                            class="input"
                        />
                        <button
                            @click="saveBlaeoPoints"
                            type="button"
                            class="button button--space-left"
                        >save</button>
                        <button
                            @click="endEditingBlaeoPoints"
                            type="button"
                            class="button button--space-left"
                        >cancel</button>
                    </template>
                    <div
                        v-else-if="blaeoPoints"
                    >
                        <div
                            :title="rewardHints[ rewardReasons.BLAEO_POINTS ]"
                            class="medal"
                        >{{+blaeoPoints}}</div>
                    </div>
                    <span
                        v-if="isAdmin && !isEditingBlaeoPoints"
                        @click="startEditingBlaeoPoints"
                        class="edit-link"
                    >edit</span>
                </div>

                <div class="participation__sub-title">
                    <span>Extra rules by <b>{{participantUser.profileName}}</b> for this event:</span>
                    <span
                        v-if="canEditFields && !isEditingExtraRules"
                        @click="startEditingExtraRules"
                        class="edit-link"
                    >edit</span>
                </div>
                <template v-if="isEditingExtraRules">
                    <textarea
                        v-model="newExtraRules"
                        class="input input--textarea input--space-bottom"
                        placeholder="Extra rules for picking games"
                        rows="5"
                    >{{newExtraRules}}</textarea>
                    <button
                        @click="saveExtraRules"
                        type="button"
                        class="button button--space-right"
                    >Save</button>
                    <button
                        @click="endEditingExtraRules"
                        type="button"
                        class="button button--space-right"
                    >Cancel</button>
                </template>
                <div
                    v-else
                    class="participation__rules text"
                    v-html="$getMarkedContent(participant.extraRules)"
                ></div>
            </div>
        </div>

        <template
            v-for="pickerType in [MAJOR, MINOR]"
        >
            <div class="participation__line">
                <div class="participation__picker">
                    <div class="participation__user-title">{{pickerType === MAJOR ? 'Major' : 'Minor'}} Picker:</div>
                    <participation-picker
                        :user-id="pickersUserIds[pickerType]"
                        @change-picker="saveNewPicker($event, pickerType)"
                        class="participation__user"
                    />

                    <div
                        v-if="pickers[pickerType]"
                        class="participation__picker-bottom"
                    >
                    <span
                        v-if="!isCommentsShown[pickerType]"
                        @click="showComments(pickerType)"
                        class="edit-link edit-link--comments-show"
                    >Show comments ({{pickers[pickerType].comments.length}})</span>
                    <span
                        v-if="isCommentsShown[pickerType]"
                        @click="hideComments(pickerType)"
                        class="edit-link edit-link--comments-hide"
                    >Hide comments</span>
                    </div>

                </div>
                <div class="participation__main-area">
                    <div
                        v-if="pickers[pickerType]"
                        class="participation__picks"
                    >
                        <div
                            v-for="pickType in pickTypes[pickerType]"
                            :id="participant.picks[pickerType][pickType]"
                            class="participation__pick"
                        >
                            <div v-if="pickType === SHORT" class="participation__pick-help">Short game (2-8h)</div>
                            <div v-if="pickType === MEDIUM" class="participation__pick-help">Medium game (8-15h)</div>
                            <div v-if="pickType === LONG" class="participation__pick-help">Long game (15-25h)</div>
                            <div v-if="pickType === VERY_LONG" class="participation__pick-help">Very long game (25h+)</div>
                            <pick-item
                                :pick="getPick(participant.picks[pickerType][pickType])"
                                :user-id="participant.user"
                                :is-picker="isPicker(pickerType)"
                                :is-participant="isParticipant"
                                :rewards="participant.rewards[ participant.picks[pickerType][pickType] ]"
                                :potential-rewards="getPotentialRewards(pickType)"
                                :pick-error="pickErrors[pickerType][pickType]"
                                @select-game="selectGame($event, pickType, pickerType)"
                                @change-status="changeStatus($event, pickType, pickerType)"
                            />
                            <div
                                v-if="reviewsForPicks[ participant.picks[pickerType][pickType] ]"
                                @click="scrollToReview(reviewsForPicks[ participant.picks[pickerType][pickType] ], pickerType)"
                                class="participation__pick-review"
                            >
                                <i class="fa-icon fa-fw far fa-file-alt"></i>Review
                            </div>
                        </div>

                        <div
                            v-if="pickerType === MINOR"
                            class="participation__pick participation__pick--total"
                        >
                            <div class="participation__total-title">
                                {{participantUser.profileName}}'s Totals:
                            </div>
                            <div class="participation__total-line">
                                <i class="fa-icon fa-fw fas fa-trophy"></i>{{totalPlayStats.achievements}} achievements taken
                            </div>
                            <div class="participation__total-line">
                                <i class="fa-icon fa-fw far fa-clock"></i>{{totalPlayStats.playtimeHours}} hours played
                            </div>
                            <div class="participation__total-line participation__reward-info">
                                <div class="participation__reward-info">
                                    <span class="participation__reward-title">Total points achieved:</span>
                                    <div class="medal medal--total" title="Total points achieved">{{participant.totalRewardValue}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                v-if="pickers[pickerType]"
                v-show="isCommentsShown[pickerType]"
                class="participation__comments"
            >
                <comments-area
                    :comments="pickers[pickerType].comments"
                    :can-comment="canComment(pickerType)"
                    :is-participant="isParticipant"
                    :picked-games="pickedGames[pickerType]"
                    :unique-key="'comments_'+participant.uuid"
                    @add-comment="addComment($event, pickerType)"
                />
                <error-box v-if="commentErrors[pickerType]">{{commentErrors[pickerType]}}</error-box>
            </div>
        </template>

    </div>
</template>

<script>
    import uuid from 'uuid';
    import {mapState, mapGetters} from 'vuex';
    import CommentItem from "./CommentItem";
    import ParticipationPicker from "./ParticipationPicker";
    import PickItem from "./PickItem";
    import CommentsArea from "./CommentsArea";
    import ErrorBox from "./ErrorBox";

    export default {
        name: "ParticipationItem",
        components: {ErrorBox, CommentsArea, PickItem, ParticipationPicker, CommentItem},
        props: {
            participant: {
                type: Object,
                default: () => {
                    return {
                        uuid: '',
                        user: '',
                        active: true,
                        groupWins: '',
                        blaeoGames: '',
                        pickers: [],
                        totalRewardValue: 0
                    };
                }
            },
            isHidingAllComments: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isCommentsShown: {},
                isEditingGroupWins: false,
                isEditingBlaeoGames: false,
                isEditingExtraRules: false,
                isEditingBlaeoPoints: false,
                newExtraRules: '',
                newGroupWins: '',
                newBlaeoGames: '',
                newBlaeoPoints: '',
                commentErrors: {},
                pickErrors: {}
            };
        },
        computed: {
            ...mapState([
                'BLAEO_USER_BASE_LINK',
                'MAJOR', 'MINOR',
                'SHORT', 'MEDIUM', 'LONG', 'VERY_LONG',
                'GAME_REFERENCE_TYPE',
                'rewardsMap'
            ]),

            ...mapGetters({
                users: 'getSortedUsers',
                isAdmin: 'loggedUserIsAdmin',
            }),

            ...mapGetters([
                'loggedUserSteamId',
                'getPick',
                'getGame',
                'getComment',
                'rewardReasons',
                'rewardHints',
                'pickTypes'
            ]),

            canEditFields: function () {
                return this.isAdmin || this.isParticipant;
            },

            participantUser: function () {
                return this.$store.getters.getUser(this.participant.user);
            },
            participantBlaeoLink: function () {
                return this.participantUser.blaeoName ? this.BLAEO_USER_BASE_LINK + this.participantUser.blaeoName : '';
            },
            blaeoPoints: function () {
                if (this.participant.rewards.global && this.participant.rewards.global[ this.rewardReasons.BLAEO_POINTS ])
                    return this.participant.rewards.global[ this.rewardReasons.BLAEO_POINTS ].value;

                return null;
            },
            all7BeatenReward: function () {
                return this.participant.rewards.global ? this.participant.rewards.global[ this.rewardReasons.ALL_PICKS_BEATEN ] : null;
            },
            potentialAll7Reward: function () {
                if (this.all7BeatenReward)
                    return null;

                return this.rewardsMap[ this.rewardReasons.ALL_PICKS_BEATEN ];
            },
            pickers: function () {
                let pickers = {};
                [this.MAJOR, this.MINOR].forEach(type => {
                    let picker = this.$store.getters.getPicker( this.participant.pickers[type] );
                    if (picker)
                        pickers[type] = picker;
                });
                return pickers;
            },
            pickersUserIds: function () {
                return Object.values(this.pickers).reduce((prev, cur) => ({...prev, ...{[cur.type]: cur.user}}), {});
            },
            isParticipant: function () {
                return this.participant.user === this.loggedUserSteamId;
            },
            totalPlayStats: function () {
                let totals = {
                    achievements: 0,
                    playtime: 0,
                    playtimeHours: 0
                };

                Object.values(this.participant.picks).forEach(pickerPicks => {
                    Object.values(pickerPicks).forEach(pickUuid => {
                        let pick = this.getPick(pickUuid);
                        totals.achievements += +pick.playingState.achievements;
                        totals.playtime += +pick.playingState.playtime;
                    });
                });

                totals.playtimeHours = (totals.playtime / 60).toFixed(1);

                return totals;
            },
            pickedGames: function () {
                let gamesByPicker = {};

                Object.keys(this.participant.picks).forEach(pickerType => {
                    let pickerPicks = this.participant.picks[pickerType];
                    let games = {};
                    Object.values(pickerPicks).forEach(pickUuid => {
                        let pick = this.getPick(pickUuid);
                        let game = this.getGame(pick.game);
                        let gameId = game.id;
                        games[gameId] = {...game};
                        games[gameId].pickUuid = pickUuid;
                        games[gameId].playedStatus = pick.playedStatus;
                    });

                    this.pickers[pickerType].comments.forEach(commentUuid => {
                        let comment = this.getComment(commentUuid);
                        if (comment.referencedGame && comment.gameReferenceType === this.GAME_REFERENCE_TYPE.REVIEW && games[comment.referencedGame])
                            games[comment.referencedGame].reviewExists = true;
                    });

                    gamesByPicker[pickerType] = games;
                });

                return gamesByPicker;
            },
            reviewsForPicks: function () {
                let reviewsForPicks = {};
                Object.keys(this.participant.picks).forEach(pickerType => {
                    this.pickers[pickerType].comments.forEach(commentUuid => {
                        let comment = this.getComment(commentUuid);
                        if (comment.referencedPick && comment.gameReferenceType === this.GAME_REFERENCE_TYPE.REVIEW)
                            reviewsForPicks[comment.referencedPick] = comment.uuid;
                    });
                });
                return reviewsForPicks;
            }
        },
        watch: {
            isHidingAllComments: function () {
                Object.keys(this.isCommentsShown).forEach(type => {
                    this.hideComments(type);
                });
            }
        },
        methods: {
            showComments(type) {
                this.$set(this.isCommentsShown, type, true);
            },

            hideComments(type) {
                this.$set(this.isCommentsShown, type, false);
            },

            canComment: function (type) {
                if (this.isAdmin || this.isParticipant)
                    return true;

                return this.isPicker(type);
            },

            saveNewPicker(newPickerSteamId, pickerType) {

                let existedPicker = this.$store.getters.getPicker(this.participant.pickers[pickerType]);

                if (existedPicker && existedPicker.uuid)
                {
                    this.$store.dispatch(
                        'replacePickerUser',
                        {
                            picker: existedPicker,
                            userId: newPickerSteamId
                        });
                }
                else
                {
                    let picker = {
                        uuid: uuid.v4(),
                        type: pickerType,
                        user: newPickerSteamId,
                        picks: [],
                        comments: []
                    };

                    this.$store.dispatch(
                        'addPicker',
                        {
                            picker: picker,
                            participant: this.participant
                        });
                }
            },

            isPicker: function (pickerType) {
                let picker = this.pickers[pickerType];
                if (!picker)
                    return false;

                return (picker.user === this.loggedUserSteamId);
            },

            startEditingGroupWins() {
                this.newGroupWins = this.participant.groupWins;
                this.isEditingGroupWins = true;
            },

            endEditingGroupWins() {
                this.isEditingGroupWins = false;
            },

            saveGroupWins() {
                this.$store.dispatch('updateParticipantGroupWins', {participant: this.participant, groupWins: this.newGroupWins})
                    .then(() => {
                        this.endEditingGroupWins();
                    });
            },

            startEditingBlaeoGames() {
                this.newBlaeoGames = this.participant.blaeoGames;
                this.isEditingBlaeoGames = true;
            },

            endEditingBlaeoGames() {
                this.isEditingBlaeoGames = false;
            },

            saveBlaeoGames() {
                this.$store.dispatch('updateParticipantBlaeoGames', {participant: this.participant, blaeoGames: this.newBlaeoGames})
                    .then(() => {
                        this.endEditingBlaeoGames();
                    });
            },

            startEditingExtraRules() {
                this.newExtraRules = this.participant.extraRules;
                this.isEditingExtraRules = true;
            },

            endEditingExtraRules() {
                this.isEditingExtraRules = false;
            },

            startEditingBlaeoPoints() {
                this.newBlaeoPoints = +this.blaeoPoints;
                this.isEditingBlaeoPoints = true;
            },

            endEditingBlaeoPoints() {
                this.isEditingBlaeoPoints = false;
            },

            saveBlaeoPoints() {
                this.$store.dispatch('updateParticipantBlaeoPoints', {participant: this.participant, blaeoPoints: this.newBlaeoPoints})
                    .then(() => {
                        this.endEditingBlaeoPoints();
                    });
            },

            saveExtraRules() {
                this.$store.dispatch('updateParticipantExtraRules', {participant: this.participant, extraRules: this.newExtraRules})
                    .then(() => {
                        this.endEditingExtraRules();
                    });
            },

            selectGame(game, gameType, pickerType) {
                let errors = this.pickErrors[pickerType];
                errors[gameType] = '';
                this.$set(this.pickErrors, pickerType, {...errors});
                let existedPick = this.getPick(this.participant.picks[pickerType][gameType]);
                let actionName = 'makePick';
                let pick = {};

                if (existedPick && existedPick.uuid)
                {
                    actionName = 'changePickGame';
                    pick = {...existedPick, ...{game}};
                }
                else
                {
                    pick = {
                        uuid: uuid.v4(),
                        type: gameType,
                        game: game,
                        playingState: {
                            playtime: null,
                            achievements: null
                        },
                        playedStatus: this.$store.state.NOT_PLAYED
                    };
                }

                this.$store.dispatch(
                    actionName,
                    {
                        picker: this.pickers[pickerType],
                        pick,
                        participantUuid: this.participant.uuid
                    })
                    .catch(e => {
                        let errors = this.pickErrors[pickerType];
                        errors[gameType] = e;
                        this.$set(this.pickErrors, pickerType, {...errors});
                    });
            },

            changeStatus(status, gameType, pickerType) {
                this.$store.dispatch(
                    'changePickStatus',
                    {
                        pick: this.getPick(this.participant.picks[pickerType][gameType]),
                        status
                    });
            },

            addComment(comment, pickerType) {
                this.$set(this.commentErrors, pickerType, '');

                this.$store.dispatch('addPickerComment', {
                    picker: this.$store.getters.getPicker(this.participant.pickers[pickerType]),
                    comment: Object.assign(comment, {user: this.loggedUserSteamId, createdAt: this.$getDateNow()})
                })
                    .catch(e => this.$set(this.commentErrors, pickerType, e));
            },

            scrollToReview(commentUuid, pickerType) {
                this.showComments(pickerType);
                setTimeout(() => {
                    document.getElementById('comment_'+commentUuid).scrollIntoView({behavior: 'smooth'});
                }, 25);
            },

            getPotentialRewards(pickType) {
                let potentialRewards = {};
                let reasonCompleted = this.rewardReasons.GAME_COMPLETED;
                let reasonBeaten = this.rewardReasons.GAME_BEATEN[pickType];

                potentialRewards[reasonCompleted] = this.rewardsMap[reasonCompleted];
                potentialRewards[ this.rewardReasons.GAME_BEATEN_UNI ] = this.rewardsMap[reasonBeaten];

                return potentialRewards;
            },

            removeParticipant() {
                let confirmed = confirm('Are you sure you want to delete this participant?\n\n' + this.participantUser.profileName);

                if (!confirmed)
                    return false;

                this.$store.dispatch('removeEventParticipant', {participantUuid: this.participant.uuid});
            }
        },
        created() {
            this.isCommentsShown = {
                [this.MAJOR]: true,
                [this.MINOR]: true
            };

            if (this.isHidingAllComments)
            {
                this.isCommentsShown[this.MAJOR] = false;
                this.isCommentsShown[this.MINOR] = false;
            }

            this.pickErrors = {
                [this.MAJOR]: {},
                [this.MINOR]: {}
            };

            this.commentErrors = {
                [this.MAJOR]: '',
                [this.MINOR]: ''
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/user-tile";
    @import "../assets/medal";

    .participation{
        border-top: 3px solid @color-dark-orange;
        padding-bottom: 10px;

        .dark-mode &{
            border-top-color: @color-dark-orange-hover;
        }

        &__line{
            display: flex;
            align-items: stretch;
            margin-bottom: 10px;

            &--base{
                margin-bottom: 0;
                padding: 20px 0 10px;
            }
        }

        &__participant{
            padding: 0 10px;
            width: 250px;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        &__user{
            width: 240px;
            flex-shrink: 0;
        }

        &__main-area{
            flex-grow: 1;
        }

        &__sub-title{
            color: @color-cobalt;
            font-size: 14px;
            margin-right: 10px;
            flex-shrink: 0;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__additional{
            margin-bottom: 6px;
            display: flex;
            align-items: baseline;
        }

        &__rules{
            font-size: 14px;

            & > p:last-child{
                margin-bottom: 0;
            }
        }

        &__picker{
            border-top: 1px solid @color-cobalt;
            border-bottom: 1px solid @color-cobalt;
            padding: 10px 0 10px 10px;
            display: flex;
            flex-direction: column;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__picker-bottom{
            margin-top: auto;
        }

        &__user-title{
            font-size: 12px;
            font-weight: bold;
            color: @color-cobalt;
            margin-bottom: 4px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__picks{
            display: flex;
            align-items: stretch;
            width: 100%;
            height: 100%;
        }

        &__pick{
            display: flex;
            flex-direction: column;
            width: 25%;
            flex-basis: 25%;
            border: 1px solid @color-cobalt;
            box-sizing: border-box;
            position: relative;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }

            &:not(:first-child){
                border-left: none;
            }

            &--total{
                justify-content: center;
                align-items: center;
                padding: 6px 10px;
                background: none;
                box-shadow: inset 0 0 0 3px @color-cobalt;

                .dark-mode &{
                    box-shadow: inset 0 0 0 3px @color-cobalt-light;
                }
            }
        }

        &__pick-help{
            font-size: 12px;
            font-weight: bold;
            padding: 6px 10px 10px;
        }

        &__pick-review{
            position: absolute;
            top: 0;
            right: 0;
            font-size: 12px;
            color: @color-light-orange;
            background: @color-cobalt;
            padding: 2px 6px;
            cursor: pointer;

            .dark-mode &{
                background: @color-cobalt-light;
                color: @color-dark-bg;
            }
        }

        &__total-title{
            margin-bottom: 8px;
            color: @color-cobalt;
            font-size: 14px;
            font-weight: bold;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__total-line{
            margin-bottom: 5px;
        }

        &__comments{
            padding: 0 10px 10px 260px;
        }

        &__reward-info{
            display: flex;
            align-items: center;
        }

        &__reward-title{
            color: @color-beaten;
            font-weight: bold;
            font-size: 13px;
            margin-right: 6px;

            &--disabled{
                color: @color-gray-dark;
            }
        }
    }

</style>
