<template>
    <div class="members">
        <h1 class="title">Member List</h1>
        <loading-indicator v-if="isLoading" />
        <template v-else>
            <member-item
                v-for="(member, key) in members"
                :key="'member'+key"
                :user="member"
            />
        </template>

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
            members: function () {
                return this.$store.getters.getSortedUsers;
            }
        },
        methods: {
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

</style>