<template>
    <div>
        <div
            v-if="isEditing"
            class="form"
        >
            <div class="form__block">
                <div class="form__label">
                    Content for main page
                    <span class="form__note">
                        (<router-link :to="{name: 'text_formatting'}" target="_blank">Markdown</router-link> supported)
                    </span>
                </div>
                <textarea
                    v-model="newContent"
                    class="input input--textarea"
                    rows="8"
                ></textarea>
            </div>
            <div class="form__block">
                <button
                    @click="saveContent"
                    type="button"
                    class="button button--space-right"
                >save</button>
                <button
                    @click="endEditingContent"
                    type="button"
                    class="button button--space-right"
                >cancel</button>
            </div>
        </div>
        <div
            v-if="!isEditing && content"
            class="text" v-html="$getMarkedContent(content)"
        ></div>
        <span
            v-if="isAdmin && !isEditing"
            @click="startEditingContent"
            class="edit-link"
        >edit main page content</span>

    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    export default {
        name: "Index",
        data: function () {
            return {
                newContent: '',
                isEditing: false
            };
        },
        computed: {
            ...mapGetters({
                isAdmin: 'loggedUserIsAdmin',
                content: 'getMainPageContent'
            })
        },
        methods: {
            startEditingContent: function () {
                this.newContent = this.content;
                this.isEditing = true;
            },
            endEditingContent: function () {
                this.isEditing = false;
            },
            saveContent: function () {
                this.$store.dispatch('setMainPageContent', this.newContent)
                    .then(() => {
                        this.endEditingContent();
                    });
            }
        },
        created () {
            this.$store.dispatch('loadMainPageContent');
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

</style>