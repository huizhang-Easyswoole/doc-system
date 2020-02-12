<!doctype html>
<html lang="{$lan}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/Css/document.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/highlight.js/9.18.1/styles/zenburn.min.css">
    <link rel="stylesheet" href="/Css/markdown.css">
    <script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/highlight.js/9.18.1/highlight.min.js"></script>
    <script src="/Js/global.js"></script>
    <script src="/Js/jquery.mark.min.js"></script>
    {$header}
</head>
<body>
<div class="container">
    <header class="navBar">
        <div class="navInner">
            <img src="/Images/docNavLogo.png" alt="">
            <div class="navInnerRight">
                <div class="navSearch">
                    <input aria-label="Search" autocomplete="off" spellcheck="false" class="" placeholder="">
                </div>
                <div class="navItem">
                    <div class="dropdown-wrapper">
                        <button type="button" aria-label="Select language" class="dropdown-title">
                            <span class="title">Language</span> <span class="arrow right"></span>
                        </button>
                        <ul class="nav-dropdown" style="display: none;">
                            <li class="dropdown-item"><!----> <a href="/" class="nav-link router-link-exact-active router-link-active">简体中文</a></li>
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

    })
</script>
</body>
</html>
