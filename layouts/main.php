<?php

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */

\yii\apidoc\templates\getyii\assets\AssetBundle::register($this);

// Navbar hides initial content when jumping to in-page anchor
// https://github.com/twbs/bootstrap/issues/1768
$this->registerJs(<<<JS
    var shiftWindow = function () { scrollBy(0, -50) };
    if (location.hash) shiftWindow();
    window.addEventListener("hashchange", shiftWindow);
JS
,
    \yii\web\View::POS_READY
);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="language" content="en" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <title><?php if (isset($type)) {
            echo Html::encode(StringHelper::basename($type->name) . ", {$type->name} - {$this->context->pageTitle}");
        } elseif (isset($guideHeadline)) {
            echo Html::encode("$guideHeadline - {$this->context->pageTitle}");
        } else {
            echo Html::encode($this->context->pageTitle);
        }
    ?></title>
</head>
<body>

<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Yii2.0中文开发文档',
//        'brandUrl' => ($this->context->apiUrl === null && $this->context->guideUrl !== null) ? './guide-index.html' : './index.html',
        'brandUrl' => '#',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'renderInnerContainer' => false,
        'view' => $this,
    ]);
    $nav = [];

    if ($this->context->apiUrl !== null) {
        $nav[] = ['label' => '参考类', 'url' => rtrim($this->context->apiUrl, '/') . '/index.html'];
        if (!empty($this->context->extensions)) {
            $extItems = [];
            foreach ($this->context->extensions as $ext) {
                $extItems[] = [
                    'label' => $ext,
                    'url' => "./ext-{$ext}-index.html",
                ];
            }
            $nav[] = ['label' => 'Extensions', 'items' => $extItems];
        }
    }

    if ($this->context->guideUrl !== null) {
        $nav[] = ['label' => '权威指南', 'url' => '/doc-2.0/guide/README.html'];
    }
    $nav[] = ['label' => 'CookBook', 'url' => '/doc-2.0/cookbook/README.html'];

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $nav,
        'view' => $this,
        'params' => [],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right pr15'],
        'items' => [
            ['label' => '返回 GetYii 首页', 'url' => 'http://www.getyii.com']
        ],
        'view' => $this,
        'params' => [],
    ]);

?>
<div class="navbar-form navbar-left" role="search">
  <div class="form-group">
    <input id="searchbox" type="text" class="form-control" placeholder="Search">
  </div>
</div>
    <?php $this->registerJsFile('http://changyan.sohu.com/upload/changyan.js');?>
    <?php $this->registerJsFile('http://tajs.qq.com/stats?sId=51762958',['charset'=>'UTF-8']);?>
<?php
    \yii\apidoc\templates\getyii\assets\JsSearchAsset::register($this);

    // defer loading of the search index: https://developers.google.com/speed/docs/best-practices/payload?csw=1#DeferLoadingJS
    $this->registerJs(<<<JS
var element = document.createElement("script");
element.src = "./jssearch.index.js";
document.body.appendChild(element);
JS
);

    $this->registerJs(<<<JS

var searchBox = $('#searchbox');

// search when typing in search field
searchBox.on("keyup", function(event) {
    var query = $(this).val();

    if (query == '' || event.which == 27) {
        $('#search-resultbox').hide();
        return;
    } else if (event.which == 13) {
        var selectedLink = $('#search-resultbox a.selected');
        if (selectedLink.length != 0) {
            document.location = selectedLink.attr('href');
            return;
        }
    } else if (event.which == 38 || event.which == 40) {
        $('#search-resultbox').show();

        var selected = $('#search-resultbox a.selected');
        if (selected.length == 0) {
            $('#search-results').find('a').first().addClass('selected');
        } else {
            var next;
            if (event.which == 40) {
                next = selected.parent().next().find('a').first();
            } else {
                next = selected.parent().prev().find('a').first();
            }
            if (next.length != 0) {
                var resultbox = $('#search-results');
                var position = next.position();

//              TODO scrolling is buggy and jumps around
//                resultbox.scrollTop(Math.floor(position.top));
//                console.log(position.top);

                selected.removeClass('selected');
                next.addClass('selected');
            }
        }

        return;
    }
    $('#search-resultbox').show();
    $('#search-results').html('<li><span class="no-results">No results</span></li>');

    var result = jssearch.search(query);

    if (result.length > 0) {
        var i = 0;
        var resHtml = '';

        for (var key in result) {
            if (i++ > 20) {
                break;
            }
            resHtml = resHtml +
            '<li><a href="' + result[key].file.u.substr(3) +'"><span class="title">' + result[key].file.t + '</span>' +
            '<span class="description">' + result[key].file.d + '</span></a></li>';
        }
        $('#search-results').html(resHtml);
    }
});

// hide the search results on ESC
$(document).on("keyup", function(event) { if (event.which == 27) { $('#search-resultbox').hide(); } });
// hide search results on click to document
$(document).bind('click', function (e) { $('#search-resultbox').hide(); });
// except the following:
searchBox.bind('click', function(e) { e.stopPropagation(); });
$('#search-resultbox').bind('click', function(e) { e.stopPropagation(); });

var href=location.href;
var array_href = href.split("/");
var filename = array_href.slice(-1);
var type = array_href.slice(-2);
var url;
switch(type[0])
{
    case 'cookbook':
        url = 'iiYii/yii2-cookbook/edit/gh-pages/book/';
    break;
    case 'guide':
        url = 'yii2-chinesization/yii2-zh-cn/edit/master/guide-zh-CN/';
    break;
    default:
}
var name = filename[0].split(".");
$("a#edit").attr("href", "https://github.com/"+ url + name[0] + ".md");

window._config = { showScore: true };
window.changyan.api.config({
    appid: 'cys3GSKdV',
    conf: 'prod_b750ea15a46256235acb1612bcf62afd'
});
JS
);

    NavBar::end();
    ?>

    <div id="search-resultbox" style="display: none;" class="modal-content">
        <ul id="search-results">
        </ul>
    </div>
    <div class="toplink">
        <a href="" id="edit" class="h1" title="修改此文档"><span class="glyphicon glyphicon-edit"></span></a>
    </div>

    <?= $content ?>
    <div id="SOHUCS"></div>

</div>

<footer class="footer">
    <?php /* <p class="pull-left">&copy; My Company <?= date('Y') ?></p> */ ?>
    <p class="pull-right"><small>Page generated on <?= date('r') ?></small></p>
    <?= Yii::powered() ?>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
