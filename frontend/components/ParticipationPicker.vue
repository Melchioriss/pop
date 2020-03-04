<template>
    <div class="picker">
        <div
            v-if="userId"
            :class="['user-tile', {'user-tile--mine': isMe}]"
        >
            <div class="user-tile__pic-block">
                <img
                    :src="pickerUser.avatar"
                    class="user-tile__pic"
                    :alt="pickerUser.profileName"
                />
            </div>
            <div class="user-tile__info">
                <a
                    :href="pickerUser.profileUrl"
                    target="_blank"
                    class="user-tile__name"
                >{{pickerUser.profileName}}</a>
            </div>
        </div>
        <div class="picker__edit">
            <span
                v-if="isAdmin && !isChangingPicker"
                @click="startChangingPicker"
                class="edit-link"
            >change picker</span>
            <select
                v-if="isChangingPicker"
                class="input"
                :value="pickerUser ? pickerUser.steamId : null"
                @change="saveNewPicker"
            >
                <option
                    v-for="user in users"
                    :value="user.steamId"
                >{{user.profileName}}</option>
            </select>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "ParticipationPicker",
        props: {
            userId: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                isChangingPicker: false
            };
        },
        computed: {
            ...mapGetters({
                users: 'getSortedUsers',
                isAdmin: 'loggedUserIsAdmin',
                loggedUserId: 'loggedUserSteamId'
            }),

            pickerUser: function () {
                return this.$store.getters.getUser(this.userId);
            },

            isMe: function () {
                return this.userId === this.loggedUserId;
            }
        },
        watch: {
            userId: function () {
                this.endChangingPicker();
            }
        },
        methods: {
            startChangingPicker() {
                this.isChangingPicker = true;
            },

            endChangingPicker() {
                this.isChangingPicker = false;
            },

            saveNewPicker($event) {
                if (!this.isAdmin)
                    return;

                this.$emit('change-picker', $event.target.value);
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";
    @import "../assets/user-tile";

    .picker{

        &__edit{
            padding: 5px 10px 5px 0;
        }
    }

</style>