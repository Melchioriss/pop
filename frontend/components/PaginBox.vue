<template>
    <div class="pagin">
        <component
            v-if="currentPageNumber > 1"
            :is="linkComponentName"
            :to="getRoute(1)"
            :title="1"
            class="pagin__link"
            @click.prevent="changePageNumber(1)"
        >
            <i class="fas fa-fw fa-angle-double-left"></i>
        </component>

        <component
            v-for="i in range(minPageToShow, maxPageToShow)"
            :is="(i === currentPageNumber) ? 'span' : linkComponentName"
            :to="getRoute(i)"
            :key="'page_'+i"
            :class="(i === currentPageNumber) ? 'pagin__cur' : 'pagin__link'"
            @click.prevent="changePageNumber(i)"
        >{{i}}</component>

        <component
            v-if="currentPageNumber < maxPageNumber"
            :is="linkComponentName"
            :to="getRoute(maxPageNumber)"
            :title="maxPageNumber"
            class="pagin__link"
            @click.prevent="changePageNumber(maxPageNumber)"
        >
            <i class="fas fa-fw fa-angle-double-right"></i>
        </component>

    </div>
</template>

<script>
    export default {
        name: "PaginBox",
        props: {
            currentPageNumber: {
                type: Number,
                default: 1
            },
            maxPageNumber: {
                type: Number,
                default: 1
            },
            useRoute: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {};
        },
        computed: {
            minPageToShow: function() {
                let minPage = this.currentPageNumber - 3;

                if (minPage < 1)
                    minPage = 1;

                return minPage;
            },
            maxPageToShow: function() {
                let maxPage = this.currentPageNumber + 3;

                if (maxPage > this.maxPageNumber)
                    maxPage = this.maxPageNumber;

                return maxPage;
            },
            linkComponentName: function () {
                return this.useRoute ? 'router-link' : 'a';
            }
        },
        methods: {
            range(start, end){
                return Array(end - start + 1).fill(0).map((val, i) => i + start);
            },

            changePageNumber(pageNum){
                if (pageNum === this.currentPageNumber)
                    return;

                if (!this.useRoute)
                    this.$emit('change-page-number', pageNum);
            },

            getRoute(pageNum){
                if (!this.useRoute)
                    return null;

                let newRoute = {
                    name: this.$route.name,
                    params: this.$route.params ? {...this.$route.params} : {},
                    query: this.$route.query ? {...this.$route.query} : {}
                };

                if (pageNum === 1)
                    delete newRoute.query.page;
                else
                    newRoute.query.page = pageNum;

                return newRoute;
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/_colors";

    .pagin{
        padding: 6px 0;
        display: flex;
        justify-content: flex-end;

        &__link{
            cursor: pointer;
        }

        &__link, &__cur{
            padding: 0 5px;
        }
    }

</style>