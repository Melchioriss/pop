<template>
    <div class="participation">

        <div
            :class="['participation__line', 'participation__line--base', {'participation__line--mine': isParticipant}]"
        >
            <div class="participation__participant">
                <div class="participation__user-title">Participant:</div>
                <div class="user-tile participation__user">
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
                        v-html="$getMarkedResult(participant.groupWins)"
                    ></span>
                    <span
                        v-if="!isEditingGroupWins"
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
                        v-html="$getMarkedResult(participant.blaeoGames)"
                    ></span>
                    <span
                        v-if="!isEditingBlaeoGames"
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
                        v-else
                    >
                        <div class="medal">18</div>
                    </div>
                    <span
                        v-if="!isEditingBlaeoPoints"
                        @click="startEditingBlaeoPoints"
                        class="edit-link"
                    >edit</span>
                </div>

                <div class="participation__sub-title">
                    <span>Extra rules by <b>{{participantUser.profileName}}</b> for this event:</span>
                    <span
                        v-if="!isEditingExtraRules"
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
                    v-html="$getMarkedResult(participant.extraRules)"
                ></div>
            </div>
        </div>
        <div
            :class="['participation__line', {'participation__line--mine': isMajorPicker}]"
        >
            <div class="participation__picker">
                <div class="participation__user-title">Major Picker:</div>
                <participation-picker
                    :user-id="majorPickerUserId"
                    @change-picker="saveNewPicker($event, MAJOR)"
                    class="participation__user"
                />

                <div
                    v-if="majorPicker"
                    class="participation__picker-bottom"
                >
                    <span
                        v-if="!isCommentsShown[MAJOR]"
                        @click="showComments(MAJOR)"
                        class="edit-link edit-link--comments-show"
                    >Show comments</span>
                    <span
                        v-if="isCommentsShown[MAJOR]"
                        @click="hideComments(MAJOR)"
                        class="edit-link edit-link--comments-hide"
                    >Hide comments</span>
                </div>

            </div>
            <div class="participation__main-area">
                <div
                    v-if="majorPicker"
                    class="participation__picks"
                >
                    <div class="participation__pick">
                        <div class="participation__pick-help">Short game (2-8h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MAJOR][SHORT])"
                            :user-id="participant.user"
                            :is-picker="isMajorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, SHORT, MAJOR)"
                            @change-status="changeStatus($event, SHORT, MAJOR)"
                        />
                    </div>
                    <div class="participation__pick">
                        <div class="participation__pick-help">Medium game (8-15h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MAJOR][MEDIUM])"
                            :user-id="participant.user"
                            :is-picker="isMajorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, MEDIUM, MAJOR)"
                            @change-status="changeStatus($event, MEDIUM, MAJOR)"
                        />
                    </div>
                    <div class="participation__pick">
                        <div class="participation__pick-help">Long game (15-25h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MAJOR][LONG])"
                            :user-id="participant.user"
                            :is-picker="isMajorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, LONG, MAJOR)"
                            @change-status="changeStatus($event, LONG, MAJOR)"
                        />
                    </div>
                    <div class="participation__pick">
                        <div class="participation__pick-help">Very long game (25h+)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MAJOR][VERY_LONG])"
                            :user-id="participant.user"
                            :is-picker="isMajorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, VERY_LONG, MAJOR)"
                            @change-status="changeStatus($event, VERY_LONG, MAJOR)"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="majorPicker"
            v-show="isCommentsShown[MAJOR]"
            class="participation__comments"
        >
            <comments-area
                :comments="majorPicker.comments"
                :can-comment="canComment(MAJOR)"
                @add-comment="addComment($event, MAJOR)"
            />
        </div>

        <div
            :class="['participation__line', {'participation__line--mine': isMinorPicker}]"
        >
            <div class="participation__picker">
                <div class="participation__user-title">Minor Picker:</div>

                <participation-picker
                    :user-id="minorPickerUserId"
                    @change-picker="saveNewPicker($event, MINOR)"
                    class="participation__user"
                />

                <div
                    v-if="minorPicker"
                    class="participation__picker-bottom"
                >
                    <span
                        v-if="!isCommentsShown[MINOR]"
                        @click="showComments(MINOR)"
                        class="edit-link  edit-link--comments-show"
                    >Show comments</span>
                    <span
                        v-if="isCommentsShown[MINOR]"
                        @click="hideComments(MINOR)"
                        class="edit-link  edit-link--comments-hide"
                    >Hide comments</span>
                </div>

            </div>
            <div class="participation__main-area">
                <div
                    v-if="minorPicker"
                    class="participation__picks"
                >
                    <div class="participation__pick">
                        <div class="participation__pick-help">Short game (2-8h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MINOR][SHORT])"
                            :user-id="participant.user"
                            :is-picker="isMinorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, SHORT, MINOR)"
                            @change-status="changeStatus($event, SHORT, MINOR)"
                        />
                    </div>
                    <div class="participation__pick">
                        <div class="participation__pick-help">Medium game (8-15h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MINOR][MEDIUM])"
                            :user-id="participant.user"
                            :is-picker="isMinorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, MEDIUM, MINOR)"
                            @change-status="changeStatus($event, MEDIUM, MINOR)"
                        />
                    </div>
                    <div class="participation__pick">
                        <div class="participation__pick-help">Long game (15-25h)</div>
                        <pick-item
                            :pick="getPick(participant.picks[MINOR][LONG])"
                            :user-id="participant.user"
                            :is-picker="isMinorPicker"
                            :is-participant="isParticipant"
                            @select-game="selectGame($event, LONG, MINOR)"
                            @change-status="changeStatus($event, LONG, MINOR)"
                        />
                    </div>
                    <div class="participation__pick participation__pick--total">
                        <div class="participation__total-title">
                            {{participantUser.profileName}}'s Totals:
                        </div>
                        <div class="participation__total-line">
                            <i class="fa-icon fa-fw fas fa-trophy"></i>{{totalPlayStats.achievements}} acheivements taken
                        </div>
                        <div class="participation__total-line">
                            <i class="fa-icon fa-fw far fa-clock"></i>{{totalPlayStats.playtimeHours}} hours played
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="minorPicker"
            v-show="isCommentsShown[MINOR]"
            class="participation__comments"
        >
            <comments-area
                :comments="minorPicker.comments"
                :can-comment="canComment(MINOR)"
                @add-comment="addComment($event, MINOR)"
            />
        </div>

    </div>
