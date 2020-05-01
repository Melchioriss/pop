<template>
    <div class="member">
        <div class="member__img-block">
            <img
                :src="user.avatar"
                :alt="user.profileName"
                class="member__img"
            />
        </div>
        <div class="member__info-block">
            <div class="member__name">{{user.profileName}}</div>

            <div class="member__steam-info">
                <div class="member__line">
                    <a
                        :href="user.profileUrl"
                        target="_blank"
                    >
                        <i class="fa-icon fab fa-fw fa-steam-square"></i>Steam
                    </a>
                </div>
                <div class="member__line">
                    <a
                        :href="'http://steamgifts.com/go/user/'+user.steamId"
                        target="_blank"
                    >
                        <i class="fa-icon far fa-fw fa-square"></i>Steamgifts
                    </a>
                </div>
                <div class="member__line">
                    <div class="member__activity">
                        <input
                            :checked="user.active"
                            :id="'member_active_'+user.steamId"
                            :disabled="!canEdit"
                            @click.prevent="saveActivity"
                            type="checkbox"
                            class="checkbox"
                        />
                        <label
                            :for="'member_active_'+user.steamId"
                        >Active</label>
                    </div>
                </div>
                <div class="member__line">
                    <span v-if="user.admin && !loggedUserIsAdmin">
                        <i class="fa-icon fas fa-fw fa-user-tag"></i>Admin
                    </span>

                    <template v-if="loggedUserIsAdmin">
                        <input
                            :checked="user.admin"
                            :id="'member_admin_'+user.steamId"
                            :disabled="!loggedUserIsAdmin"
                            @click.prevent="saveAdminRole"
                            type="checkbox"
                            class="checkbox"
                        />
                        <label
                            :for="'member_admin_'+user.steamId"
                        >Admin</label>
                    </template>
                </div>
            </div>

            <div class="member__blaeo">
                <div class="member__line">
                    <div
                        v-if="isEditingBlaeoLink"
                        class="member__edit-block member__edit-block--one-line"
                    >
                        <input
                            v-model="newBlaeoLink"
                            type="text"
                            class="input"
                            :placeholder="'BLAEO link for '+user.profileName"
                        />
                        <button
                            @click="saveBlaeoLink"
                            type="button"
                            class="button button--space-left"
                        >save</button>
                        <button
                            @click="cancelEditingBlaeoLink"
                            type="button"
                            class="button button--space-left"
                        >cancel</button>
                    </div>
                    <template v-if="canEdit && !isEditingBlaeoLink">
                        <a
                            v-if="userBlaeoLink"
                            :href="userBlaeoLink"
                            target="_blank"
                        ><i class="fa-icon fas fa-fw fa-bold"></i>BLAEO</a>
                        <span
                            @click="startEditingBlaeoLink"
                            class="edit-link"
                        >{{userBlaeoLink ? 'edit link' : 'add BLAEO link' }}</span>
                    </template>

                </div>
            </div>

            <div class="member__rules-block">
                <div class="member__rules-title" v-if="user.extraRules || canEdit">
                    <span>Extra rules for picking (Eg: only SG wins, no EA, no MP, etc):</span>
                    <span
                        v-if="canEdit"
                        @click="startEditingRules"
                        class="edit-link"
                    >{{user.extraRules ? 'edit' : 'add'}}</span>
                </div>

                <div
                    v-if="isEditingRules"
                    class="member__edit-block"
                >
                    <textarea
                        v-model="newRules"
                        class="input input--textarea input--space-bottom"
                        placeholder="Extra rules for picking"
                        rows="5"
                    >{{newRules}}</textarea>
                    <button
                        @click="saveExtraRules"
                        type="button"
                        class="button button--space-right"
                    >Save</button>
                    <button
                        @click="cancelEditingRules"
                        type="button"
                        class="button button--space-right"
                    >Cancel</button>
                </div>
                <template v-if="user.extraRules && !isEditingRules">
                    <div
                        v-html="$getMarkedContent(user.extraRules)"
                        class="member__rules text"
                    ></div>
                </template>

            </div>

        </div>


    </div>
</template>

