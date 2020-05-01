<template>
    <div>
        <h1 class="title">Admin Tools</h1>

        <div v-if="loggedUserIsAdmin" class="tools">
            <div class="tools__section">
                <div class="tools__section-name">Group Management</div>
                <div class="form">
                    <div class="form__block">
                        <label for="group_code">Group Indentificator:</label>
                        <input
                            v-model="newGroupCode"
                            type="text"
                            id="group_code"
                            class="input"
                        />
                    </div>
                </div>

                <div class="tools__process">
                    <button
                        @click="updateGroup"
                        type="button"
                        class="button button--space-right"
                    >Update Group Info And Members</button>
                    <loading-indicator
                        v-if="isUpdatingGroup"
                        :no-margin="true"
                    >updating...</loading-indicator>
                </div>

                <div
                    v-if="updateGroupResult"
                    class="tools__result"
                >{{updateGroupResult}}</div>
            </div>

            <div class="tools__section">
                <div class="tools__section-name">Sync with Steam</div>

                <div class="tools__process">
                    <button
                        @click="updateGames"
                        type="button"
                        class="button button--space-right"
                    >Update Steam games</button>
                    <loading-indicator
                        v-if="isUpdatingGames"
                        :no-margin="true"
                    >updating, this may take a while...</loading-indicator>
                </div>
                <div
                    v-if="updateGamesResult"
                    class="tools__result"
                >{{updateGamesResult}}</div>
            </div>

        </div>

    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import LoadingIndicator from "../components/LoadingIndicator";

    export default {
        name: "AdminTools",
        components: {LoadingIndicator},
        props: {},
        data() {
            return {
                newGroupCode: '',
                updateGroupResult: '',
                isUpdatingGroup: false,

                updateGamesResult: '',
                isUpdatingGames: false
            };
        },
        computed: {
            ...mapGetters({
                loggedUserIsAdmin: 'loggedUserIsAdmin',
                group: 'getMainGroup'
            }),

            groupCode: function () {
                return this.group ? this.group.code: '';
            }
        },
        watch: {
            groupCode: function () {
                this.newGroupCode = this.groupCode;
            }
        },
        methods: {
            updateGroup () {
                if (this.newGroupCode === '')
                {
                    this.updateGroupResult = 'Code not specified.';
                    return;
                }

                this.updateGroupResult = '';
                this.isUpdatingGroup = true;
                this.$store.dispatch('importGroup', this.newGroupCode)
                    .then(() => {
                        this.updateGroupResult = 'Group updated';
                    })
                    .catch(e => {
                        this.updateGroupResult = 'There was an error, try again later. \n\rError text:\n\r' + e;
                    })
                    .finally(() => this.isUpdatingGroup = false);
            },

            updateGames () {
                this.updateGamesResult = '';
                this.isUpdatingGames = true;
                this.$store.dispatch('importGames')
                    .then(() => {
                        this.updateGamesResult = 'Games updated.';
                    })
                    .catch(e => {
                        this.updateGamesResult = 'There was an error updating games, try again later. \n\rError text:\n\r' + e;
                    })
                    .finally(() => this.isUpdatingGames = false);
            }
        },
        created() {
            if (this.groupCode)
                this.newGroupCode = this.groupCode;
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .tools{
        
        &__section{
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid @color-cobalt;

            .dark-mode &{
                border-bottom-color: @color-cobalt-light;
            }
        }

        &__section-name{
            color: @color-cobalt;
            font-size: 18px;
            margin-bottom: 12px;
            
            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__process{
            display: flex;
            align-items: flex-start;
        }

        &__result{
            border-left: 3px solid @color-cobalt;
            padding: 4px 10px;
            margin: 10px 0;
            white-space: pre-wrap;

            .dark-mode &{
                border-left-color: @color-cobalt-light;
            }
        }
    }

</style>