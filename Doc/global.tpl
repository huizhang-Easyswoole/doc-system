<html lang="{$lan}">
<head>
<link href="https://cdn.staticfile.org/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/Css/doc.css">
<link rel="stylesheet" type="text/css" href="/Css/markdown.css">
<link rel="stylesheet" type="text/css" href="/Css/prettify.css">
<link rel="stylesheet" type="text/css" href="/Css/prism.css">
<script rel="script" src="/Js/global.js"></script>
<script rel="script" src="/Js/prettify.js"></script>
<script rel="script" src="/Js/prism.js"></script>
<script rel="script" src="/Js/jquery.mark.min.js"></script>
{$header}
</head>
<body>
    <!-- navBar -->
    <div class="navBar">
        <img src="/Images/docNavLogo.png" class="navBarLogo">
        {$nav}
    </div>
    <!-- sideBar -->
    <div class="sideBar">
        {$sidebar}
    </div>
    <!-- content -->
    <div class="mainBody">
        <div class="mainBodyContent">
            {$content}
        </div>
    </div>
    <!-- footer -->
    <div class="footer">
        {$footer}
    </div>
</body>
</html>