<script>
    import {mapState, mapGetters} from 'vuex';

    export default {
        name: "MemberItem",
        props: {
            user: {
                type: Object,
                default: () => {
                    return {
                        id: 0,
                        active: true,
                        steamId: '',
                        avatar: '',
                        profileName: '',
                        profileUrl: '',
                        blaeoName: '',
                        admin: false,
                        extraRules: ''
                    }
                }
            }
        },
        data() {
            return {
                isEditingBlaeoLink: false,
                isEditingRules: false,
                newBlaeoLink: '',
                newRules: ''
            };
        },
        computed: {
            ...mapState([
                'BLAEO_USER_BASE_LINK',
            ]),

            ...mapGetters([
                'loggedUserSteamId',
                'loggedUserIsAdmin'
            ]),

            userBlaeoLink: function () {
                return this.user.blaeoName ? this.BLAEO_USER_BASE_LINK + this.user.blaeoName : '';
            },

            canEdit: function () {
                return this.loggedUserIsAdmin || (this.loggedUserSteamId === this.user.steamId);
            }
        },
        methods: {
            startEditingBlaeoLink() {
                this.newBlaeoLink = this.userBlaeoLink;
                this.isEditingBlaeoLink = true;
            },

            cancelEditingBlaeoLink() {
                this.isEditingBlaeoLink = false;
            },

            startEditingRules() {
                this.newRules = this.user.extraRules;
                this.isEditingRules = true;
            },

            cancelEditingRules() {
                this.isEditingRules = false;
            },

            saveBlaeoLink() {
                if (!this.canEdit)
                    return;

                this.$store.dispatch('updateUserBlaeoLink', {user: this.user, blaeoLink: this.newBlaeoLink})
                    .then(() => {
                        this.cancelEditingBlaeoLink();
                    });
            },

            saveExtraRules() {
                if (!this.canEdit)
                    return;

                this.$store.dispatch('updateUserExtraRules', {user: this.user, extraRules: this.newRules})
                    .then(() => {
                        this.cancelEditingRules();
                    });
            },

            saveActivity(event) {
                if (!this.canEdit)
                    return false;

                let activity = event.target.checked;

                let actionName = activity ? 'activateUser' : 'deactivateUser';

                this.$store.dispatch(actionName, this.user);
            },

            saveAdminRole(event) {
                if (!this.loggedUserIsAdmin)
                    return;

                let grantRole = event.target.checked;

                let actionName = grantRole ? 'grantUserAdminRole' : 'revokeUserAdminRole';
                this.$store.dispatch(actionName, this.user);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .member{
        display: flex;
        border: 2px solid @color-cobalt;
        padding: 15px;
        margin-bottom: 20px;

        .dark-mode &{
            border-color: @color-cobalt-light;
        }

        &__img-block{
            width: 80px;
            margin-right: 20px;
            flex-shrink: 0;
        }

        &__img{
            width: 100%;
            display: block;
            margin-top: 5px;
            border: 1px solid @color-cobalt;

            .dark-mode &{
                border-color: @color-cobalt-light;
            }
        }

        &__info-block{
            flex-grow: 1;
        }

        &__steam-info{
            display: flex;
            align-items: baseline;
            margin-bottom: 14px;
        }
        
        &__name{
            width: 100%;
            font-size: 20px;
            color: @color-cobalt;
            margin-bottom: 10px;

            .dark-mode &{
                color: @color-cobalt-light;
                font-weight: bold;
            }
        }

        &__line{
            margin-right: 60px;
        }

        &__blaeo{
            margin-bottom: 14px;
        }

        &__edit-block{
            width: 700px;
            display: flex;
            flex-wrap: wrap;

            &--one-line{
                flex-wrap: nowrap;
                align-items: stretch;
            }
        }

        &__rules-block{
            flex-grow: 1;
            min-width: 200px;
        }

        &__rules-title{
            color: @color-cobalt;
            font-size: 14px;
            margin-bottom: 4px;

            .dark-mode &{
                color: @color-cobalt-light;
            }
        }

        &__rules{
            border-left: 2px solid @color-cobalt;
            padding: 2px 0 2px 10px;
            font-size: 14px;

            & > p:last-child{
                margin-bottom: 0;
            }

            & img{
                max-height: 100px;
            }

            .dark-mode &{
                border-left-color: @color-cobalt-light;
            }
        }

    }

</style>