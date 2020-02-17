<template>
    <div class="steam-login">
        <template v-if="user && user.steamId">
            <img
                :src="user.avatar"
                class="steam-login__pic"
            />
            <div class="steam-login__name">{{user.profileName}}</div>
            <a
                @click.prevent="logout"
                class="steam-login__logout"
            ><i class="fa-icon fas fa-sign-out-alt"></i></a>
        </template>

        <form
            v-else
            method="post"
            action="https://steamcommunity.com/openid/login"
        >
            <input type="hidden" name="openid.ns" value="http://specs.openid.net/auth/2.0">
            <input type="hidden" name="openid.mode" value="checkid_setup">
            <input type="hidden" name="openid.identity" value="http://specs.openid.net/auth/2.0/identifier_select">
            <input type="hidden" name="openid.claimed_id" value="http://specs.openid.net/auth/2.0/identifier_select">
            <input type="hidden" name="openid.return_to" :value="returnTo">
            <input type="hidden" name="openid.realm" :value="returnTo">
            <button
                class="steam-login__button"
                type="submit"
            ><img src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png" alt=""/></button>
        </form>
    </div>
</template>

<script>
    import api from '../api/index';
    export default {
        name: "SteamLogin",
        props: {},
        data() {
            return {};
        },
        computed: {
            user: function () {
                return this.$store.state.loggedUser;
            },
            returnTo: () => global.location.origin + '/api/healthz'
        },
        methods: {
            logout: function () {
                api.profile.logout()
                    .then(() => {
                        location.reload();
                    });
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .steam-login{
        display: flex;
        justify-content: center;
        align-items: center;

        &__button{
            display: block;
            border: none;
            background: none;
            padding: 0;
            margin: 0;
            cursor: pointer;

            & > img{
                display: block;
            }
        }

        &__pic{
            width: 34px;
            height: 34px;
            margin-right: 8px;
            border: 1px solid @color-bg;
        }

        &__name{
            color: @color-bg;
        }

        &__logout{
            font-size: 18px;
            color: @color-light-orange;
            margin-left: 8px;
            cursor: pointer;

            &:hover{
                color: @color-light-orange-hover;
            }
        }
    }

</style>