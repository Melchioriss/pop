<template>
    <div>
        <h1 class="title">Text Formatting Help</h1>

        <div class="text">
            <p>This site uses Markdown for text formatting. The most important formatting tips are located on this page, but you can get more information on the official Markdown <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">website</a>.</p>

            <h2 class="heading heading--h2">Headings</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td>
                    <pre>{{headersExample1}}</pre>
                    </td>
                    <td v-html="$getMarkedContent(headersExample1)"></td>
                </tr>
                <tr>
                    <td>
                        <p>Alternative syntax</p>
                        <pre>{{headersExample2}}</pre>
                    </td>
                    <td v-html="$getMarkedContent(headersExample2)"></td>
                </tr>
            </table>


            <h2 class="heading heading--h2">Text emphasis</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td>
                        <pre>{{textEmphasisExample}}</pre>
                    </td>
                    <td v-html="$getMarkedContent(textEmphasisExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Blockquotes</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{quoteExample}}</pre></td>
                    <td v-html="$getMarkedContent(quoteExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Lists</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td>
                        <p>Ordered list:</p>
                        <pre>{{listExample1}}</pre>
                        <p>Unordered list:</p>
                        <pre>{{listExample2}}</pre>
                        <p>Lists can be nested:</p>
                        <pre>{{listExample3}}</pre>
                    </td>
                    <td>
                        <p>Ordered list:</p>
                        <div v-html="$getMarkedContent(listExample1)"></div>
                        <p>Unordered list:</p>
                        <div v-html="$getMarkedContent(listExample2)"></div>
                        <p>Lists can be nested:</p>
                        <div v-html="$getMarkedContent(listExample3)"></div>
                    </td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Code</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{codeExample}}</pre></td>
                    <td v-html="$getMarkedContent(codeExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Links</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{linkExample}}</pre></td>
                    <td v-html="$getMarkedContent(linkExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Images</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{imageExample}}</pre></td>
                    <td v-html="$getMarkedContent(imageExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Horizontal Rules</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{hrExample}}</pre></td>
                    <td v-html="$getMarkedContent(hrExample)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Tables</h2>
            <table>
                <tr>
                    <th>Markdown</th>
                    <th>Output</th>
                </tr>
                <tr>
                    <td><pre>{{tableExample1}}</pre></td>
                    <td v-html="$getMarkedContent(tableExample1)"></td>
                </tr>
                <tr>
                    <td><pre>{{tableExample2}}</pre></td>
                    <td v-html="$getMarkedContent(tableExample2)"></td>
                </tr>
            </table>

            <h2 class="heading heading--h2">Escaping Characters</h2>
            <p>To display a literal character that would otherwise be used to format text in a Markdown document, add a backslash (\) in front of the character.</p>
        </div>

    </div>
</template>

<script>
    export default {
        name: "TextFormatting",
        props: {},
        data() {
            return {
                headersExample1: "# Heading 1\n" +
                    "## Heading 2\n" +
                    "### Heading 3\n" +
                    "#### Heading 4\n" +
                    "##### Heading 5\n" +
                    "###### Heading 6",
                headersExample2: "Heading 1\n" +
                    "====\n" +
                    "Heading 2\n" +
                    "----",
                textEmphasisExample: "**bold text**\n" +
                    "__bold text__\n" +
                    "*italic text*\n" +
                    "_italic text_\n" +
                    "***bold and italic text***\n" +
                    "___bold and italic text___\n" +
                    "~Strikethrough text~",
                quoteExample: "> An example of a quote",
                listExample1: "1. First\n" +
                    "2. Second\n" +
                    "3. Third",
                listExample2: "* First\n" +
                    "* Second\n" +
                    "* Third",
                listExample3: "1. First\n" +
                    "2. Second\n" +
                    "* next level\n" +
                    "* one more item\n" +
                    "3. Third",
                codeExample: "Code can be `inline`\n" +
                    "\n" +
                    "Code can be multiline:\n" +
                    "```\n" +
                    "<sc" + "ript>\n" +
                    "    alert( 'Hello, world!' );\n" +
                    "</scr" + "ipt>\n"+
                    "```",
                linkExample: "[Link example](https://store.steampowered.com/)",
                imageExample: "![image](https://steamcommunity-a.akamaihd.net/public/images/badges/01_community/communityleader_80.png)",
                hrExample: "Text before\n" +
                    "\n" +
                    "---\n" +
                    "\n" +
                    "Text after",
                tableExample1: "Heading 1 | Heading 2\n" +
                    "- | -\n" +
                    "Cell 1 | Cell 2\n" +
                    "Cell 3 | Cell 4",
                tableExample2: "| A table | with | alignment\n" +
                    ":- |    :-:   | -:\n" +
                    "Left | Center | Right\n" +
                    "Example | Text | End"
            };
        },
        methods: {}
    }
</script>

<style lang="less">
    @import "../assets/_colors";

</style>