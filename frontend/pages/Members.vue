<template>
    <div class="members">
        <h1 class="title">Member List</h1>
        <loading-indicator v-if="isLoading" />
        <div
            v-else
            class="members"
        >
            <member-item
                v-for="(member, key) in members"
                :key="'member'+key"
                :user="member"
                :class="['members__item', {'members__item--mine': memberIsMe(member)}]"
            />
        </div>

    </div>
</template>

<script>
    import MemberItem from "../components/MemberItem";
    import LoadingIndicator from "../components/LoadingIndicator";
    export default {
        name: "Members.vue",
        components: {LoadingIndicator, MemberItem},
        props: {},
        data() {
            return {
                isLoading: false
            };
        },
        computed: {
            loggedUserId: function () {
                return this.$store.getters.loggedUserSteamId;
            },

            members: function () {
                return this.$store.getters.getSortedUsers;
            }
        },
        methods: {
            memberIsMe: function (member) {
                return member.steamId === this.loggedUserId;
            }
        },
        created() {
            this.isLoading = true;
            this.$store.dispatch('loadUsers')
                .finally(() => this.isLoading = false);
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .members{
        display: flex;
        flex-direction: column;

        &__item{
            order: 2;

            &--mine{
                order: 1;
            }
        }
    }

</style>