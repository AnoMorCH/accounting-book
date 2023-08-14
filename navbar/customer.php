<?php
echo "
    <header>
        <a href='#'>Санаторий</a>
    </header>
    <input type='checkbox' id='nav' hidden checked />
    <div overflow>
        <ul>
            <li><a href='{$urls["my_reviews_page"]}'>Мои отзывы</a></li>
            <li><a href='{$urls["public_reviews_page"]}'>Общие отзывы</a></li>
            <li><a href='{$urls["logout_page"]}'>Выход</a></li>
        </ul>
    </div>
";