<!doctype html>
<html lang="{$lan}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/Css/document.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/highlight.js/9.18.1/styles/zenburn.min.css">
    <link rel="stylesheet" href="/Css/markdown.css">
    <script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/highlight.js/9.18.1/highlight.min.js"></script>
    <script src="/Js/global.js"></script>
    <script src="/Js/jquery.mark.min.js"></script>
    <script src="/Js/lunr.min.js"></script>
    <script src="/Js/lunr.stemmer.support.js"></script>
    <script src="/Js/lunr.tinyseg.js"></script>
    <script src="/Js/lunr.zhcn.js"></script>
    {$header}
</head>
<body>
<div class="container">
    <header class="navBar">
        <div class="navInner">
            <img src="/Images/docNavLogo.png" alt="">
            <div class="navInnerRight">
                <div class="navSearch">
                    <input aria-label="Search" autocomplete="off" spellcheck="false" class="" placeholder=""
                           id="SearchValue">
                    <div class="resultList" id="resultList" style="display: none"></div>
                </div>
                <div class="navItem">
                    <div class="dropdown-wrapper">
                        <button type="button" aria-label="Select language" class="dropdown-title">
                            <span class="title">Language</span> <span class="arrow right"></span>
                        </button>
                        <ul class="nav-dropdown" style="display: none;">
                            <li class="dropdown-item"><!----> <a href="/"
                                                                 class="nav-link router-link-exact-active router-link-active">简体中文</a>
                            </li>
                            <li class="dropdown-item"><!----> <a href="/En/" class="nav-link">ENGLISH</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <aside class="sideBar">{$sidebar}</aside>
    <section class="mainContent">
        <div class="content markdown-body">{$content}</div>
    </section>
</div>

<script>
    $(function () {


        // 监听菜单点击事件
        $(".sideBar ul>li").on('click', function () {
            $.each($(".sideBar ul>li"), function () {
                $(this).removeClass('active')
            });
            $(this).addClass('active')
        });

        hljs.initHighlightingOnLoad();

        var articles = [];

        $.ajax({
            url: '/keywordCn.json',
            success: function (data) {
                articles = data;
            }
        });


        /**
         * 关键词查找
         * @param keyword
         */
        function searchKeyword(keyword) {
            var result = [];
            articles.forEach(function (value) {
                var score = 0;
                !value.content && (value.content = '');
                var titleCount = value.title.match(new RegExp(keyword, 'g'));
                var contentCount = value.content.match(new RegExp(keyword, 'g'));
                if ( titleCount && titleCount.length > 0 ) {
                    score += titleCount.length * 100;
                } else if ( contentCount && contentCount.length > 0 ) {
                    score += contentCount.length;
                }

                // 截取内容前后字符
                var contentDesc = '';
                if ( contentCount ) {
                    var keywordIndex = value.content.indexOf(keyword);
                    contentDesc += value.content.slice(keywordIndex - 10, keywordIndex);
                    contentDesc += "<span class='searchKeyword'>" + keyword + "</span>";
                    contentDesc += value.content.slice(keywordIndex + keyword.length, keywordIndex + 30);
                }

                if ( score > 0 ) {
                    var searchResult = {
                        score: score,
                        hitType: titleCount ? 'title' : 'content',
                        title: value.title,
                        link: value.link,
                        contentDesc: titleCount ? value.title : contentDesc + '...',
                    };

                    result.push(searchResult);
                }
            });
            // 结果排序
            result.sort(function (a, b) {
                return b.score - a.score;
            });

            // 生成目标Dom
            var searchDom = '';
            result.forEach(function (value) {
                var dom = [
                    '<div class="resultItem">',
                    '<a href="' + value.link + '" class="resultLink">',
                    '<div class="resultTitle">' + value.title + '</div>',
                    value.hitType !== 'title' ? '<div class="resultDesc">' + value.contentDesc + '</div>' : '',
                    '</a></div>'
                ];
                searchDom += dom.join('');
            });

            $('#resultList').html(searchDom).show(100);
        }

        // 事件防抖
        function debounce(func, wait) {
            let timer;
            return function () {
                let context = this; // 注意 this 指向
                let args = arguments; // arguments中存着e
                if ( timer ) clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args)
                }, wait)
            }
        }

        // 搜索输入事件
        $('#SearchValue').on('input', debounce(function (e) {
            searchKeyword($('#SearchValue').val())
        }, 300)).on('blur', function () {
            $('#resultList').hide();
        });

        // 阻止冒泡使得点击条目时不视为失去焦点
        $('#resultList').on('mousedown', function (e) {
            e.preventDefault();
        });

    })
</script>
</body>
</html>