</template>

<script>
    import uuid from 'uuid';
    import {mapState, mapGetters} from 'vuex';
    import CommentItem from "./CommentItem";
    import ParticipationPicker from "./ParticipationPicker";
    import PickItem from "./PickItem";
    import CommentsArea from "./CommentsArea";

    export default {
        name: "ParticipationItem",
        components: {CommentsArea, PickItem, ParticipationPicker, CommentItem},
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
                        pickers: []
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
                newBlaeoPoints: ''
            };
        },
        computed: {
            ...mapState([
                'BLAEO_USER_BASE_LINK',
                'MAJOR', 'MINOR',
                'SHORT', 'MEDIUM', 'LONG', 'VERY_LONG'
            ]),

            ...mapGetters({
                users: 'getSortedUsers',
                loggedUserSteamId: 'loggedUserSteamId',
                isAdmin: 'loggedUserIsAdmin',
                getPick: 'getPick'
            }),

            participantUser: function () {
                return this.$store.getters.getUser(this.participant.user);
            },
            participantBlaeoLink: function () {
                return this.participantUser.blaeoName ? this.BLAEO_USER_BASE_LINK + this.participantUser.blaeoName : '';
            },
            majorPicker: function () {
                return this.$store.getters.getPicker( this.participant.pickers[this.MAJOR] );
            },
            minorPicker: function () {
                return this.$store.getters.getPicker( this.participant.pickers[this.MINOR] );
            },
            isMajorPicker: function () {
                if (!this.majorPicker)
                    return false;

                return (this.majorPicker.user === this.loggedUserSteamId);
            },
            isMinorPicker: function () {
                if (!this.minorPicker)
                    return false;

                return (this.minorPicker.user === this.loggedUserSteamId);
            },
            isParticipant: function () {
                return this.participant.user === this.loggedUserSteamId;
            },
            majorPickerUserId: function () {
                return this.majorPicker ? this.majorPicker.user : '';
            },
            minorPickerUserId: function () {
                return this.minorPicker ? this.minorPicker.user : '';
            },
            totalPlayStats: function () {
                let totals = {
                    achievements: 0,
                    playtime: 0,
                    playtimeHours: 0
                };

                [this.majorPicker, this.minorPicker].forEach(picker => {
                    picker.picks.forEach(pick => {
                        totals.achievements += +pick.playingState.achievements;
                        totals.playtime += +pick.playingState.playtime;
                    });
                });

                totals.playtimeHours = (totals.playtime / 60).toFixed(1);

                return totals;
            }
        },
        watch: {
            isHidingAllComments: function () {
                Object.keys(this.isCommentsShown).forEach(type => {
                    this.hideComments(type);
                })
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

                if (type === this.MAJOR)
                    return this.isMajorPicker;

                return this.isMinorPicker;
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
                        picks: []
                    };

                    this.$store.dispatch(
                        'addPicker',
                        {
                            picker: picker,
                            participant: this.participant
                        });
                }
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
                this.isEditingBlaeoPoints = true;
            },

            endEditingBlaeoPoints() {
                this.isEditingBlaeoPoints = false;
            },

            saveBlaeoPoints() {
                console.log(this.newBlaeoPoints);
            },

            saveExtraRules() {
                this.$store.dispatch('updateParticipantExtraRules', {participant: this.participant, extraRules: this.newExtraRules})
                    .then(() => {
                        this.endEditingExtraRules();
                    });
            },

            selectGame(game, gameType, pickerType) {
                let existedPick = this.getPick(this.participant.picks[pickerType][gameType]);
                let actionName = 'makePick';
                let pick = {};

                if (existedPick && existedPick.uuid)
                {
                    actionName = 'changePick';
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
                        }
                    };
                }

                this.$store.dispatch(
                    actionName,
                    {
                        picker: (pickerType === this.MAJOR) ? this.majorPicker : this.minorPicker,
                        pick,
                        participantUuid: this.participant.uuid
                    })
                    .then();
            },

            changeStatus(status, gameType, pickerType) {
                this.$store.dispatch(
                    'changePickStatus',
                    {
                        pick: this.getPick(this.participant.picks[pickerType][gameType]),
                        status
                    });
            },

            addComment(commentText, pickerType) {

                this.$store.dispatch('addPickerComment', {
                    picker: this.$store.getters.getPicker(this.participant.pickers[pickerType]),
                    comment: {
                        text: commentText,
                        user: this.loggedUserSteamId,
                        createdAt: this.$getDateNow()
                    }
                });
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
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/user-tile";
    @import "../assets/medal";

    .participation{
        margin-bottom: 20px;
        border-bottom: 3px solid @color-cobalt;
        padding-bottom: 10px;

        &:first-child{
            padding-top: 20px;
            border-top: 3px solid @color-cobalt;
        }

        &__line{
            display: flex;
            align-items: stretch;
            margin-bottom: 10px;

            &--base{
                margin-bottom: 0;
                padding: 10px 0;
            }

            &--mine{
                background: @color-bg-light;
            }
        }

        &__participant{
            padding-left: 10px;
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
        }

        &__picker-bottom{
            margin-top: auto;
        }
        
        &__user-title{
            font-size: 12px;
            font-weight: bold;
            color: @color-cobalt;
            margin-bottom: 4px;
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

            &:not(:first-child){
                border-left: none;
            }

            &--total{
                justify-content: center;
                align-items: center;
                padding: 6px 10px;
                background: none;
                box-shadow: inset 0 0 0 3px @color-cobalt;
            }
        }
        
        &__pick-help{
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            padding: 6px 10px;
        }

        &__total-title{
            margin-bottom: 8px;
            color: @color-cobalt;
            font-size: 14px;
            font-weight: bold;
        }

        &__total-line{
            margin-bottom: 5px;
        }

        &__comments{
            padding: 10px 10px 10px 260px;
        }
    }

</style>